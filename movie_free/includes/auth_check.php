<?php
// includes/auth_check.php - ฟังก์ชันจัดการตรวจสอบสิทธิ์การใช้งาน
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * ตรวจสอบว่าผู้ใช้งานเข้าสู่ระบบแล้วหรือยัง
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * ตรวจสอบว่าผู้ใช้งานมีสิทธิ์ Admin หรือไม่
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * ดึงข้อมูลผู้ใช้งานที่กำลังล็อกอินอยู่
 */
function currentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'full_name' => $_SESSION['full_name'] ?? '',
            'role' => $_SESSION['role'] ?? 'user'
        ];
    }
    return null;
}

/**
 * บังคับให้ต้องเข้าสู่ระบบก่อนใช้งานหน้านี้
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบก่อนใช้งานหน้านี้';
        header('Location: login.php');
        exit;
    }
}

/**
 * บังคับให้ต้องเป็น Admin เท่านั้นที่เข้าถึงหน้านี้ได้
 */
function requireAdmin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบด้วยบัญชีผู้ดูแลระบบ';
        header('Location: ../login.php');
        exit;
    }
    if (!isAdmin()) {
        $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงส่วนของผู้ดูแลระบบ (Admin Only)';
        header('Location: ../index.php');
        exit;
    }
}
?>
