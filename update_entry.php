<?php
session_start();
if ($_SESSION['role']!=='research_admin') { http_response_code(403); exit; }
require 'db.php';
$id = (int)($_POST['id'] ?? 0);
$st = $_POST['status'] ?? '';
if ($id && in_array($st,['Submitted','Accepted','Published'])) {
    $pdo->prepare("UPDATE journals SET status=? WHERE id=?")->execute([$st,$id]);
    echo 'Updated';
} else echo 'Fail';