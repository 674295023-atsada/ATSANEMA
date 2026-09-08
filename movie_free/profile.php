<?php
// profile.php - หน้าแก้ไขข้อมูลสมาชิก และเปลี่ยนรหัสผ่าน
$page_title = 'แก้ไขข้อมูลส่วนตัว';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth_check.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$profile_error = '';
$profile_success = '';
$password_error = '';
$password_success = '';

// ดึงข้อมูลผู้ใช้งานปัจจุบัน
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();

if (!$user_data) {
    die("ไม่พบข้อมูลผู้ใช้งาน");
}

// 1. จัดการการอัปเดตข้อมูลส่วนตัว
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'update_profile') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($full_name) || empty($email)) {
        $profile_error = 'กรุณากรอกชื่อ-นามสกุล และอีเมล';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } else {
        // ตรวจสอบอีเมลซ้ำกับผู้ใช้อื่น
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt_check->execute([$email, $user_id]);
        if ($stmt_check->fetch()) {
            $profile_error = 'อีเมลนี้ถูกใช้งานโดยผู้ใช้อื่นแล้ว';
        } else {
            $stmt_upd = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            if ($stmt_upd->execute([$full_name, $email, $user_id])) {
                $_SESSION['full_name'] = $full_name; // อัปเดต session
                $user_data['full_name'] = $full_name;
                $user_data['email'] = $email;
                $profile_success = 'บันทึกการแก้ไขข้อมูลส่วนตัวสำเร็จ!';
            } else {
                $profile_error = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล';
            }
        }
    }
}

// 2. จัดการการเปลี่ยนรหัสผ่าน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $password_error = 'กรุณากรอกข้อมูลรหัสผ่านให้ครบถ้วน';
    } elseif (!password_verify($current_password, $user_data['password'])) {
        $password_error = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
    } elseif (strlen($new_password) < 6) {
        $password_error = 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
    } elseif ($new_password !== $confirm_new_password) {
        $password_error = 'รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน';
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_pass = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt_pass->execute([$new_hash, $user_id])) {
            $password_success = 'เปลี่ยนรหัสผ่านใหม่สำเร็จแล้ว!';
        } else {
            $password_error = 'เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <h2 class="fw-bold text-white mb-4"><i class="fa-solid fa-id-card text-warning me-2"></i>จัดการข้อมูลส่วนตัว</h2>

        <div class="row g-4">
            <!-- ฟอร์มแก้ไขข้อมูลส่วนตัว -->
            <div class="col-md-6">
                <div class="card card-custom h-100 p-3 p-md-4">
                    <div class="card-body">
                        <h4 class="card-title text-white fw-bold mb-3"><i class="fa-solid fa-user-pen me-2 text-info"></i>ข้อมูลสมาชิก</h4>
                        <hr class="border-secondary mb-4">

                        <?php if ($profile_error): ?>
                            <div class="alert alert-danger mb-3"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($profile_error); ?></div>
                        <?php endif; ?>

                        <?php if ($profile_success): ?>
                            <div class="alert alert-success mb-3"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($profile_success); ?></div>
                        <?php endif; ?>

                        <form action="profile.php" method="POST">
                            <input type="hidden" name="action_type" value="update_profile">

                            <div class="mb-3">
                                <label class="form-label text-secondary">ชื่อผู้ใช้ (Username)</label>
                                <input type="text" class="form-control bg-dark text-muted border-secondary" value="<?php echo htmlspecialchars($user_data['username']); ?>" disabled>
                                <div class="form-text text-secondary small">ชื่อผู้ใช้ไม่สามารถแก้ไขได้</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary">สถานะบัญชี (Role)</label>
                                <div>
                                    <?php if ($user_data['role'] === 'admin'): ?>
                                        <span class="badge bg-danger fs-6"><i class="fa-solid fa-shield-halved me-1"></i> ผู้ดูแลระบบ (Admin)</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary fs-6"><i class="fa-solid fa-user me-1"></i> สมาชิกทั่วไป (User)</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label text-light fw-medium">ชื่อ-นามสกุล</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label text-light fw-medium">อีเมล (Email)</label>
                                <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-info fw-bold text-dark"><i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูลส่วนตัว</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ฟอร์มเปลี่ยนรหัสผ่าน -->
            <div class="col-md-6">
                <div class="card card-custom h-100 p-3 p-md-4">
                    <div class="card-body">
                        <h4 class="card-title text-white fw-bold mb-3"><i class="fa-solid fa-key me-2 text-warning"></i>เปลี่ยนรหัสผ่าน</h4>
                        <hr class="border-secondary mb-4">

                        <?php if ($password_error): ?>
                            <div class="alert alert-danger mb-3"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($password_error); ?></div>
                        <?php endif; ?>

                        <?php if ($password_success): ?>
                            <div class="alert alert-success mb-3"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($password_success); ?></div>
                        <?php endif; ?>

                        <form action="profile.php" method="POST">
                            <input type="hidden" name="action_type" value="change_password">

                            <div class="mb-3">
                                <label for="current_password" class="form-label text-light fw-medium">รหัสผ่านปัจจุบัน</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-dark text-white border-secondary" id="current_password" name="current_password" required placeholder="••••••••">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password" title="แสดง/ซ่อนรหัสผ่าน">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label text-light fw-medium">รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-dark text-white border-secondary" id="new_password" name="new_password" required placeholder="••••••••">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password" title="แสดง/ซ่อนรหัสผ่าน">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_new_password" class="form-label text-light fw-medium">ยืนยันรหัสผ่านใหม่</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-dark text-white border-secondary" id="confirm_new_password" name="confirm_new_password" required placeholder="••••••••">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_new_password" title="แสดง/ซ่อนรหัสผ่าน">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning fw-bold"><i class="fa-solid fa-lock me-1"></i> เปลี่ยนรหัสผ่าน</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
