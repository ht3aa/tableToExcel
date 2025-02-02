<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function exportTableToExcel($host, $port, $dbname, $user, $password, $tableName, $outputFile, $withRows)
{
    try {
        // Connect to the PostgreSQL database
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to the database.\n";

        // Query the table
        $query = $pdo->query("SELECT * FROM public.\"$tableName\"");
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            throw new Exception("The table '$tableName' is empty or does not exist.");
        }

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Add column headers
        $headers = array_keys($rows[0]);
        $sheet->fromArray($headers, null, 'A1');

        if ((int) $withRows === 1) {
            // Add data rows
            $sheet->fromArray($rows, null, 'A2');
        }

        // Save to Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputFile);

        echo "Table '$tableName' exported successfully to '$outputFile'.\n";
    } catch (Exception $e) {
        echo 'An error occurred: '.$e->getMessage()."\n";
    }
}

// Command-line arguments
if ($argc !== 5) {
    echo "Usage: php export_table_to_excel.php <database_name> <table_name_without_public> <file_path_with_file_name> <with_rows_1_0>\n";
    exit(1);
}

$dbname = $argv[1];
$tableName = $argv[2];
$outputFilePath = $argv[3];
$withRows = $argv[4];

// Connection details
$host = 'localhost';
$port = '5432';
$user = 'postgres';
$password = 'ht3aa';

// Run the export function
exportTableToExcel($host, $port, $dbname, $user, $password, $tableName, $outputFilePath, $withRows);
