<?php
// index.php - หน้าหลัก แสดงรายการภาพยนตร์และแอนิเมชันจัดกลุ่มตามหมวดหมู่ พร้อมปุ่มเลือกประเภทหนัง (ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ ฯลฯ)
$page_title = 'หน้าแรก';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth_check.php';

// รับค่าคำค้นหา และ ประเภทหนัง (Genre Filter)
$search = trim($_GET['search'] ?? '');
$selected_genre = trim($_GET['genre'] ?? '');

// รายการประเภทหนังที่ปุ่ม Filter ให้กดเลือก
$genres_list = ['ครอบครัว', 'ไซไฟ', 'สยองขวัญ', 'ระทึกขวัญ', 'แอคชัน', 'แอนิเมชัน', 'โรแมนติก', 'คอมเมดี้', 'ดราม่า'];

// รายการหมวดหมู่หลัก 5 หมวด
$categories_list = ['มาใหม่', 'แนะนำ', 'ยอดนิยม', 'ได้รางวัล', 'ทำเงิน'];

$movies_by_category = [];
$filtered_movies = [];

if (!empty($search)) {
    // 1. ค้นหาตามคำค้นหา
    $stmt_search = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? OR description LIKE ? OR category LIKE ? OR genre LIKE ? ORDER BY id DESC");
    $stmt_search->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
    $filtered_movies = $stmt_search->fetchAll();
} elseif (!empty($selected_genre)) {
    // 2. กรองตามประเภทหนัง (ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ ฯลฯ)
    $stmt_genre = $pdo->prepare("SELECT * FROM movies WHERE genre = ? OR description LIKE ? ORDER BY id DESC");
    $stmt_genre->execute([$selected_genre, "%$selected_genre%"]);
    $filtered_movies = $stmt_genre->fetchAll();
} else {
    // 3. หน้าเริ่มต้น: แสดง 5 หมวดหมู่หลักแบบเลื่อนสไลด์ด้านข้าง
    foreach ($categories_list as $cat) {
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE category = ? ORDER BY id DESC");
        $stmt->execute([$cat]);
        $movies_by_category[$cat] = $stmt->fetchAll();
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner -->
<div class="p-4 p-md-5 mb-4 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #311042 50%, #111827 100%); border: 1px solid #374151;">
    <div class="col-md-8">
        <h1 class="display-5 fw-bold text-warning mb-3">ดูฟรีออนไลน์ HD</h1>
        <p class="lead text-light mb-4">คลังภาพยนตร์และแอนิเมชันครบครัน สมัครสมาชิกฟรี ดูได้ทันทีแบบไม่จำกัด</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="d-flex flex-wrap gap-3 mb-2">
                <a href="register.php" class="btn btn-danger btn-lg px-4 fw-bold shadow"><i class="fa-solid fa-user-plus me-1"></i> สมัครสมาชิกฟรี</a>
                <a href="login.php" class="btn btn-outline-light btn-lg px-4"><i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบ</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success d-inline-block px-4 py-2 mb-0 shadow-sm">
                <i class="fa-solid fa-circle-check me-2"></i>สวัสดีคุณ <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> เลือกดูเนื้อหาแยกตามประเภทด้านล่างได้ทันที!
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Search Bar Section -->
<div class="card card-custom p-3 mb-4 shadow-sm">
    <form action="index.php" method="GET" class="d-flex gap-2 mb-3">
        <div class="input-group">
            <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="ค้นหาชื่อเรื่อง, รายละเอียด หรือประเภท..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <button type="submit" class="btn btn-warning px-4 fw-bold">ค้นหา</button>
        <?php if (!empty($search) || !empty($selected_genre)): ?>
            <a href="index.php" class="btn btn-outline-secondary d-flex align-items-center text-nowrap" title="ล้างการกรอง"><i class="fa-solid fa-xmark me-1"></i> แสดงทั้งหมด</a>
        <?php endif; ?>
    </form>

    <!-- Genre Filter Buttons (ปุ่มเลือกประเภท: ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ ฯลฯ) -->
    <div class="d-flex align-items-center flex-wrap gap-2 pt-2 border-top border-secondary">
        <span class="text-secondary small fw-bold me-2"><i class="fa-solid fa-filter text-warning me-1"></i>ประเภท:</span>
        <a href="index.php" class="btn btn-sm <?php echo (empty($selected_genre) && empty($search)) ? 'btn-danger' : 'btn-outline-secondary'; ?> rounded-pill px-3">ทั้งหมด</a>
        <?php foreach ($genres_list as $g): ?>
            <a href="index.php?genre=<?php echo urlencode($g); ?>" class="btn btn-sm <?php echo ($selected_genre === $g) ? 'btn-danger' : 'btn-outline-secondary'; ?> rounded-pill px-3">
                <?php echo htmlspecialchars($g); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!empty($search) || !empty($selected_genre)): ?>
    <!-- Filtered Results Grid View -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-white mb-0">
            <?php if (!empty($search)): ?>
                ผลการค้นหา: "<?php echo htmlspecialchars($search); ?>"
            <?php else: ?>
                ประเภท: <?php echo htmlspecialchars($selected_genre); ?>
            <?php endif; ?>
            <span class="badge bg-secondary fs-6 ms-2 font-monospace"><?php echo count($filtered_movies); ?> เรื่อง</span>
        </h4>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปหน้าหมวดหมู่</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        <?php if (count($filtered_movies) > 0): ?>
            <?php foreach ($filtered_movies as $movie): ?>
                <div class="col">
                    <div class="card card-custom h-100 shadow-sm overflow-hidden border-secondary">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="height: 300px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80';">
                            <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-1 align-items-end">
                                <span class="badge bg-danger fs-6 opacity-90"><?php echo htmlspecialchars($movie['category']); ?></span>
                                <span class="badge bg-info text-dark fs-6 opacity-90"><?php echo htmlspecialchars($movie['genre'] ?? 'ทั่วไป'); ?></span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-white fw-bold text-truncate"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text text-secondary small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($movie['description']); ?></p>
                            <div class="mt-2 pt-2 border-top border-secondary">
                                <?php if (isLoggedIn()): ?>
                                    <a href="watch.php?id=<?php echo $movie['id']; ?>" class="btn btn-warning w-100 fw-bold"><i class="fa-solid fa-play me-1"></i> รับชม</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-outline-warning w-100 fw-bold" onclick="alert('กรุณาเข้าสู่ระบบเพื่อรับชม');"><i class="fa-solid fa-lock me-1"></i> ล็อกอินเพื่อรับชม</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-white">ไม่พบรายการที่ค้นหาในหมวดนี้</h4>
                <a href="index.php" class="btn btn-warning mt-2"><i class="fa-solid fa-rotate-left me-1"></i> แสดงภาพยนตร์ทั้งหมด</a>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Horizontal Scroll Category Rows (Strictly: มาใหม่, แนะนำ, ยอดนิยม, ได้รางวัล, ทำเงิน) -->
    <?php foreach ($categories_list as $cat_name): ?>
        <?php $list = $movies_by_category[$cat_name] ?? []; ?>
        <div class="mb-5">
            <h4 class="fw-bold text-white mb-3"><?php echo htmlspecialchars($cat_name); ?></h4>

            <?php if (count($list) > 0): ?>
                <div class="movie-scroll-row">
                    <?php foreach ($list as $movie): ?>
                        <div class="card card-custom movie-card-item overflow-hidden shadow">
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="height: 280px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80';">
                                <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-1 align-items-end">
                                    <span class="badge bg-danger fs-6 opacity-90"><?php echo htmlspecialchars($movie['category']); ?></span>
                                    <span class="badge bg-info text-dark fs-6 opacity-90"><?php echo htmlspecialchars($movie['genre'] ?? 'ทั่วไป'); ?></span>
                                </div>
                            </div>
                            <div class="card-body p-3 d-flex flex-column">
                                <h6 class="card-title text-white fw-bold text-truncate mb-1" title="<?php echo htmlspecialchars($movie['title']); ?>">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </h6>
                                <p class="card-text text-secondary small flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($movie['description']); ?>
                                </p>
                                <div class="mt-auto">
                                    <?php if (isLoggedIn()): ?>
                                        <a href="watch.php?id=<?php echo $movie['id']; ?>" class="btn btn-warning btn-sm w-100 fw-bold">
                                            <i class="fa-solid fa-play me-1"></i> รับชม
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-outline-warning btn-sm w-100 fw-bold" onclick="alert('กรุณาเข้าสู่ระบบเพื่อรับชม');">
                                            <i class="fa-solid fa-lock me-1"></i> ล็อกอิน
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-3 bg-dark text-secondary rounded border border-secondary text-center small">
                    ยังไม่มีข้อมูลในหมวดหมู่นี้
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
