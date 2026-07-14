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

    public function surats(): HasMany
    {
        return $this->hasMany(Surat::class);
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nik} - {$this->nama_lengkap}";
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
        foreach (['dusun', 'rt', 'rw', 'jenis_kelamin', 'agama'] as $key) {
            $query->when($filters[$key] ?? null, fn (Builder $q, $value) => $q->where($key, $value));
        }

        return $query;
    }
}
