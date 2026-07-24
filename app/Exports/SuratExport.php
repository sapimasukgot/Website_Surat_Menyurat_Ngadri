<?php
namespace App\Exports;

use App\Models\Surat;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Backup riwayat surat ke Excel.
 *
 * Kolom NIK/kode dipakai sebagai kunci saat restore (import) di komputer lain;
 * kolom "Data Surat (JSON)" menyimpan seluruh isi surat sehingga berkas Word
 * dapat dibuat ulang persis tanpa perlu membackup file .docx-nya.
 */
class SuratExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function query()
    {
        return Surat::query()
            ->with(['jenisSurat', 'penduduk', 'user'])
            ->search($this->filters['q'] ?? null)
            ->when($this->filters['jenis_surat_id'] ?? null, fn ($q, $v) => $q->where('jenis_surat_id', $v))
            ->when($this->filters['dari'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_surat', '>=', $v))
            ->when($this->filters['sampai'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_surat', '<=', $v))
            ->orderBy('tanggal_surat')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Nomor Surat', 'Kode Surat', 'Nama Surat', 'NIK Pemohon', 'Nama Pemohon',
            'Tanggal Surat', 'Penandatangan', 'Dibuat Oleh (Email)', 'Dibuat Pada',
            'Data Surat (JSON)',
        ];
    }

    public function map($surat): array
    {
        return [
            $surat->nomor_surat,
            $surat->jenisSurat->kode_surat ?? '',
            $surat->jenisSurat->nama_surat ?? '',
            "\t".($surat->penduduk->nik ?? ''),
            $surat->penduduk->nama_lengkap ?? '',
            $surat->tanggal_surat?->format('d-m-Y'),
            trim(($surat->data('penandatangan', '')).' ('.$surat->data('jabatan_ttd', 'Kepala Desa').')'),
            $surat->user->email ?? '',
            $surat->created_at?->format('d-m-Y H:i:s'),
            json_encode($surat->data_surat ?? [], JSON_UNESCAPED_UNICODE),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
