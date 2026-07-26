<?php
namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use ZipArchive;

class SuratGeneratorService
{
    private const OUTPUT_DIR = 'surat';

    private const NS_W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    private const BULAN = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public function generate(Surat $surat): string
    {
        $jenis = $surat->jenisSurat;

        if (! $jenis || ! $jenis->hasTemplate()) {
            throw new RuntimeException('Template untuk jenis surat ini belum tersedia.');
        }

        $templateAbsolute = Storage::disk('public')->path($jenis->template_path);
        $processor = new TemplateProcessor($templateAbsolute);

        $values = $this->buildValues($surat);

        // Blok kondisional diproses lebih dulu: blok yang tidak dipakai dibuang
        // beserta seluruh placeholder di dalamnya.
        $this->terapkanBlok($processor, $surat, $values);

        foreach ($values as $key => $value) {

            $processor->setValue($key, $this->toWordValue((string) $value));
        }

        $this->kosongkanSisaPlaceholder($processor);

        $filename = Str::slug($jenis->kode_surat).'-'.$surat->id.'-'.now()->format('YmdHis').'.docx';
        $relativePath = self::OUTPUT_DIR.'/'.$filename;

        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        $processor->saveAs($absolutePath);

        if (! $surat->pakai_kop) {
            try {
                $this->hapusKop($absolutePath);
            } catch (\Throwable $e) {
                // Berkas tetap dipakai walau kop gagal dihapus — operator masih
                // bisa menghapusnya manual di Word daripada surat gagal dibuat.
            }
        }

        return $relativePath;
    }

    /**
     * Kosongkan placeholder yang tidak terisi.
     *
     * Placeholder bisa tersisa bila field-nya dibiarkan kosong oleh operator,
     * atau bila nama field di menu Jenis Surat tidak sama dengan yang tertulis
     * di template. Tanpa ini, tulisan mentah seperti ${keperluan} ikut tercetak
     * di surat resmi — lebih baik dibiarkan kosong agar bisa ditulis tangan.
     */
    private function kosongkanSisaPlaceholder(TemplateProcessor $processor): void
    {
        foreach ($processor->getVariables() as $sisa) {
            try {
                $processor->setValue($sisa, '');
            } catch (\Throwable $e) {
                // Lewati placeholder yang tidak bisa diproses.
            }
        }
    }

    /**
     * Tampilkan atau buang blok kondisional pada template.
     *
     * Bagian template yang dibungkus ${nama_field} ... ${/nama_field} hanya
     * ikut tercetak bila field bertipe "select" dengan nama tersebut bernilai
     * "Ya". Contoh: blok surat pernyataan panitia hiburan pada Surat Izin
     * Keramaian. cloneBlock(..., 0) membuang blok berikut isinya.
     */
    private function terapkanBlok(TemplateProcessor $processor, Surat $surat, array $values): void
    {
        foreach ($surat->jenisSurat?->additionalFields() ?? [] as $field) {
            if ($field['type'] !== 'select') {
                continue;
            }

            $nama = $field['name'];
            $aktif = strcasecmp((string) ($values[$nama] ?? ''), 'Ya') === 0;

            try {
                $processor->cloneBlock($nama, $aktif ? 1 : 0, true, false, null);
            } catch (\Throwable $e) {
                // Template tidak memakai blok ${nama} ... ${/nama} — abaikan,
                // field tetap dipakai sebagai placeholder biasa di bawah.
            }
        }
    }

    /**
     * Hapus tabel kop surat dari berkas .docx yang sudah dibuat.
     *
     * Kop dikenali dengan cara yang sama seperti TemplateLogoService, yaitu
     * tabel pertama yang memuat teks "PEMERINTAH" (baris PEMERINTAH KABUPATEN
     * ... / KECAMATAN ... / DESA ...). Garis pemisah tepat di bawahnya (jika
     * ada) ikut dibuang agar tidak menyisakan garis menggantung.
     */
    private function hapusKop(string $absolutePath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($absolutePath) !== true) {
            return;
        }

        try {
            $documentXml = $zip->getFromName('word/document.xml');

            if ($documentXml === false) {
                return;
            }

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = true;

            // loadXML() memunculkan warning PHP pada XML rusak — dibungkus agar
            // tidak berubah menjadi ErrorException dan menggagalkan pembuatan surat.
            $sebelumnya = libxml_use_internal_errors(true);
            $terbaca = $doc->loadXML($documentXml);
            libxml_clear_errors();
            libxml_use_internal_errors($sebelumnya);

            if (! $terbaca) {
                return;
            }

            $xp = new DOMXPath($doc);
            $xp->registerNamespace('w', self::NS_W);

            $kop = null;

            foreach ($xp->query('//w:tbl') as $tbl) {
                if (str_contains($tbl->textContent, 'PEMERINTAH')) {
                    $kop = $tbl;
                    break;
                }
            }

            if (! $kop instanceof DOMElement) {
                return;
            }

            // Satu paragraf kosong (garis pemisah) tepat setelah kop ikut dibuang
            // agar isi surat tidak menyisakan ruang menggantung. Node teks dari
            // dokumen yang di-format rapi dilewati.
            $berikutnya = $kop->nextSibling;

            while ($berikutnya !== null && ! $berikutnya instanceof DOMElement) {
                $berikutnya = $berikutnya->nextSibling;
            }

            if ($berikutnya instanceof DOMElement
                && $berikutnya->localName === 'p'
                && trim($berikutnya->textContent) === '') {
                $berikutnya->parentNode->removeChild($berikutnya);
            }

            $kop->parentNode->removeChild($kop);

            $zip->addFromString('word/document.xml', $doc->saveXML());
        } finally {
            $zip->close();
        }
    }

    private function buildValues(Surat $surat): array
    {
        $data = $surat->data_surat ?? [];
        $tanggal = $surat->tanggal_surat ?? Carbon::now();

        $base = [
            'nomor_surat' => $surat->nomor_surat,
            'tanggal' => $this->tanggalIndonesia($tanggal),
            'desa' => config('desa.nama'),
            'kecamatan' => config('desa.kecamatan'),
            'kabupaten' => config('desa.kabupaten'),
            'provinsi' => config('desa.provinsi'),
            'penandatangan' => $surat->user->name ?? '',
            'jabatan_ttd' => $surat->user->jabatan ?? '',
        ];

        $values = array_map(
            fn ($v) => is_array($v) ? implode(', ', $v) : (string) $v,
            array_merge($base, $data)
        );

        // Surat yang ditandatangani Sekretaris Desa memakai template yang sama,
        // dengan tambahan "An. Kepala Desa Ngadri" di atas jabatan Sekretaris Desa.
        if (($values['penandatangan_role'] ?? '') === 'sekretaris_desa') {
            $values['jabatan_ttd'] = 'An. Kepala '.config('desa.nama')."\n".'Sekretaris Desa';
        }

        return $values;
    }

    /**
     * Amankan nilai untuk XML Word dan ubah baris baru menjadi line break.
     *
     * Catatan: <w:br/> tidak boleh berada DI DALAM <w:t> (invalid OOXML —
     * MS Word menoleransi, tetapi docx-preview tidak merendernya). Karena
     * macro selalu berada di dalam <w:t>, tag w:t harus ditutup lalu dibuka
     * kembali di sekeliling <w:br/>.
     */
    private function toWordValue(string $value): string
    {
        $lines = array_map(
            fn ($line) => htmlspecialchars($line, ENT_XML1 | ENT_QUOTES, 'UTF-8'),
            explode("\n", str_replace(["\r\n", "\r"], "\n", $value))
        );

        return implode('</w:t><w:br/><w:t xml:space="preserve">', $lines);
    }

    private function tanggalIndonesia(Carbon $t): string
    {
        return $t->day.' '.self::BULAN[$t->month].' '.$t->year;
    }
}
