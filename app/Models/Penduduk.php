<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penduduk extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'penduduks';

    protected $fillable = [
        'nik', 'no_kk', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'golongan_darah', 'agama', 'pendidikan', 'pekerjaan',
        'status_kawin', 'status_hubungan', 'nama_ayah', 'nama_ibu',
        'alamat', 'rt', 'rw', 'dusun', 'no_hp',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public const JENIS_KELAMIN = ['L' => 'Laki-laki', 'P' => 'Perempuan'];

    public const STATUS_KAWIN = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];

    public const AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];

    public const GOLONGAN_DARAH = ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Tidak Tahu'];

    public const STATUS_HUBUNGAN = [
        'Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu',
        'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya',
    ];

    public const KELOMPOK_USIA = [
        'balita' => ['label' => 'Balita (0-5 th)', 'min' => 0, 'max' => 5],
        'anak' => ['label' => 'Anak (6-12 th)', 'min' => 6, 'max' => 12],
        'remaja' => ['label' => 'Remaja (13-17 th)', 'min' => 13, 'max' => 17],
        'dewasa' => ['label' => 'Dewasa (18-59 th)', 'min' => 18, 'max' => 59],
        'lansia' => ['label' => 'Lansia (60+ th)', 'min' => 60, 'max' => null],
    ];

    public function surats(): HasMany
    {
        return $this->hasMany(Surat::class);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nik} - {$this->nama_lengkap}";
    }

    /**
     * Anggota keluarga berstatus "Anak" yang berada dalam satu KK
     * dengan penduduk ini (no_kk sebagai kunci penghubung).
     */
    public function anakSatuKk(): \Illuminate\Support\Collection
    {
        if (blank($this->no_kk)) {
            return collect();
        }

        return static::query()
            ->where('no_kk', $this->no_kk)
            ->where('id', '!=', $this->id)
            ->where('status_hubungan', 'Anak')
            ->orderBy('tanggal_lahir')
            ->get();
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return self::JENIS_KELAMIN[$this->jenis_kelamin] ?? '-';
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $q) use ($term) {
            $q->where(function (Builder $sub) use ($term) {
                $sub->where('nik', 'like', "%{$term}%")
                    ->orWhere('no_kk', 'like', "%{$term}%")
                    ->orWhere('nama_lengkap', 'like', "%{$term}%");
            });
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ([
            'dusun', 'rt', 'rw', 'jenis_kelamin', 'agama',
            'pendidikan', 'pekerjaan', 'status_kawin', 'golongan_darah', 'status_hubungan',
        ] as $key) {
            $query->when($filters[$key] ?? null, fn (Builder $q, $value) => $q->where($key, $value));
        }

        $query->when($filters['kelompok_usia'] ?? null, function (Builder $q, $key) {
            $range = self::KELOMPOK_USIA[$key] ?? null;

            if (! $range) {
                return;
            }

            $today = now();
            $q->whereNotNull('tanggal_lahir')
                ->where('tanggal_lahir', '<=', $today->copy()->subYears($range['min'])->toDateString());

            if (! is_null($range['max'])) {
                $q->where('tanggal_lahir', '>', $today->copy()->subYears($range['max'] + 1)->toDateString());
            }
        });

        return $query;
    }
}
