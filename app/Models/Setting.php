<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    public const ROLE_LABELS = [
        'kepala_desa' => 'Kepala Desa',
        'sekretaris_desa' => 'Sekretaris Desa',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            return static::query()->where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.$key");
    }

    public static function penandatangan(string $role): array
    {
        return [
            'nama' => static::get($role, ''),
            'jabatan' => static::ROLE_LABELS[$role] ?? 'Kepala Desa',
            'role' => $role,
        ];
    }
}
