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

    /**
     * Tipe field tambahan (dinamis) yang tersedia beserta labelnya.
     * Tipe "select" memakai daftar pilihan yang diisi di menu Jenis Surat,
     * dan bisa dipakai untuk menampilkan/menyembunyikan blok pada template
     * (lihat SuratGeneratorService::terapkanBlok()).
     */
    public const FIELD_TYPE_LABELS = [
        'text' => 'Teks',
        'textarea' => 'Teks Panjang',
        'date' => 'Tanggal',
        'number' => 'Angka',
        'select' => 'Pilihan (dropdown)',
        'anak_kk' => 'Anak (satu KK)',
    ];

    public const FIELD_TYPES = [
        'text', 'textarea', 'date', 'number', 'select', 'anak_kk',
    ];

    protected $fillable = [
        'nama_surat', 'slug', 'kode_surat', 'kode_klasifikasi', 'deskripsi',
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
            'options' => self::parseOptions($f['options'] ?? null),
        ])->all();
    }

    /**
     * Pilihan untuk field bertipe "select". Disimpan sebagai teks dipisah baris
     * baru atau koma (mis. "Ya\nTidak"), dikembalikan sebagai array bersih.
     *
     * @return array<int, string>
     */
    public static function parseOptions(mixed $raw): array
    {
        if (is_array($raw)) {
            $items = $raw;
        } elseif (is_string($raw) && filled($raw)) {
            $items = preg_split('/[\r\n,]+/', $raw) ?: [];
        } else {
            return [];
        }

        return collect($items)
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Rapikan input definisi field dari form menjadi skema yang disimpan di
     * kolom JSON `fields`. Dipakai bersama oleh Store & Update request.
     *
     * Nama field (yang menjadi placeholder ${...} di template) diambil dari
     * input tersembunyi bila ada, supaya nama field yang sudah dipakai di
     * template tidak ikut berubah ketika labelnya disunting.
     */
    public static function normalizeFieldSchema(array $rows): array
    {
        return collect($rows)
            ->filter(fn ($f) => filled($f['label'] ?? null))
            ->map(function ($f) {
                $type = $f['type'] ?? 'text';
                $type = in_array($type, self::FIELD_TYPES, true) ? $type : 'text';

                $nama = filled($f['name'] ?? null)
                    ? Str::slug($f['name'], '_')
                    : Str::slug($f['label'], '_');

                $field = [
                    'name' => $nama,
                    'label' => trim($f['label']),
                    'type' => $type,
                    'required' => (bool) ($f['required'] ?? false),
                ];

                if ($type === 'select') {
                    $field['options'] = self::parseOptions($f['options'] ?? null) ?: ['Ya', 'Tidak'];
                }

                return $field;
            })
            ->values()
            ->all();
    }
}
