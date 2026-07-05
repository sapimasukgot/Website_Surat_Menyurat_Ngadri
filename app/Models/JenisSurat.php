<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JenisSurat extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'jenis_surats';

    protected $fillable = [
        'nama_surat', 'slug', 'kode_surat', 'deskripsi',
        'template_path', 'template_original_name', 'fields', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {

        static::saving(function (JenisSurat $model) {
            if (blank($model->slug)) {
                $model->slug = Str::slug($model->nama_surat);
            }
        });
    }

    public function surats(): HasMany
    {
        return $this->hasMany(Surat::class);
    }

    public function getTemplateUrlAttribute(): ?string
    {
        return $this->template_path ? Storage::disk('public')->url($this->template_path) : null;
    }

    public function hasTemplate(): bool
    {
        return $this->template_path && Storage::disk('public')->exists($this->template_path);
    }

    public function additionalFields(): array
    {
        return collect($this->fields ?? [])->map(fn ($f) => [
            'name' => $f['name'] ?? Str::slug($f['label'] ?? '', '_'),
            'label' => $f['label'] ?? ($f['name'] ?? ''),
            'type' => $f['type'] ?? 'text',
            'required' => (bool) ($f['required'] ?? false),
        ])->all();
    }
}
