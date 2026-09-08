<?php
// includes/header.php - ส่วนหัวของเว็บไซต์ (Navbar & Layout)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth_check.php';

$is_admin_area = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;
$base_url = $is_admin_area ? '../' : './';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - MovieFree' : 'MovieFree - ดูหนังฟรีออนไลน์'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #0b0f19;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-custom {
            background-color: #111827;
            border-bottom: 1px solid #1f2937;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }
        .brand-logo {
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #e11d48 !important;
            text-decoration: none;
        }
        .nav-link {
            color: #9ca3af;
            font-weight: 500;
            transition: color 0.2s ease-in-out;
        }
        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
        }
        .main-content {
            flex: 1;
        }
        .card-custom {
            background-color: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.75rem;
            color: #f3f4f6;
        }
        .badge-admin {
            background-color: #dc2626;
            color: #ffffff;
        }
        .badge-user {
            background-color: #2563eb;
            color: #ffffff;
        }

        /* Horizontal Scroll Slider Styles */
        .movie-scroll-row {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 1.25rem;
            padding-bottom: 1rem;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
        .movie-scroll-row::-webkit-scrollbar {
            height: 8px;
        }
        .movie-scroll-row::-webkit-scrollbar-track {
            background: #111827;
            border-radius: 10px;
        }
        .movie-scroll-row::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }
        .movie-scroll-row::-webkit-scrollbar-thumb:hover {
            background: #e11d48;
        }
        .movie-card-item {
            flex: 0 0 220px;
            max-width: 220px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .movie-card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <!-- Logo แอป (คลิกกลับหน้าแรก) -->
        <a class="navbar-brand brand-logo fs-3" href="<?php echo $base_url; ?>index.php" title="กลับหน้าหลัก">
            <i class="fa-solid fa-film me-2"></i>Movie<span class="text-white">Free</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isAdmin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-gauge me-1"></i> ระบบผู้ดูแล (Admin)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow">
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>admin/dashboard.php"><i class="fa-solid fa-chart-line me-2 text-warning"></i>Dashboard รายงาน</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>admin/users.php"><i class="fa-solid fa-users-gear me-2 text-info"></i>จัดการข้อมูลสมาชิก</a></li>
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>admin/movies.php"><i class="fa-solid fa-clapperboard me-2 text-danger"></i>จัดการภาพยนตร์</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (isLoggedIn()): ?>
                    <?php $user = currentUser(); ?>
                    <!-- Dropdown เมนูผู้ใช้งาน (รวมปุ่มออกจากระบบที่นี่ที่เดียว) -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user fs-5 text-warning"></i>
                            <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge badge-admin">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-user">Member</span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="userMenuDropdown">
                            <li>
                                <a class="dropdown-item" href="<?php echo $base_url; ?>profile.php">
                                    <i class="fa-solid fa-id-card me-2 text-info"></i>แก้ไขข้อมูลส่วนตัว
                                </a>
                            </li>
                            <?php if (isAdmin()): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>admin/dashboard.php">
                                        <i class="fa-solid fa-gauge-high me-2 text-warning"></i>Dashboard Admin
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger fw-bold" href="<?php echo $base_url; ?>logout.php">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>login.php" class="btn btn-outline-light me-2">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบ
                    </a>
                    <a href="<?php echo $base_url; ?>register.php" class="btn btn-danger">
                        <i class="fa-solid fa-user-plus me-1"></i> สมัครสมาชิก
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="main-content py-4">
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
