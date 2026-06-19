<?php
session_start();
if (!isset($_SESSION['uid'])) { header('Location: index.html?err=2'); exit; }
require 'db.php';

$uid   = $_SESSION['uid'];
$cycle = $_SESSION['cycle'];
$targetDir = __DIR__ . '/uploads/';

// basic mkdir once
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

$pdfPath = null;
if (!empty($_FILES['pdf']['name'])) {
    $file = $_FILES['pdf'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') { die('Only PDF allowed'); }
    $newName = uniqid('J', true) . '.pdf';
    if (move_uploaded_file($file['tmp_name'], $targetDir . $newName)) {
        $pdfPath = 'uploads/' . $newName;
    }
}

$sql = "INSERT INTO journals (user_id,cycle_id,title,authors,journal_name,issn,doi,vol,issue,pages,impact_factor,indexing,status,pdf_path)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt= $pdo->prepare($sql);
$stmt->execute([
    $uid,$cycle,
    $_POST['title'],$_POST['authors'],$_POST['journal_name'],
    $_POST['issn'],$_POST['doi'],$_POST['vol'],$_POST['issue'],$_POST['pages'],
    $_POST['impact_factor'],$_POST['indexing'],$_POST['status'],$pdfPath
]);

header('Location: staff_dashboard.php?ok=1');
?>