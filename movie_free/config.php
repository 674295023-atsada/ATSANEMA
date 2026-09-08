<?php
// config.php - ไฟล์กำหนดค่าการเชื่อมต่อฐานข้อมูล
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'movie_db';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    // ถ้ายังไม่ได้สร้างฐานข้อมูล ให้ redirect ไปหน้า setup.php ได้
    $error_code = $e->getCode();
    if ($error_code == 1049) { // Unknown database
        header('Location: setup.php');
        exit;
    } else {
        die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage() . "<br><a href='setup.php'>คลิกที่นี่เพื่อติดตั้งฐานข้อมูลอัตโนมัติ (setup.php)</a>");
    }
}
?>
