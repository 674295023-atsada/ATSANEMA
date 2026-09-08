<?php
// admin/dashboard.php - หน้า Dashboard รายงานข้อมูลสรุปสำหรับ Admin
$page_title = 'Dashboard รายงานข้อมูลผู้ดูแลระบบ';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin(); // เฉพาะ Admin เท่านั้น

// 1. ดึงสถิติต่างๆ สำหรับรายงาน
// จำนวนสมาชิกทั้งหมด
$stmt_total_users = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt_total_users->fetchColumn();

// จำนวนสมาชิกทั่วไป (User)
$stmt_regular_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
$regular_users = $stmt_regular_users->fetchColumn();

// จำนวนผู้ดูแลระบบ (Admin)
$stmt_admin_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$admin_users = $stmt_admin_users->fetchColumn();

// จำนวนภาพยนตร์ทั้งหมด
$stmt_total_movies = $pdo->query("SELECT COUNT(*) FROM movies");
$total_movies = $stmt_total_movies->fetchColumn();

// 2. ดึงสมาชิกที่สมัครล่าสุด 5 รายการ
$stmt_recent_users = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 5");
$recent_users = $stmt_recent_users->fetchAll();

// 3. ดึงหนังอัปเดตล่าสุด 5 รายการ
$stmt_recent_movies = $pdo->query("SELECT * FROM movies ORDER BY id DESC LIMIT 5");
$recent_movies = $stmt_recent_movies->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-chart-line text-warning me-2"></i>Admin Dashboard</h2>
        <p class="text-secondary small mb-0">รายงานภาพรวมสถิติสมาชิกและคลังภาพยนตร์ในระบบ</p>
    </div>
    <div class="d-flex gap-2">
        <a href="movies.php" class="btn btn-danger"><i class="fa-solid fa-plus me-1"></i> เพิ่มหนังใหม่</a>
        <a href="users.php" class="btn btn-info text-dark font-weight-bold"><i class="fa-solid fa-users-gear me-1"></i> จัดการสมาชิก</a>
    </div>
</div>

<!-- Metric Report Cards -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-5">
    <!-- Card 1: Total Users -->
    <div class="col">
        <div class="card card-custom p-3 border-start border-4 border-primary shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-medium">สมาชิกทั้งหมด</span>
                    <h2 class="fw-bold text-white mb-0 mt-1"><?php echo number_format($total_users); ?> <small class="fs-6 text-muted">คน</small></h2>
                </div>
                <div class="fs-1 text-primary opacity-75">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Regular Users -->
    <div class="col">
        <div class="card card-custom p-3 border-start border-4 border-info shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-medium">สมาชิกทั่วไป (User)</span>
                    <h2 class="fw-bold text-info mb-0 mt-1"><?php echo number_format($regular_users); ?> <small class="fs-6 text-muted">คน</small></h2>
                </div>
                <div class="fs-1 text-info opacity-75">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Admin Users -->
    <div class="col">
        <div class="card card-custom p-3 border-start border-4 border-warning shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-medium">ผู้ดูแลระบบ (Admin)</span>
                    <h2 class="fw-bold text-warning mb-0 mt-1"><?php echo number_format($admin_users); ?> <small class="fs-6 text-muted">คน</small></h2>
                </div>
                <div class="fs-1 text-warning opacity-75">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Movies -->
    <div class="col">
        <div class="card card-custom p-3 border-start border-4 border-danger shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-medium">ภาพยนตร์ทั้งหมด</span>
                    <h2 class="fw-bold text-danger mb-0 mt-1"><?php echo number_format($total_movies); ?> <small class="fs-6 text-muted">เรื่อง</small></h2>
                </div>
                <div class="fs-1 text-danger opacity-75">
                    <i class="fa-solid fa-film"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Data Reports Tables -->
<div class="row g-4">
    <!-- Table 1: Recent Users -->
    <div class="col-lg-6">
        <div class="card card-custom p-3 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i>สมาชิกสมัครใหม่ล่าสุด</h5>
                <a href="users.php" class="btn btn-outline-info btn-sm">ดูทั้งหมด</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th>ID</th>
                            <th>ชื่อผู้ใช้ / อีเมล</th>
                            <th>สิทธิ์</th>
                            <th>วันที่สมัคร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_users as $u): ?>
                            <tr>
                                <td>#<?php echo $u['id']; ?></td>
                                <td>
                                    <div class="fw-bold text-white"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                    <div class="small text-secondary">@<?php echo htmlspecialchars($u['username']); ?> | <?php echo htmlspecialchars($u['email']); ?></div>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-secondary"><?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Table 2: Recent Movies -->
    <div class="col-lg-6">
        <div class="card card-custom p-3 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0"><i class="fa-solid fa-clapperboard text-danger me-2"></i>ภาพยนตร์อัปเดตล่าสุด</h5>
                <a href="movies.php" class="btn btn-outline-danger btn-sm">จัดการหนัง</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th>โปสเตอร์</th>
                            <th>ชื่อเรื่อง / หมวดหมู่</th>
                            <th>วันที่เพิ่ม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_movies as $m): ?>
                            <tr>
                                <td style="width: 50px;">
                                    <img src="<?php echo htmlspecialchars($m['poster_url']); ?>" alt="" style="width: 40px; height: 55px; object-fit: cover;" class="rounded" onerror="this.src='https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80';">
                                </td>
                                <td>
                                    <div class="fw-bold text-white"><?php echo htmlspecialchars($m['title']); ?></div>
                                    <span class="badge bg-secondary small"><?php echo htmlspecialchars($m['category']); ?></span>
                                    <span class="badge bg-info text-dark small ms-1"><?php echo htmlspecialchars($m['genre'] ?? 'ทั่วไป'); ?></span>
                                </td>
                                <td class="small text-secondary"><?php echo date('d/m/Y', strtotime($m['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
