<?php
namespace App\Exports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendudukExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        return Penduduk::query()->orderBy('nama_lengkap');
    }

    public function headings(): array
    {
        return [
            'NIK', 'No KK', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir',
            'Jenis Kelamin', 'Agama', 'Pendidikan', 'Pekerjaan', 'Status Kawin',
            'Alamat', 'RT', 'RW', 'Dusun', 'No HP',
        ];
    }

    public function map($p): array
    {
        return [
            "\t".$p->nik,
            "\t".$p->no_kk,
            $p->nama_lengkap,
            $p->tempat_lahir,
            $p->tanggal_lahir?->format('d-m-Y'),
            $p->jenis_kelamin,
            $p->agama,
            $p->pendidikan,
            $p->pekerjaan,
            $p->status_kawin,
            $p->alamat,
            $p->rt,
            $p->rw,
            $p->dusun,
            $p->no_hp,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
