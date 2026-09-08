<?php
// admin/movies.php - จัดการข้อมูลภาพยนตร์สำหรับ Admin
$page_title = 'จัดการภาพยนตร์';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin();

$msg_success = '';
$msg_error = '';

// 1. เพิ่มหนังใหม่ (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_movie') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $poster_url = trim($_POST['poster_url'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($video_url)) {
        $msg_error = 'กรุณากรอกชื่อหนัง และ ลิงก์วิดีโอ (Video URL)';
    } else {
        if (empty($poster_url)) {
            $poster_url = 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80';
        }
        if (empty($category)) {
            $category = 'มาใหม่';
        }
        if (empty($genre)) {
            $genre = 'ทั่วไป';
        }
        $stmt_ins = $pdo->prepare("INSERT INTO movies (title, category, genre, poster_url, video_url, description) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt_ins->execute([$title, $category, $genre, $poster_url, $video_url, $description])) {
            $msg_success = 'เพิ่มภาพยนตร์ใหม่สำเร็จแล้ว!';
        } else {
            $msg_error = 'เกิดข้อผิดพลาดในการเพิ่มหนัง';
        }
    }
}

// 2. แก้ไขหนัง (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_movie') {
    $movie_id = (int)$_POST['movie_id'];
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $poster_url = trim($_POST['poster_url'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($video_url)) {
        $msg_error = 'กรุณากรอกชื่อหนัง และ ลิงก์วิดีโอ (Video URL)';
    } else {
        $stmt_upd = $pdo->prepare("UPDATE movies SET title = ?, category = ?, genre = ?, poster_url = ?, video_url = ?, description = ? WHERE id = ?");
        if ($stmt_upd->execute([$title, $category, $genre, $poster_url, $video_url, $description, $movie_id])) {
            $msg_success = 'อัปเดตข้อมูลภาพยนตร์และแนวหนังสำเร็จแล้ว!';
        } else {
            $msg_error = 'เกิดข้อผิดพลาดในการอัปเดต';
        }
    }
}

// 3. ลบหนัง (GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    $stmt_del = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    if ($stmt_del->execute([$del_id])) {
        $msg_success = 'ลบภาพยนตร์เรียบร้อยแล้ว';
    } else {
        $msg_error = 'เกิดข้อผิดพลาดในการลบ';
    }
}

// ดึงภาพยนตร์ทั้งหมด
$stmt_movies = $pdo->query("SELECT * FROM movies ORDER BY id DESC");
$movies_list = $stmt_movies->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-clapperboard text-danger me-2"></i>จัดการภาพยนตร์</h2>
        <p class="text-secondary small mb-0">เพิ่ม แก้ไข และลบภาพยนตร์และแนวหนังในคลังระบบ</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addMovieModal">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มภาพยนตร์ใหม่
        </button>
        <a href="dashboard.php" class="btn btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> กลับ Dashboard</a>
    </div>
</div>

<?php if ($msg_success): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($msg_success); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($msg_error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($msg_error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Datalist คำแนะนำสำหรับแนวหนัง -->
<datalist id="genreSuggestions">
    <option value="ครอบครัว">
    <option value="ไซไฟ">
    <option value="สยองขวัญ">
    <option value="ระทึกขวัญ">
    <option value="แอคชัน">
    <option value="แอนิเมชัน">
    <option value="โรแมนติก">
    <option value="คอมเมดี้">
    <option value="ดราม่า">
</datalist>

<!-- Datalist คำแนะนำสำหรับหมวดหมู่หลัก -->
<datalist id="categorySuggestions">
    <option value="มาใหม่">
    <option value="แนะนำ">
    <option value="ยอดนิยม">
    <option value="ได้รางวัล">
    <option value="ทำเงิน">
</datalist>

<!-- Table Listing Movies -->
<div class="card card-custom p-3 shadow-lg">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-secondary border-secondary">
                    <th>ID</th>
                    <th>โปสเตอร์</th>
                    <th>ชื่อเรื่อง</th>
                    <th>หมวดหมู่</th>
                    <th>แนวหนัง</th>
                    <th>ลิงก์วิดีโอ (MP4/Embed)</th>
                    <th>วันที่เพิ่ม</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($movies_list) > 0): ?>
                    <?php foreach ($movies_list as $m): ?>
                        <tr>
                            <td>#<?php echo $m['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($m['poster_url']); ?>" alt="" style="width: 45px; height: 60px; object-fit: cover;" class="rounded shadow-sm" onerror="this.src='https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80';">
                            </td>
                            <td class="fw-bold text-white"><?php echo htmlspecialchars($m['title']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($m['category']); ?></span></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($m['genre'] ?? 'ทั่วไป'); ?></span></td>
                            <td class="small text-secondary text-truncate" style="max-width: 180px;"><?php echo htmlspecialchars($m['video_url']); ?></td>
                            <td class="small text-secondary"><?php echo date('d/m/Y', strtotime($m['created_at'])); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="../watch.php?id=<?php echo $m['id']; ?>" class="btn btn-outline-light" target="_blank" title="ทดลองดู"><i class="fa-solid fa-eye"></i></a>
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#editMovieModal<?php echo $m['id']; ?>"><i class="fa-solid fa-pen-to-square"></i> แก้ไข</button>
                                    <a href="movies.php?action=delete&id=<?php echo $m['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('ยืนยันลบภาพยนตร์เรื่องนี้?');"><i class="fa-solid fa-trash"></i> ลบ</a>
                                </div>

                                <!-- Modal แก้ไขภาพยนตร์ -->
                                <div class="modal fade" id="editMovieModal<?php echo $m['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content bg-dark text-white border-secondary text-start">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square text-info me-2"></i>แก้ไขภาพยนตร์ #<?php echo $m['id']; ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="movies.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="edit_movie">
                                                    <input type="hidden" name="movie_id" value="<?php echo $m['id']; ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label text-light">ชื่อภาพยนตร์</label>
                                                        <input type="text" class="form-control bg-dark text-white border-secondary" name="title" value="<?php echo htmlspecialchars($m['title']); ?>" required>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label text-light">หมวดหมู่หลัก</label>
                                                            <input type="text" class="form-control bg-dark text-white border-secondary" name="category" list="categorySuggestions" value="<?php echo htmlspecialchars($m['category']); ?>" placeholder="มาใหม่, แนะนำ, ยอดนิยม, ได้รางวัล, ทำเงิน">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label text-light">แนวหนัง (Genre)</label>
                                                            <input type="text" class="form-control bg-dark text-white border-secondary" name="genre" list="genreSuggestions" value="<?php echo htmlspecialchars($m['genre'] ?? 'ทั่วไป'); ?>" placeholder="เช่น ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ, แอคชัน">
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-light">URL โปสเตอร์ (Poster Image URL)</label>
                                                        <input type="url" class="form-control bg-dark text-white border-secondary" name="poster_url" value="<?php echo htmlspecialchars($m['poster_url']); ?>">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-light">URL วิดีโอ (Direct MP4 / Embed URL)</label>
                                                        <input type="url" class="form-control bg-dark text-white border-secondary" name="video_url" value="<?php echo htmlspecialchars($m['video_url']); ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label text-light">เรื่องย่อ / รายละเอียด</label>
                                                        <textarea class="form-control bg-dark text-white border-secondary" name="description" rows="4"><?php echo htmlspecialchars($m['description']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                    <button type="submit" class="btn btn-info fw-bold text-dark">บันทึกการแก้ไข</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-secondary">ยังไม่มีข้อมูลภาพยนตร์ในระบบ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่มภาพยนตร์ใหม่ -->
<div class="modal fade" id="addMovieModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus text-danger me-2"></i>เพิ่มภาพยนตร์ใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="movies.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_movie">

                    <div class="mb-3">
                        <label class="form-label text-light">ชื่อภาพยนตร์ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" name="title" required placeholder="เช่น Avatar: The Way of Water">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">หมวดหมู่หลัก</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="category" list="categorySuggestions" placeholder="มาใหม่, แนะนำ, ยอดนิยม, ได้รางวัล, ทำเงิน">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">แนวหนัง (Genre)</label>
                            <input type="text" class="form-control bg-dark text-white border-secondary" name="genre" list="genreSuggestions" placeholder="เช่น ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ, แอคชัน">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">URL โปสเตอร์หนัง</label>
                        <input type="url" class="form-control bg-dark text-white border-secondary" name="poster_url" placeholder="https://example.com/poster.jpg">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">URL ไฟล์วิดีโอ (MP4) <span class="text-danger">*</span></label>
                        <input type="url" class="form-control bg-dark text-white border-secondary" name="video_url" required placeholder="https://example.com/video.mp4">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">เรื่องย่อ / รายละเอียด</label>
                        <textarea class="form-control bg-dark text-white border-secondary" name="description" rows="4" placeholder="กรอกเรื่องย่อหนัง..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fa-solid fa-plus me-1"></i> เพิ่มภาพยนตร์</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
