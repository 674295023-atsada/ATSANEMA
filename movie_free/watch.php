<?php
// watch.php - หน้าเล่นวิดีโอรับชมภาพยนตร์
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth_check.php';

requireLogin(); // ต้องล็อกอินก่อนเท่านั้น

$movie_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$movie_id) {
    header('Location: index.php');
    exit;
}

// ดึงข้อมูลหนังที่ต้องการดู
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    $_SESSION['error'] = 'ไม่พบภาพยนตร์เรื่องนี้ในระบบ';
    header('Location: index.php');
    exit;
}

$page_title = 'รับชม ' . $movie['title'];

// ดึงรายการหนังอื่นๆ แนะนำ
$stmt_other = $pdo->prepare("SELECT * FROM movies WHERE id != ? ORDER BY RAND() LIMIT 4");
$stmt_other->execute([$movie_id]);
$other_movies = $stmt_other->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="row">
    <!-- เครื่องเล่นวิดีโอหนัง -->
    <div class="col-lg-8 mb-4">
        <div class="card card-custom p-3 overflow-hidden shadow-lg">
            <div class="ratio ratio-16x9 bg-black rounded-3 overflow-hidden mb-3">
                <?php if (strpos($movie['video_url'], 'youtube.com') !== false || strpos($movie['video_url'], 'youtu.be') !== false): ?>
                    <iframe src="<?php echo htmlspecialchars($movie['video_url']); ?>" allowfullscreen></iframe>
                <?php else: ?>
                    <video controls autoplay class="w-100 h-100">
                        <source src="<?php echo htmlspecialchars($movie['video_url']); ?>" type="video/mp4">
                        เบราว์เซอร์ของคุณไม่รองรับการเล่นวิดีโอ HTML5
                    </video>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                    <h3 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($movie['title']); ?></h3>
                    <span class="badge bg-danger fs-6"><i class="fa-solid fa-tag me-1"></i><?php echo htmlspecialchars($movie['category'] ?? 'ทั่วไป'); ?></span>
                    <span class="badge bg-info text-dark fs-6 ms-1"><?php echo htmlspecialchars($movie['genre'] ?? 'ทั่วไป'); ?></span>
                    <span class="text-secondary small ms-2"><i class="fa-solid fa-clock me-1"></i>อัปเดตเมื่อ: <?php echo date('d/m/Y', strtotime($movie['created_at'])); ?></span>
                </div>
                <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปรายการหนัง</a>
            </div>

            <hr class="border-secondary my-3">

            <div>
                <h5 class="text-warning fw-bold mb-2"><i class="fa-solid fa-align-left me-2"></i>เรื่องย่อ</h5>
                <p class="text-light leading-relaxed"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
            </div>
        </div>
    </div>

    <!-- รายการหนังเรื่องอื่นๆ -->
    <div class="col-lg-4">
        <div class="card card-custom p-3 shadow">
            <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-film text-danger me-2"></i>เรื่องอื่นๆ ที่น่าสนใจ</h5>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($other_movies as $item): ?>
                    <div class="d-flex gap-3 align-items-center bg-dark p-2 rounded border border-secondary">
                        <img src="<?php echo htmlspecialchars($item['poster_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 70px; height: 95px; object-fit: cover;" class="rounded" onerror="this.src='https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80';">
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="text-white fw-bold mb-1 text-truncate"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($item['category']); ?></span>
                            <div>
                                <a href="watch.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-warning py-0 px-2"><i class="fa-solid fa-play me-1"></i> รับชม</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
