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
            'No KK', 'NIK', 'Nama Lengkap', 'JK', 'Tempat Lahir', 'Tanggal Lahir',
            'Gol. Darah', 'Agama', 'Status Kawin', 'Status Hubungan',
            'Pendidikan', 'Pekerjaan', 'Nama Ibu', 'Nama Ayah',
            'Alamat', 'RT', 'RW', 'Dusun', 'No HP',
        ];
    }

    public function map($p): array
    {
        return [
            "\t".$p->no_kk,
            "\t".$p->nik,
            $p->nama_lengkap,
            $p->jenis_kelamin,
            $p->tempat_lahir,
            $p->tanggal_lahir?->format('d-m-Y'),
            $p->golongan_darah,
            $p->agama,
            $p->status_kawin,
            $p->status_hubungan,
            $p->pendidikan,
            $p->pekerjaan,
            $p->nama_ibu,
            $p->nama_ayah,
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
