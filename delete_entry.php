<?php
session_start();
if ($_SESSION['role']!=='research_admin') { http_response_code(403); exit; }
require 'db.php';
$id = (int)($_POST['id'] ?? 0);
// optional: unlink PDF too
$path = $pdo->query("SELECT pdf_path FROM journals WHERE id=$id")->fetchColumn();
if ($path && file_exists(__DIR__.'/'.$path)) unlink(__DIR__.'/'.$path);
$pdo->prepare("DELETE FROM journals WHERE id=?")->execute([$id]);
echo 'Deleted';