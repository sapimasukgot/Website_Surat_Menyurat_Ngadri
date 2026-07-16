<?php
namespace App\Services;

use App\Models\JenisSurat;
use App\Models\Setting;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Menerapkan logo kabupaten & desa (dari Pengaturan) ke kop surat
 * seluruh template Word (.docx).
 *
 * Struktur kop yang dikenali: tabel pertama yang memuat teks "PEMERINTAH",
 * sel kiri berisi logo kabupaten, sel kanan untuk logo desa. Jika sel kanan
 * belum memiliki gambar, logo desa disisipkan dengan meniru ukuran logo kiri.
 */
class TemplateLogoService
{
    private const NS_W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const NS_A = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    private const NS_R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const NS_WP = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
    private const NS_REL = 'http://schemas.openxmlformats.org/package/2006/relationships';
    private const NS_CT = 'http://schemas.openxmlformats.org/package/2006/content-types';
    private const REL_IMAGE = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image';

    /**
     * Terapkan logo ke semua template jenis surat.
     *
     * @return array{updated: int, skipped: array<string>}
     */
    public function applyToAllTemplates(): array
    {
        $updated = 0;
        $skipped = [];

        foreach (JenisSurat::withTrashed()->whereNotNull('template_path')->get() as $jenis) {
            if (! $jenis->hasTemplate()) {
                continue;
            }

            try {
                $this->applyToTemplate(Storage::disk('public')->path($jenis->template_path))
                    ? $updated++
                    : $skipped[] = $jenis->kode_surat;
            } catch (\Throwable $e) {
                $skipped[] = $jenis->kode_surat.' ('.$e->getMessage().')';
            }
        }

        return ['updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * Terapkan logo (yang tersimpan di Pengaturan) ke satu berkas template.
     * Mengembalikan true bila ada perubahan yang disimpan.
     */
    public function applyToTemplate(string $absolutePath): bool
    {
        $logoKabupaten = $this->logoBytes('logo_kabupaten');
        $logoDesa = $this->logoBytes('logo_desa');

        if ($logoKabupaten === null && $logoDesa === null) {
            return false;
        }

        $zip = new ZipArchive();

        if ($zip->open($absolutePath) !== true) {
            throw new \RuntimeException('Berkas template tidak dapat dibuka.');
        }

        try {
            $documentXml = $zip->getFromName('word/document.xml');
            $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
            $ctXml = $zip->getFromName('[Content_Types].xml');

            if ($documentXml === false || $relsXml === false || $ctXml === false) {
                return false;
            }

            $doc = $this->loadXml($documentXml);
            $rels = $this->loadXml($relsXml);
            $ct = $this->loadXml($ctXml);

            $xp = new DOMXPath($doc);
            $xp->registerNamespace('w', self::NS_W);
            $xp->registerNamespace('a', self::NS_A);
            $xp->registerNamespace('wp', self::NS_WP);

            [$leftCell, $rightCell] = $this->findKopCells($xp);

            if (! $leftCell) {
                return false;
            }

            $changed = false;

            if ($logoKabupaten !== null) {
                $changed = $this->swapCellImage($zip, $rels, $ct, $xp, $leftCell, $logoKabupaten, 'logo_kabupaten') || $changed;
            }

            if ($logoDesa !== null && $rightCell) {
                $swapped = $this->swapCellImage($zip, $rels, $ct, $xp, $rightCell, $logoDesa, 'logo_desa');

                if (! $swapped) {
                    $swapped = $this->insertCellImage($zip, $rels, $ct, $xp, $leftCell, $rightCell, $logoDesa, 'logo_desa');
                }

                $changed = $swapped || $changed;
            }

            if ($changed) {
                $zip->addFromString('word/document.xml', $doc->saveXML());
                $zip->addFromString('word/_rels/document.xml.rels', $rels->saveXML());
                $zip->addFromString('[Content_Types].xml', $ct->saveXML());
            }

            return $changed;
        } finally {
            $zip->close();
        }
    }

    private function logoBytes(string $settingKey): ?string
    {
        $path = Setting::get($settingKey);

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->get($path);
    }

    private function loadXml(string $xml): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;

        if (! $dom->loadXML($xml)) {
            throw new \RuntimeException('Struktur XML template tidak valid.');
        }

        return $dom;
    }

    /**
     * @return array{0: ?DOMElement, 1: ?DOMElement} [sel kiri, sel kanan] baris pertama tabel kop
     */
    private function findKopCells(DOMXPath $xp): array
    {
        foreach ($xp->query('//w:tbl') as $tbl) {
            if (! str_contains($tbl->textContent, 'PEMERINTAH')) {
                continue;
            }

            $cells = $xp->query('./w:tr[1]/w:tc', $tbl);

            if ($cells->length >= 2) {
                return [$cells->item(0), $cells->item($cells->length - 1)];
            }

            return [null, null];
        }

        return [null, null];
    }

    /**
     * Ganti gambar yang sudah ada di dalam sel dengan logo baru.
     * Mengembalikan false bila sel tidak memiliki gambar.
     */
    private function swapCellImage(ZipArchive $zip, DOMDocument $rels, DOMDocument $ct, DOMXPath $xp, DOMElement $cell, string $bytes, string $name): bool
    {
        $blip = $xp->query('.//a:blip', $cell)->item(0);

        if (! $blip instanceof DOMElement) {
            return false;
        }

        $rid = $this->addImage($zip, $rels, $ct, $bytes, $name);
        $blip->setAttributeNS(self::NS_R, 'r:embed', $rid);

        return true;
    }

    /**
     * Sisipkan logo ke sel kanan dengan meniru drawing (ukuran) milik sel kiri.
     */
    private function insertCellImage(ZipArchive $zip, DOMDocument $rels, DOMDocument $ct, DOMXPath $xp, DOMElement $leftCell, DOMElement $rightCell, string $bytes, string $name): bool
    {
        $sourceRun = $xp->query('.//w:r[.//a:blip]', $leftCell)->item(0);

        if (! $sourceRun instanceof DOMElement) {
            return false;
        }

        $rid = $this->addImage($zip, $rels, $ct, $bytes, $name);

        /** @var DOMElement $run */
        $run = $sourceRun->cloneNode(true);

        $blip = $xp->query('.//a:blip', $run)->item(0);
        $blip->setAttributeNS(self::NS_R, 'r:embed', $rid);

        // ID & nama docPr harus unik dalam dokumen.
        foreach ($xp->query('.//wp:docPr', $run) as $docPr) {
            $docPr->setAttribute('id', (string) random_int(9000, 99999));
            $docPr->setAttribute('name', 'Logo Desa');
        }

        $paragraph = $xp->query('./w:p', $rightCell)->item(0);

        if ($paragraph instanceof DOMElement) {
            $paragraph->appendChild($run);
        } else {
            $p = $rightCell->ownerDocument->createElementNS(self::NS_W, 'w:p');
            $p->appendChild($run);
            $rightCell->appendChild($p);
        }

        return true;
    }

    /**
     * Daftarkan berkas gambar baru ke dalam paket docx dan kembalikan relationship ID-nya.
     */
    private function addImage(ZipArchive $zip, DOMDocument $rels, DOMDocument $ct, string $bytes, string $name): string
    {
        $filename = $name.'_'.substr(md5($bytes), 0, 8).'.png';
        $zip->addFromString('word/media/'.$filename, $bytes);

        $this->ensurePngContentType($ct);

        $root = $rels->documentElement;
        $rid = $this->uniqueRelId($rels);

        $rel = $rels->createElementNS(self::NS_REL, 'Relationship');
        $rel->setAttribute('Id', $rid);
        $rel->setAttribute('Type', self::REL_IMAGE);
        $rel->setAttribute('Target', 'media/'.$filename);
        $root->appendChild($rel);

        return $rid;
    }

    private function ensurePngContentType(DOMDocument $ct): void
    {
        $xp = new DOMXPath($ct);
        $xp->registerNamespace('ct', self::NS_CT);

        if ($xp->query('//ct:Default[@Extension="png"]')->length > 0) {
            return;
        }

        $default = $ct->createElementNS(self::NS_CT, 'Default');
        $default->setAttribute('Extension', 'png');
        $default->setAttribute('ContentType', 'image/png');
        $ct->documentElement->appendChild($default);
    }

    private function uniqueRelId(DOMDocument $rels): string
    {
        $existing = [];

        foreach ($rels->documentElement->childNodes as $node) {
            if ($node instanceof DOMElement) {
                $existing[] = $node->getAttribute('Id');
            }
        }

        $i = 1000;

        do {
            $rid = 'rIdLogo'.$i++;
        } while (in_array($rid, $existing, true));

        return $rid;
    }
}
