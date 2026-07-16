<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $filePath = __DIR__ . '/bip_ 2019.xlsx';
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();

    $uniqueStatuses = [];
    $uniqueJks = [];
    $uniqueShdks = [];
    $uniqueAgamas = [];
    $uniqueGolDrhs = [];

    for ($row = 4; $row <= $highestRow; $row++) {
        $jk = $worksheet->getCell('E' . $row)->getValue();
        $gol = $worksheet->getCell('H' . $row)->getValue();
        $agama = $worksheet->getCell('I' . $row)->getValue();
        $status = $worksheet->getCell('J' . $row)->getValue();
        $shdk = $worksheet->getCell('K' . $row)->getValue();

        if ($jk !== null && $jk !== '')
            $uniqueJks[$jk] = true;
        if ($gol !== null && $gol !== '')
            $uniqueGolDrhs[$gol] = true;
        if ($agama !== null && $agama !== '')
            $uniqueAgamas[$agama] = true;
        if ($status !== null && $status !== '')
            $uniqueStatuses[$status] = true;
        if ($shdk !== null && $shdk !== '')
            $uniqueShdks[$shdk] = true;
    }

    $out = "Distinct JK: " . json_encode(array_keys($uniqueJks)) . "\n";
    $out .= "Distinct GOL. DRH: " . json_encode(array_keys($uniqueGolDrhs)) . "\n";
    $out .= "Distinct AGAMA: " . json_encode(array_keys($uniqueAgamas)) . "\n";
    $out .= "Distinct STATUS: " . json_encode(array_keys($uniqueStatuses)) . "\n";
    $out .= "Distinct SHDK: " . json_encode(array_keys($uniqueShdks)) . "\n";

    file_put_contents(__DIR__ . '/excel_values_output.txt', $out);
    echo "Done output.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
