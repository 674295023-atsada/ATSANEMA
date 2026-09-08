<?php
// logout.php - ออกจากระบบ
require_once __DIR__ . '/config.php';

session_unset();
session_destroy();

session_start();
$_SESSION['success'] = 'ออกจากระบบเรียบร้อยแล้ว';
header('Location: login.php');
exit;
?>
