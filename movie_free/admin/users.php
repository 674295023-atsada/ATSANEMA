<?php
// admin/users.php - จัดการข้อมูลสมาชิกสำหรับผู้ดูแลระบบ
$page_title = 'จัดการข้อมูลสมาชิก';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin();

$current_admin_id = $_SESSION['user_id'];
$msg_success = '';
$msg_error = '';

// 1. จัดการการลบสมาชิก
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    if ($target_id === $current_admin_id) {
        $msg_error = 'คุณไม่สามารถลบบัญชีผู้ใช้ของตัวเองที่กำลังใช้งานอยู่ได้';
    } else {
        $stmt_del = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt_del->execute([$target_id])) {
            $msg_success = 'ลบสมาชิกเรียบร้อยแล้ว';
        } else {
            $msg_error = 'เกิดข้อผิดพลาดในการลบสมาชิก';
        }
    }
}

// 2. จัดการการเปลี่ยนสิทธิ์ (Role Switch)
if (isset($_GET['action']) && $_GET['action'] === 'toggle_role' && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    if ($target_id === $current_admin_id) {
        $msg_error = 'คุณไม่สามารถเปลี่ยนสิทธิ์บัญชีของตัวเองได้';
    } else {
        $stmt_get = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt_get->execute([$target_id]);
        $u_role = $stmt_get->fetchColumn();
        if ($u_role) {
            $new_role = ($u_role === 'admin') ? 'user' : 'admin';
            $stmt_upd = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt_upd->execute([$new_role, $target_id]);
            $msg_success = "เปลี่ยนสิทธิ์ผู้ใช้เป็น '$new_role' เรียบร้อยแล้ว";
        }
    }
}

// 3. จัดการการแก้ไขข้อมูลสมาชิก (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $edit_id = (int)$_POST['user_id'];
    $edit_full_name = trim($_POST['full_name'] ?? '');
    $edit_email = trim($_POST['email'] ?? '');
    $edit_role = $_POST['role'] ?? 'user';

    if (empty($edit_full_name) || empty($edit_email)) {
        $msg_error = 'กรุณากรอกข้อมูลชื่อและอีเมลให้ครบถ้วน';
    } else {
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt_check->execute([$edit_email, $edit_id]);
        if ($stmt_check->fetch()) {
            $msg_error = 'อีเมลนี้ถูกใช้งานโดยผู้ใช้อื่นแล้ว';
        } else {
            $stmt_u = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
            if ($stmt_u->execute([$edit_full_name, $edit_email, $edit_role, $edit_id])) {
                $msg_success = 'บันทึกการแก้ไขข้อมูลสมาชิกเรียบร้อยแล้ว';
            } else {
                $msg_error = 'เกิดข้อผิดพลาดในการอัปเดต';
            }
        }
    }
}

// ดึงรายการสมาชิกทั้งหมด
$stmt_users = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$all_users = $stmt_users->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-users-gear text-info me-2"></i>จัดการข้อมูลสมาชิก</h2>
        <p class="text-secondary small mb-0">ดู เปลี่ยนสิทธิ์ และจัดการรายชื่อสมาชิกในระบบ</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> กลับ Dashboard</a>
</div>

<?php if ($msg_success): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($msg_success); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($msg_error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($msg_error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card card-custom p-3 shadow-lg">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-secondary border-secondary">
                    <th>ID</th>
                    <th>ชื่อผู้ใช้ (Username)</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมล</th>
                    <th>สิทธิ์ใช้งาน</th>
                    <th>วันที่สมัคร</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_users as $u): ?>
                    <tr>
                        <td>#<?php echo $u['id']; ?></td>
                        <td class="fw-bold text-warning">@<?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td class="text-secondary"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge bg-danger"><i class="fa-solid fa-shield-halved me-1"></i> Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary"><i class="fa-solid fa-user me-1"></i> User</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-secondary"><?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <!-- ปุ่มเปลี่ยนสิทธิ์ -->
                                <?php if ($u['id'] !== $current_admin_id): ?>
                                    <a href="users.php?action=toggle_role&id=<?php echo $u['id']; ?>" class="btn btn-outline-warning" title="สลับสิทธิ์ User/Admin" onclick="return confirm('สลับสิทธิ์ผู้ใช้งานคนนี้?');">
                                        <i class="fa-solid fa-repeat"></i> สิทธิ์
                                    </a>
                                <?php endif; ?>

                                <!-- ปุ่มแก้ไข Modal -->
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $u['id']; ?>">
                                    <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                </button>

                                <!-- ปุ่มลบ -->
                                <?php if ($u['id'] !== $current_admin_id): ?>
                                    <a href="users.php?action=delete&id=<?php echo $u['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('ยืนยันที่จะลบสมาชิกคนนี้?');">
                                        <i class="fa-solid fa-trash"></i> ลบ
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Modal แก้ไขข้อมูลสมาชิก -->
                            <div class="modal fade" id="editModal<?php echo $u['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-white border-secondary text-start">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen text-info me-2"></i>แก้ไขข้อมูลสมาชิก #<?php echo $u['id']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="users.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="edit_user">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">

                                                <div class="mb-3">
                                                    <label class="form-label text-secondary">Username</label>
                                                    <input type="text" class="form-control bg-secondary text-light border-0" value="<?php echo htmlspecialchars($u['username']); ?>" disabled>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-light">ชื่อ-นามสกุล</label>
                                                    <input type="text" class="form-control bg-dark text-white border-secondary" name="full_name" value="<?php echo htmlspecialchars($u['full_name']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-light">อีเมล</label>
                                                    <input type="email" class="form-control bg-dark text-white border-secondary" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-light">สิทธิ์ใช้งาน</label>
                                                    <select name="role" class="form-select bg-dark text-white border-secondary">
                                                        <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>สมาชิกทั่วไป (User)</option>
                                                        <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>ผู้ดูแลระบบ (Admin)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                <button type="submit" class="btn btn-info font-weight-bold text-dark">บันทึกการแก้ไข</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
