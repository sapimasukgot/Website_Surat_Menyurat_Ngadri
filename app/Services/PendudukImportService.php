<?php
namespace App\Services;

use App\Imports\PendudukImport;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PendudukImportService
{

    public function handle(UploadedFile $file, User $user): ImportLog
    {
        $import = new PendudukImport();

        DB::transaction(function () use ($import, $file) {
            Excel::import($import, $file);
        });

        return ImportLog::create([
            'user_id' => $user->id,
            'context' => 'penduduk',
            'file_name' => $file->getClientOriginalName(),
            'total_rows' => $import->total,
            'inserted_count' => $import->inserted,
            'updated_count' => $import->updated,
            'failed_count' => $import->failed,
            'errors' => $import->errors,
            'status' => 'completed',
        ]);
    }
}
