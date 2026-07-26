<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Surat extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'surats';

    protected $fillable = [
        'nomor_surat', 'jenis_surat_id', 'penduduk_id', 'user_id',
        'tanggal_surat', 'pakai_kop', 'data_surat', 'file_path', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'pakai_kop' => 'boolean',
            'data_surat' => 'array',
        ];
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function hasFile(): bool
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }

    public function data(string $key, mixed $default = null): mixed
    {
        return data_get($this->data_surat, $key, $default);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $q) use ($term) {
            $q->where('nomor_surat', 'like', "%{$term}%")
              ->orWhereHas('penduduk', fn (Builder $p) => $p
                  ->where('nama_lengkap', 'like', "%{$term}%")
                  ->orWhere('nik', 'like', "%{$term}%"));
        });
    }
}
