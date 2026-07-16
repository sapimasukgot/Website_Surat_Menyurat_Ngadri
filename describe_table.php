<?php

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = \Illuminate\Support\Facades\DB::select("DESCRIBE penduduks");
    foreach ($columns as $column) {
        echo $column->Field . " - " . $column->Type . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
