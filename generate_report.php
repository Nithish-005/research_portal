<?php
session_start();
if (!isset($_SESSION['uid']) || !in_array($_SESSION['role'], ['hod','research_admin'])) {
    http_response_code(403); exit('Forbidden');
}
require 'db.php';
require 'vendor/autoload.php';          // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$cycle = $_GET['cycle'] ?? $_SESSION['cycle'];

$stmt = $pdo->prepare("SELECT u.full_name, u.role, j.*
                       FROM journals j
                       JOIN users u ON u.id = j.user_id
                       WHERE j.cycle_id = ?
                       ORDER BY u.full_name, j.id");
$stmt->execute([$cycle]);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Cycle $cycle");

// header
$headers = ['Staff','Role','Title','Authors','Journal','ISSN','DOI','Vol','Issue','Pages','Status','Impact','Indexing','PDF'];
$sheet->fromArray($headers, NULL, 'A1');
$row = 2;
while ($r = $stmt->fetch()) {
    $sheet->fromArray([
        $r['full_name'],$r['role'],$r['title'],$r['authors'],$r['journal_name'],
        $r['issn'],$r['doi'],$r['vol'],$r['issue'],$r['pages'],
        $r['status'],$r['impact_factor'],$r['indexing'],$r['pdf_path']
    ], NULL, "A$row");
    $row++;
}

// download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"Cycle_$cycle_Report.xlsx\"");
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;