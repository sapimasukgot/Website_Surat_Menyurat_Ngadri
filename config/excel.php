<?php
use Maatwebsite\Excel\Excel;

return [
    'exports' => [
        'chunk_size' => 1000,
        'pre_calculate_formulas' => false,
        'strict_null_comparison' => false,
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => false,
        ],
    ],
    'imports' => [
        'read_only' => true,
        'ignore_empty' => false,
        'heading_row' => [
            'formatter' => 'slug',
        ],
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
        ],
    ],
    'temporary_files' => [
        'local_path' => storage_path('framework/cache/laravel-excel'),
    ],
];
