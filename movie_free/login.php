<?php
// login.php - หน้าเข้าสู่ระบบ (สำหรับทั้ง User และ Admin)
$page_title = 'เข้าสู่ระบบ';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth_check.php';

// ถ้าล็อกอินอยู่แล้ว ให้แยก redirect ตาม role
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_email = trim($_POST['username_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username_email) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้/อีเมล และรหัสผ่าน';
    } else {
        // ค้นหาผู้ใช้จาก username หรือ email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_email, $username_email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // บันทึก Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            $_SESSION['success'] = 'ยินดีต้อนรับคุณ ' . htmlspecialchars($user['full_name']);

            // นำทางแยกตามสิทธิ์ผู้ใช้งาน (User / Admin)
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'ชื่อผู้ใช้/อีเมล หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-custom shadow-lg p-3 p-md-4 my-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="fs-1 text-warning mb-2"><i class="fa-solid fa-right-to-bracket"></i></div>
                    <h3 class="fw-bold text-white">เข้าสู่ระบบ MovieFree</h3>
                    <p class="text-secondary small">เข้าสู่ระบบเพื่อรับชมภาพยนตร์ฟรีแบบไม่จำกัด</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-3">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username_email" class="form-label text-light fw-medium"><i class="fa-solid fa-user me-1 text-secondary"></i> ชื่อผู้ใช้ หรือ อีเมล</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="username_email" name="username_email" value="<?php echo htmlspecialchars($_POST['username_email'] ?? ''); ?>" required placeholder="Username หรือ Email">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label text-light fw-medium"><i class="fa-solid fa-lock me-1 text-secondary"></i> รหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-dark text-white border-secondary" id="password" name="password" required placeholder="••••••••">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password" title="แสดง/ซ่อนรหัสผ่าน">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold"><i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบ</button>
                    </div>

                    <div class="text-center text-secondary">
                        ยังไม่มีบัญชีสมาชิก? <a href="register.php" class="text-danger text-decoration-none font-weight-bold">สมัครสมาชิกที่นี่</a>
                    </div>
                </form>

                <div class="mt-4 pt-3 border-top border-secondary text-center small text-secondary">
                    <i class="fa-solid fa-circle-info me-1"></i> <strong>ทดลองใช้งาน:</strong><br>
                    Admin: <code class="text-warning">admin</code> / <code class="text-warning">admin123</code> | 
                    User: <code class="text-info">user1</code> / <code class="text-info">user123</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
