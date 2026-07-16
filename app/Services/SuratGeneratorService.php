<?php
namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

class SuratGeneratorService
{
    private const OUTPUT_DIR = 'surat';

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

        foreach ($this->buildValues($surat) as $key => $value) {

            $processor->setValue($key, $this->toWordValue((string) $value));
        }

        $filename = Str::slug($jenis->kode_surat).'-'.$surat->id.'-'.now()->format('YmdHis').'.docx';
        $relativePath = self::OUTPUT_DIR.'/'.$filename;

        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $processor->saveAs(Storage::disk('public')->path($relativePath));

        return $relativePath;
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
