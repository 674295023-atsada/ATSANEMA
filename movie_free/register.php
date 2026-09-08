<?php
// register.php - หน้าสมัครสมาชิก
$page_title = 'สมัครสมาชิก';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth_check.php';

// ถ้าล็อกอินแล้ว ไม่ต้องสมัครใหม่
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($confirm_password)) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วนทุกช่อง';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif (strlen($password) < 6) {
        $error = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
    } elseif ($password !== $confirm_password) {
        $error = 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน';
    } else {
        // ตรวจสอบ Username หรือ Email ซ้ำ
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'ชื่อผู้ใช้ (Username) หรืออีเมลนี้ถูกใช้งานแล้ว';
        } else {
            // เข้ารหัสรหัสผ่านและบันทึกลงฐานข้อมูล
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_ins = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'user')");
            
            if ($stmt_ins->execute([$username, $email, $hashed_password, $full_name])) {
                $_SESSION['success'] = 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบด้วยบัญชีของคุณ';
                header('Location: login.php');
                exit;
            } else {
                $error = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-custom shadow-lg p-3 p-md-4 my-3">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="fs-1 text-danger mb-2"><i class="fa-solid fa-user-plus"></i></div>
                    <h3 class="fw-bold text-white">สมัครสมาชิกดูหนังฟรี</h3>
                    <p class="text-secondary small">กรอกข้อมูลด้านล่างเพื่อเริ่มต้นใช้งานระบบ</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-3">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label text-light fw-medium"><i class="fa-solid fa-user me-1 text-secondary"></i> ชื่อผู้ใช้ (Username)</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required placeholder="เช่น movie_fan01">
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label text-light fw-medium"><i class="fa-solid fa-id-card me-1 text-secondary"></i> ชื่อ-นามสกุลจริง</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required placeholder="เช่น สมชาย สุขสันต์">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-light fw-medium"><i class="fa-solid fa-envelope me-1 text-secondary"></i> อีเมล (Email)</label>
                        <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required placeholder="example@email.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-light fw-medium"><i class="fa-solid fa-lock me-1 text-secondary"></i> รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="password" name="password" required placeholder="••••••••">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password" title="แสดง/ซ่อนรหัสผ่าน">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label text-light fw-medium"><i class="fa-solid fa-shield-halved me-1 text-secondary"></i> ยืนยันรหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password" title="แสดง/ซ่อนรหัสผ่าน">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold"><i class="fa-solid fa-user-plus me-1"></i> สมัครสมาชิก</button>
                    </div>

                    <div class="text-center text-secondary">
                        มีบัญชีสมาชิกอยู่แล้ว? <a href="login.php" class="text-warning text-decoration-none font-weight-bold">เข้าสู่ระบบที่นี่</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
