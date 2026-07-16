<?php

require __DIR__ . '/vendor/autoload.php';

use App\Imports\PendudukImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Facade;

// Bootstrap Laravel Container
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $filePath = __DIR__ . '/bip_ 2019.xlsx';
    $import = new PendudukImport();

    $rows = \Maatwebsite\Excel\Facades\Excel::toCollection($import, $filePath)[0];

    $import->collection($rows);

    $out = "Simulated Import Results:\n";
    $out .= "  Total: {$import->total}\n";
    $out .= "  Inserted: {$import->inserted}\n";
    $out .= "  Updated: {$import->updated}\n";
    $out .= "  Failed: {$import->failed}\n";

    file_put_contents(__DIR__ . '/import_result.txt', $out);
} catch (\Exception $e) {
    file_put_contents(__DIR__ . '/import_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "Exception written to import_error.txt\n";
}
