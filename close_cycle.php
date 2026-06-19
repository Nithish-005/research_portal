<?php
session_start();
if ($_SESSION['role'] !== 'hod') { http_response_code(403); exit; }
require 'db.php';
$pdo->exec("UPDATE cycles SET is_active=0 WHERE id=(SELECT MAX(id) FROM cycles)");
$pdo->exec("INSERT INTO cycles(name,start_date,end_date,is_active)
            VALUES('Cycle 2',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 1 YEAR),1)");
$pdo->exec("UPDATE users SET cycle_id=cycle_id+1");
echo "Cycle closed & Cycle 2 opened";