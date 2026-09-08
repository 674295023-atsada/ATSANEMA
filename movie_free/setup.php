<?php
// setup.php - สคริปต์ติดตั้งฐานข้อมูลอัตโนมัติ
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'movie_db';

$messages = [];
$status = 'success';

try {
    // 1. เชื่อมต่อ MySQL Server
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $messages[] = ['type' => 'success', 'text' => 'เชื่อมต่อกับ MySQL Server สำเร็จ'];

    // 2. สร้าง Database movie_db
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $messages[] = ['type' => 'success', 'text' => "สร้างฐานข้อมูล `$dbname` สำเร็จ (หรือมีอยู่แล้ว)"];

    // 3. เลือกฐานข้อมูล movie_db
    $pdo->exec("USE `$dbname`");

    // 4. สร้างตาราง users
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(100) NOT NULL,
        `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql_users);
    $messages[] = ['type' => 'success', 'text' => 'สร้างตาราง users (ข้อมูลสมาชิกและแอดมิน) สำเร็จ'];

    // 5. สร้างตาราง movies (พร้อมฟิลด์ category และ genre)
    $sql_movies = "CREATE TABLE IF NOT EXISTS `movies` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `category` VARCHAR(100) DEFAULT 'มาใหม่',
        `genre` VARCHAR(100) DEFAULT 'ทั่วไป',
        `poster_url` VARCHAR(500),
        `video_url` VARCHAR(500),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql_movies);

    // ตรวจสอบและเพิ่มคอลัมน์ genre หากยังไม่มี
    try {
        $pdo->exec("ALTER TABLE `movies` ADD `genre` VARCHAR(100) DEFAULT 'ทั่วไป' AFTER `category`");
    } catch (PDOException $e) {
        // มีคอลัมน์อยู่แล้ว ข้ามได้
    }
    $messages[] = ['type' => 'success', 'text' => 'สร้างตาราง movies (พร้อมระบบหมวดหมู่ประเภทหนังและแอนิเมชัน) สำเร็จ'];

    // 6. เพิ่มข้อมูล Admin เริ่มต้น
    $stmt_check_admin = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt_check_admin->execute();
    if ($stmt_check_admin->fetchColumn() == 0) {
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt_ins_admin = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES ('admin', 'admin@example.com', ?, 'ผู้ดูแลระบบสูงสุด', 'admin')");
        $stmt_ins_admin->execute([$admin_pass]);
        $messages[] = ['type' => 'info', 'text' => 'สร้างบัญชี Admin เริ่มต้น: User: admin | Pass: admin123'];
    }

    // 7. เพิ่มข้อมูล สมาชิกทดสอบ เริ่มต้น
    $stmt_check_user = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'user1'");
    $stmt_check_user->execute();
    if ($stmt_check_user->fetchColumn() == 0) {
        $user_pass = password_hash('user123', PASSWORD_DEFAULT);
        $stmt_ins_user = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES ('user1', 'user1@example.com', ?, 'สมชาย สายหนัง', 'user')");
        $stmt_ins_user->execute([$user_pass]);
        $messages[] = ['type' => 'info', 'text' => 'สร้างบัญชี สมาชิกทดสอบ: User: user1 | Pass: user123'];
    }

    // 8. เพิ่มข้อมูลสื่อภาพยนตร์และแอนิเมชันจำนวนมาก ครบทั้ง 5 กลุ่มหลัก และประเภทสื่อ (ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ, แอคชัน, ฯลฯ)
    $sample_items = [
        // --- หมวด: มาใหม่ ---
        [
            'title' => 'Big Buck Bunny (กระต่ายยักษ์ผจญภัย)',
            'description' => 'เรื่องราวของกระต่ายยักษ์น่ารักและสงบสุข ที่ต้องลุกขึ้นมาปกป้องผืนป่าและเหล่าเพื่อนสัตว์จากแก๊งกวนเมือง',
            'category' => 'มาใหม่',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
        ],
        [
            'title' => 'Suzume (การผนึกประตูของซูซุเมะ)',
            'description' => 'แอนิเมชันเรื่องเยี่ยม การเดินทางของเด็กสาวเพื่อปิดประตูมิติที่นำพาภัยพิบัติมาสู่ญี่ปุ่น',
            'category' => 'มาใหม่',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
        ],
        [
            'title' => 'For Bigger Escapes (การหลบหนีสุดระทึก)',
            'description' => 'ภาพยนตร์แนวแอคชันผจญภัย การแหกคุกกลางทะเลทรายและภารกิจเอาชีวิตรอดสุดระทึกใจ',
            'category' => 'มาใหม่',
            'genre' => 'ระทึกขวัญ',
            'poster_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'
        ],
        [
            'title' => 'Spider-Man: Across the Spider-Verse',
            'description' => 'การท่องพหุภพของไมลส์ โมราเลส พบเจอสไปเดอร์แมนจากหลากหลายมิติทั่วจักรวาล',
            'category' => 'มาใหม่',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],
        [
            'title' => 'Chromecast HD (เปิดมิติใหม่แห่งความบันเทิง)',
            'description' => 'เรื่องราวสารคดีสั้นท่องโลกอนาคต สัมผัสความตระการตาของเทคโนโลยีภาพและเสียงความละเอียดสูง',
            'category' => 'มาใหม่',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
        ],
        [
            'title' => 'Oppenheimer (ออปเพนไฮเมอร์)',
            'description' => 'เรื่องราวชีวิตของบิดาแห่งระเบิดปรมาณูและการทดลองประวัติศาสตร์ที่จะเปลี่ยนแปลงโลกไปตลอดกาล',
            'category' => 'มาใหม่',
            'genre' => 'ดราม่า',
            'poster_url' => 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'
        ],
        [
            'title' => 'Cyberpunk Edgerunners (นักล่าไซเบอร์)',
            'description' => 'แอนิเมชันแนวไซเบอร์พังก์ เรื่องราวของเด็กหนุ่มที่ต้องการเอาชีวิตรอดในเมืองใหญ่อันป่าเถื่อน',
            'category' => 'มาใหม่',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
        ],
        [
            'title' => 'Avatar: The Way of Water',
            'description' => 'การหวนคืนสู่ดาวแพนดอร่า ศึกปกป้องมหาสมุทรและครอบครัวจากผู้รุกรานจากต่างดาว',
            'category' => 'มาใหม่',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
        ],
        [
            'title' => 'A Quiet Place (ดินแดนไร้เสียง)',
            'description' => 'ครอบครัวที่ต้องเอาชีวิตรอดในโลกซากปรักหักพังโดยห้ามส่งเสียงดังเด็ดขาด สัตว์ประหลาดที่ล่าด้วยเสียง',
            'category' => 'มาใหม่',
            'genre' => 'สยองขวัญ',
            'poster_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],

        // --- หมวด: แนะนำ ---
        [
            'title' => 'Elephant Dream (ความฝันแห่งโลกอนาคต)',
            'description' => 'การเดินทางสำรวจโลกไซเบอร์เชิงปรัชญาและจักรกลมหัศจรรย์ ระหว่างสองตัวละคร Proog และ Emo',
            'category' => 'แนะนำ',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
        ],
        [
            'title' => 'Your Name (หลับตาฝัน ถึงชื่อเธอ)',
            'description' => 'แอนิเมชันโรแมนติกแฟนตาซี เรื่องราวของเด็กหนุ่มสาวสองคนที่สลับร่างกันอย่างปริศนา',
            'category' => 'แนะนำ',
            'genre' => 'โรแมนติก',
            'poster_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'
        ],
        [
            'title' => 'For Bigger Fun (ภารกิจสนุกทะลุมิติ)',
            'description' => 'การผจญภัยแฟนตาซีของกลุ่มวัยรุ่นที่ค้นพบประตูมิติโบราณ นำไปสู่โลกแห่งมนตราและสิ่งมีชีวิตอัศจรรย์',
            'category' => 'แนะนำ',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
        ],
        [
            'title' => 'Demon Slayer: Mugen Train (ศึกรถไฟสู่นิรันดร์)',
            'description' => 'แอนิเมชันแอคชันสุดอลังการ ทันจิโร่และเรนโกคุร่วมมือกันต่อสู้กับอสูรบนรถไฟสายมรณะ',
            'category' => 'แนะนำ',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],
        [
            'title' => 'We Are Going On Bullrun (ซิ่งทะลุพายุ)',
            'description' => 'การแข่งขันรถยนต์ระดับตำนานข้ามทวีป ความเร็ว ความท้าทาย และมิตรภาพบนเส้นทางอันตราย',
            'category' => 'แนะนำ',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4'
        ],
        [
            'title' => 'Top Gun: Maverick',
            'description' => 'การหวนคืนสู่สนามบินฝึกของนักบินขับไล่ระดับตำนาน เพื่อภารกิจเสี่ยงตายที่ไม่เคยมีใครทำได้มาก่อน',
            'category' => 'แนะนำ',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
        ],
        [
            'title' => 'Spirited Away (มิติวิญญาณมหัศจรรย์)',
            'description' => 'แอนิเมชันสตูดิโอจิบลิ เรื่องราวของเด็กหญิงที่หลงเข้าไปในโลกของเหล่าภูติผีและเทพเจ้า',
            'category' => 'แนะนำ',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
        ],
        [
            'title' => 'Interstellar (ทะยานดาวผ่าจักรวาล)',
            'description' => 'การเดินทางข้ามรูหนอนเพื่อหาสิ่งมีชีวิตและดาวดวงใหม่ในการอพยพเผ่าพันธุ์มนุษย์',
            'category' => 'แนะนำ',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
        ],
        [
            'title' => 'The Conjuring (คนเรียกผี)',
            'description' => 'ภาพยนตร์สยองขวัญสุดคลาสสิก เรื่องราวของสองสามีภรรยานักปราบผีที่เข้าช่วยครอบครัวในบ้านเฮี้ยน',
            'category' => 'แนะนำ',
            'genre' => 'สยองขวัญ',
            'poster_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
        ],

        // --- หมวด: ยอดนิยม ---
        [
            'title' => 'For Bigger Blazes (ผจญเพลิงมหาภัย)',
            'description' => 'ภาพยนตร์แอคชันสารคดีนำเสนอความกล้าหาญของเหล่านักผจญเพลิงกลางป่าลึกและการรับมือกับภัยธรรมชาติ',
            'category' => 'ยอดนิยม',
            'genre' => 'ระทึกขวัญ',
            'poster_url' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
        ],
        [
            'title' => 'Jujutsu Kaisen 0 (มหาเวทย์ผนึกมาร 0)',
            'description' => 'แอนิเมชันเรื่องราวของยูตะ อคคตสึ และความรักวิญญาณคำสาปที่ร่วมต่อสู้ในโรงเรียนเวทมนตร์',
            'category' => 'ยอดนิยม',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],
        [
            'title' => 'For Bigger Joyrides (ท่องโลกสุดหรรษา)',
            'description' => 'ทริปท่องเที่ยวแบบคาดไม่ถึงของแก๊งเพื่อนสนิท เสียงหัวเราะ รอยยิ้ม และบทเรียนชีวิตสุดประทับใจ',
            'category' => 'ยอดนิยม',
            'genre' => 'คอมเมดี้',
            'poster_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4'
        ],
        [
            'title' => 'The Secret Life of Pets (ชีวิตลับๆ ของสัตว์เลี้ยง)',
            'description' => 'เรื่องราวฮาๆ ของเหล่าสัตว์เลี้ยงแสนรักเมื่อเจ้าของไม่อยู่บ้าน ภารกิจป่วนเมืองที่คุณต้องอมยิ้ม',
            'category' => 'ยอดนิยม',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
        ],
        [
            'title' => 'The Avengers (ดิ อเวนเจอร์ส)',
            'description' => 'การรวมตัวครั้งประวัติศาสตร์ของเหล่าซูเปอร์ฮีโร่เพื่อปกป้องโลกจากการรุกรานของโลกลึกลับ',
            'category' => 'ยอดนิยม',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'
        ],
        [
            'title' => 'One Piece Film: Red',
            'description' => 'แอนิเมชันเสียงเพลงสุดยิ่งใหญ่ของอูตะ เจ้าหญิงแห่งบทเพลงและเรื่องราวตระกูลกลุ่มหมวกฟาง',
            'category' => 'ยอดนิยม',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'
        ],
        [
            'title' => 'Frozen II (ผจญภัยปริศนาราชินีหิมะ 2)',
            'description' => 'แอนิเมชันการเดินทางของอันนาและเอลซ่าสู่ป่าต้องห้ามเพื่อตามหาจุดกำเนิดพลังเวทมนตร์',
            'category' => 'ยอดนิยม',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
        ],
        [
            'title' => 'The Dark Knight (แบทแมน อัศวินรัตติกาล)',
            'description' => 'การเผชิญหน้าระหว่างแบทแมนและโจ๊กเกอร์ ตัวร้ายผู้ต้องการสร้างความโกลาหลทั่วเมืองก็อธแธม',
            'category' => 'ยอดนิยม',
            'genre' => 'ระทึกขวัญ',
            'poster_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
        ],
        [
            'title' => 'Train to Busan (ด่วนนรกซอมบี้คลั่ง)',
            'description' => 'การเอาชีวิตรอดของพ่อและลูกสาวบนรถไฟด่วนมุ่งหน้าสู่ปูซาน ท่ามกลางฝูงซอมบี้กระหายเลือด',
            'category' => 'ยอดนิยม',
            'genre' => 'สยองขวัญ',
            'poster_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],

        // --- หมวด: ได้รางวัล ---
        [
            'title' => 'Tears of Steel (สงครามไซบอร์กวิกฤตโลกา)',
            'description' => 'ภาพยนตร์ไซไฟแนวอนาคตในเมืองอัมสเตอร์ดัม กลุ่มนักต่อสู้พยายามกอบกู้โลกจากกองทัพหุ่นยนต์สังหาร',
            'category' => 'ได้รางวัล',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],
        [
            'title' => 'Coco (วันอลเวงวิญญาณอลเวง)',
            'description' => 'แอนิเมชันยอดเยี่ยมรางวัลออสการ์ เรื่องราวของเด็กหนุ่มผู้หลงรักเสียงเพลงในดินแดนแห่งความตาย',
            'category' => 'ได้รางวัล',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
        ],
        [
            'title' => 'For Bigger Meltdowns (วิกฤตการณ์วันสิ้นโลก)',
            'description' => 'ภาพยนตร์ดราม่าระทึกขวัญ การแย่งชิงทรัพยากรและการเอาชีวิตรอดของมวลมนุษยชาติในยุคน้ำแข็งใหม่',
            'category' => 'ได้รางวัล',
            'genre' => 'ดราม่า',
            'poster_url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4'
        ],
        [
            'title' => 'Spider-Man: Into the Spider-Verse',
            'description' => 'แอนิเมชันยอดเยี่ยมรางวัลออสการ์ จุดเริ่มต้นของไมลส์ โมราเลส สไปเดอร์แมนคนใหม่แห่งนิวยอร์ก',
            'category' => 'ได้รางวัล',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'
        ],
        [
            'title' => 'Interstellar Mission (สำรวจกาแลกซีสุดขอบฟ้า)',
            'description' => 'ภาพยนตร์ยอดเยี่ยมรางวัลออสการ์ การเดินทางข้ามรูหนอนเพื่อหากลุ่มดาวใหม่สำหรับอพยพมนุษยชาติ',
            'category' => 'ได้รางวัล',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],
        [
            'title' => 'Parasite (ชนชั้นปรสิต)',
            'description' => 'ภาพยนตร์ยอดเยี่ยมรางวัลออสการ์ สะท้อนความเหลื่อมล้ำทางสังคมระหว่างสองครอบครัวต่างชนชั้น',
            'category' => 'ได้รางวัล',
            'genre' => 'ดราม่า',
            'poster_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
        ],
        [
            'title' => 'Guillermo del Toro Pinocchio',
            'description' => 'แอนิเมชันสต็อปโมชันชิ้นเอก การตีความพินอคคิโอกับชีวิตในยุคสงครามโลก',
            'category' => 'ได้รางวัล',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
        ],
        [
            'title' => 'Everything Everywhere All at Once',
            'description' => 'ภาพยนตร์คว้า 7 รางวัลออสการ์ หญิงวัยกลางคนผู้ต้องกอบกู้พหุภพและกระชับความสัมพันธ์ครอบครัว',
            'category' => 'ได้รางวัล',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4'
        ],

        // --- หมวด: ทำเงิน ---
        [
            'title' => 'Subaru Outland (ล่าข้ามแผ่นดิน)',
            'description' => 'ภาพยนตร์ทำเงินสูงสุดแห่งปี การไล่ล่าข้ามทวีปของสายลับมือหนึ่งและเครือข่ายก่อการร้ายระดับโลก',
            'category' => 'ทำเงิน',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreet.mp4'
        ],
        [
            'title' => 'The Super Mario Bros. Movie',
            'description' => 'แอนิเมชันทำเงินมหาศาลแห่งปี การผจญภัยของมาริโอและลุยจิในอาณาจักรเห็ดและปราสาทบาวเซอร์',
            'category' => 'ทำเงิน',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'
        ],
        [
            'title' => 'Cyberpunk 2099 (สงครามไฮเทคผ่าเมืองมืด)',
            'description' => 'ภาพยนตร์แอคชันบล็อกบัสเตอร์ เรื่องราวของมือสังหารครึ่งมนุษย์ครึ่งเครื่องจักรในโลกนีออนอนาคต',
            'category' => 'ทำเงิน',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'
        ],
        [
            'title' => 'Minions (มินเนี่ยน)',
            'description' => 'แอนิเมชันตลกทำเงินทั่วโลก การตามหาเจ้านายวายร้ายคนใหม่ของเหล่าน้องเหลืองมินเนี่ยนแสนป่วน',
            'category' => 'ทำเงิน',
            'genre' => 'คอมเมดี้',
            'poster_url' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
        ],
        [
            'title' => 'Sintel (ซินเทล มังกรแห่งหุบเขา)',
            'description' => 'การเดินทางของหญิงสาวผู้ไม่ยอมแพ้เพื่อตามหาลูกมังกรเพื่อนรักที่ถูกลักพาตัวไปในดินแดนหิมะ',
            'category' => 'ทำเงิน',
            'genre' => 'แอนิเมชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'
        ],
        [
            'title' => 'The Lion King (เดอะ ไลออน คิง)',
            'description' => 'แอนิเมชันคลาสสิกทำเงินประวัติศาสตร์ การกลับมาทวงคืนบัลลังก์เจ้าแห่งป่าทุ่งหญ้าสะวันนาของซิมบา',
            'category' => 'ทำเงิน',
            'genre' => 'ครอบครัว',
            'poster_url' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'
        ],
        [
            'title' => 'Avengers: Endgame',
            'description' => 'บทสรุปมหากาพย์ทำเงินอันดับหนึ่งของโลก การย้อนเวลาของเหล่าอเวนเจอร์สเพื่อกอบกู้จักรวาล',
            'category' => 'ทำเงิน',
            'genre' => 'แอคชัน',
            'poster_url' => 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'
        ],
        [
            'title' => 'Avatar (อวตาร)',
            'description' => 'ภาพยนตร์ทำเงินอันดับหนึ่งตลอดกาล มหากาพย์สงครามและเทคโนโลยีภาพ 3D ทะลุมิติบนดาวแพนดอร่า',
            'category' => 'ทำเงิน',
            'genre' => 'ไซไฟ',
            'poster_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'
        ]
    ];

    // ล้างข้อมูลเดิมแล้วลงข้อมูลสื่อใหม่ทั้งหมด
    $pdo->exec("DELETE FROM movies");
    $stmt_ins = $pdo->prepare("INSERT INTO movies (title, description, category, genre, poster_url, video_url) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($sample_items as $item) {
        $stmt_ins->execute([$item['title'], $item['description'], $item['category'], $item['genre'], $item['poster_url'], $item['video_url']]);
    }
    $messages[] = ['type' => 'info', 'text' => 'เพิ่มข้อมูลภาพยนตร์และแอนิเมชันจำนวน ' . count($sample_items) . ' เรื่อง พร้อมประเภท (ครอบครัว, ไซไฟ, สยองขวัญ, ระทึกขวัญ, ฯลฯ) ครบถ้วน'];

} catch (PDOException $e) {
    $status = 'danger';
    $messages[] = ['type' => 'danger', 'text' => 'เกิดข้อผิดพลาดในการติดตั้ง: ' . $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดตั้งฐานข้อมูลอัตโนมัติ - MovieFree</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .setup-card { background: #1e293b; border-radius: 1rem; border: 1px solid #334155; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-5">

<div class="container" style="max-width: 650px;">
    <div class="card setup-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-4 text-warning mb-2"><i class="fa-solid fa-database"></i></div>
            <h2 class="fw-bold text-white">ติดตั้งฐานข้อมูลอัตโนมัติ</h2>
            <p class="text-secondary">ระบบติดตั้งและเตรียมความพร้อมสำหรับ MovieFree</p>
        </div>

        <div class="mb-4">
            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-<?php echo $msg['type'] == 'info' ? 'primary' : $msg['type']; ?> d-flex align-items-center mb-2 fs-6">
                    <?php if ($msg['type'] == 'success'): ?>
                        <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                    <?php elseif ($msg['type'] == 'info'): ?>
                        <i class="fa-solid fa-circle-info me-2 fs-5"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <?php endif; ?>
                    <div><?php echo htmlspecialchars($msg['text']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($status === 'success'): ?>
            <div class="bg-dark p-3 rounded-3 mb-4 border border-secondary">
                <h6 class="text-warning fw-bold mb-2"><i class="fa-solid fa-key me-1"></i> ข้อมูลเข้าสู่ระบบทดสอบ:</h6>
                <div class="row text-white-50 small">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <strong class="text-light">ผู้ดูแลระบบ (Admin):</strong><br>
                        Username: <code class="text-info">admin</code><br>
                        Password: <code class="text-info">admin123</code>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-light">สมาชิกทั่วไป (User):</strong><br>
                        Username: <code class="text-info">user1</code><br>
                        Password: <code class="text-info">user123</code>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="index.php" class="btn btn-outline-light btn-lg px-4 me-md-2"><i class="fa-solid fa-house me-1"></i> ไปหน้าแรก</a>
                <a href="login.php" class="btn btn-warning btn-lg px-4"><i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบทันที</a>
            </div>
        <?php else: ?>
            <div class="d-grid">
                <a href="setup.php" class="btn btn-danger btn-lg"><i class="fa-solid fa-rotate-right me-1"></i> ลองใหม่อีกครั้ง</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
