<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function exportTableToExcel($host, $port, $dbname, $user, $password, $tableName, $outputFile)
{
	try {
		// Connect to the PostgreSQL database
		$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo "Connected to the database.\n";

		// Query the table
		$query = $pdo->query("SELECT * FROM $tableName");
		$rows = $query->fetchAll(PDO::FETCH_ASSOC);

		if (empty($rows)) {
			throw new Exception("The table '$tableName' is empty or does not exist.");
		}

		// Create a new Spreadsheet
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Add column headers
		$headers = array_keys($rows[0]);
		$sheet->fromArray($headers, null, 'A1');

		// Add data rows
		$sheet->fromArray($rows, null, 'A2');

		// Save to Excel file
		$writer = new Xlsx($spreadsheet);
		$writer->save($outputFile);

		echo "Table '$tableName' exported successfully to '$outputFile'.\n";
	} catch (Exception $e) {
		echo "An error occurred: " . $e->getMessage() . "\n";
	}
}

// Command-line arguments
if ($argc !== 4) {
	echo "Usage: php export_table_to_excel.php <database_name> <table_name> <file_path>\n";
	exit(1);
}

$dbname = $argv[1];
$tableName = $argv[2];
$outputFilePath = $argv[3];

// Connection details
$host = 'localhost';
$port = '5432';
$user = 'username';
$password = 'password';

// Run the export function
exportTableToExcel($host, $port, $dbname, $user, $password, $tableName, $outputFilePath);
