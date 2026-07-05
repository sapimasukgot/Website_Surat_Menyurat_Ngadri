<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{

    use HasFactory;

    protected $table = 'import_logs';

    protected $fillable = [
        'user_id', 'file_name', 'total_rows',
        'inserted_count', 'updated_count', 'failed_count', 'errors', 'status',
    ];

    protected function casts(): array
    {
        return [
            'errors' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
