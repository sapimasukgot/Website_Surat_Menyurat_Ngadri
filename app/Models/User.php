<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{

    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'jabatan',
        'photo_path',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        // asset() mengikuti host/port yang sedang diakses, sehingga tetap
        // benar walau APP_URL di .env tidak menyertakan port (mis. :8000).
        return $this->photo_path && Storage::disk('public')->exists($this->photo_path)
            ? asset('storage/'.$this->photo_path)
            : null;
    }

    public function surats(): HasMany
    {
        return $this->hasMany(Surat::class);
    }

    public function importLogs(): HasMany
    {
        return $this->hasMany(ImportLog::class);
    }
}
