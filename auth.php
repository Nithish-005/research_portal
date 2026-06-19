<?php
session_start();
require 'db.php';

$u = $_POST['username'] ?? '';
$p = $_POST['password'] ?? '';

if (!$u || !$p) {
    header('Location: index.html?err=1');
    exit;
}

$stmt = $pdo->prepare("SELECT id, password_hash, role, full_name, target_j, target_p, cycle_id
                       FROM users WHERE username = ?");
$stmt->execute([$u]);
$user = $stmt->fetch();

if ($user && password_verify($p, $user['password_hash'])) {
    $_SESSION['uid']   = $user['id'];
    $_SESSION['role']  = $user['role'];
    $_SESSION['name']  = $user['full_name'];
    $_SESSION['tj']    = $user['target_j'];
    $_SESSION['tp']    = $user['target_p'];
    $_SESSION['cycle'] = $user['cycle_id'];

    // route
    switch ($user['role']) {
        case 'hod':            header('Location: hod_dashboard.php'); break;
        case 'research_admin': header('Location: research_admin_dashboard.php'); break;
        default:               header('Location: staff_dashboard.php');
    }
    exit;
}

header('Location: index.html?err=1');
?>