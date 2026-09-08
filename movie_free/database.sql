-- ==========================================================
-- MovieFree - Free Movie & Animation Streaming Database Dump
-- Database: `movie_db`
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `movie_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `movie_db`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `movies`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'มาใหม่',
  `genre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'ทั่วไป',
  `poster_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Default Users: Admin (admin / admin123) & Member (user1 / user123)
-- --------------------------------------------------------

INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@example.com', '$2y$10$w8.3f68a7F2l5Q5fS4p.2eN1v3O5F8fS4p2eN1v3O5F8fS4p2eN1v', 'ผู้ดูแลระบบสูงสุด', 'admin'),
('user1', 'user1@example.com', '$2y$10$w8.3f68a7F2l5Q5fS4p.2eN1v3O5F8fS4p2eN1v3O5F8fS4p2eN1v', 'สมชาย สายหนัง', 'user')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- --------------------------------------------------------
-- Sample Movies and Animations Data (43 Items)
-- --------------------------------------------------------

INSERT INTO `movies` (`title`, `description`, `category`, `genre`, `poster_url`, `video_url`) VALUES
('Big Buck Bunny (กระต่ายยักษ์ผจญภัย)', 'เรื่องราวของกระต่ายยักษ์น่ารักและสงบสุข ที่ต้องลุกขึ้นมาปกป้องผืนป่าและเหล่าเพื่อนสัตว์จากแก๊งกวนเมือง', 'มาใหม่', 'ครอบครัว', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
('Suzume (การผนึกประตูของซูซุเมะ)', 'แอนิเมชันเรื่องเยี่ยม การเดินทางของเด็กสาวเพื่อปิดประตูมิติที่นำพาภัยพิบัติมาสู่ญี่ปุ่น', 'มาใหม่', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
('For Bigger Escapes (การหลบหนีสุดระทึก)', 'ภาพยนตร์แนวแอคชันผจญภัย การแหกคุกกลางทะเลทรายและภารกิจเอาชีวิตรอดสุดระทึกใจ', 'มาใหม่', 'ระทึกขวัญ', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'),
('Spider-Man: Across the Spider-Verse', 'การท่องพหุภพของไมลส์ โมราเลส พบเจอสไปเดอร์แมนจากหลากหลายมิติทั่วจักรวาล', 'มาใหม่', 'ไซไฟ', 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('Chromecast HD (เปิดมิติใหม่แห่งความบันเทิง)', 'เรื่องราวสารคดีสั้นท่องโลกอนาคต สัมผัสความตระการตาของเทคโนโลยีภาพและเสียงความละเอียดสูง', 'มาใหม่', 'ไซไฟ', 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
('Oppenheimer (ออปเพนไฮเมอร์)', 'เรื่องราวชีวิตของบิดาแห่งระเบิดปรมาณูและการทดลองประวัติศาสตร์ที่จะเปลี่ยนแปลงโลกไปตลอดกาล', 'มาใหม่', 'ดราม่า', 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'),
('Cyberpunk Edgerunners (นักล่าไซเบอร์)', 'แอนิเมชันแนวไซเบอร์พังก์ เรื่องราวของเด็กหนุ่มที่ต้องการเอาชีวิตรอดในเมืองใหญ่อันป่าเถื่อน', 'มาใหม่', 'แอคชัน', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
('Avatar: The Way of Water', 'การหวนคืนสู่ดาวแพนดอร่า ศึกปกป้องมหาสมุทรและครอบครัวจากผู้รุกรานจากต่างดาว', 'มาใหม่', 'ไซไฟ', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'),
('A Quiet Place (ดินแดนไร้เสียง)', 'ครอบครัวที่ต้องเอาชีวิตรอดในโลกซากปรักหักพังโดยห้ามส่งเสียงดังเด็ดขาด สัตว์ประหลาดที่ล่าด้วยเสียง', 'มาใหม่', 'สยองขวัญ', 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('Elephant Dream (ความฝันแห่งโลกอนาคต)', 'การเดินทางสำรวจโลกไซเบอร์เชิงปรัชญาและจักรกลมหัศจรรย์ ระหว่างสองตัวละคร Proog และ Emo', 'แนะนำ', 'ไซไฟ', 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
('Your Name (หลับตาฝัน ถึงชื่อเธอ)', 'แอนิเมชันโรแมนติกแฟนตาซี เรื่องราวของเด็กหนุ่มสาวสองคนที่สลับร่างกันอย่างปริศนา', 'แนะนำ', 'โรแมนติก', 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'),
('For Bigger Fun (ภารกิจสนุกทะลุมิติ)', 'การผจญภัยแฟนตาซีของกลุ่มวัยรุ่นที่ค้นพบประตูมิติโบราณ นำไปสู่โลกแห่งมนตราและสิ่งมีชีวิตอัศจรรย์', 'แนะนำ', 'ครอบครัว', 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'),
('Demon Slayer: Mugen Train (ศึกรถไฟสู่นิรันดร์)', 'แอนิเมชันแอคชันสุดอลังการ ทันจิโร่และเรนโกคุร่วมมือกันต่อสู้กับอสูรบนรถไฟสายมรณะ', 'แนะนำ', 'แอคชัน', 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('We Are Going On Bullrun (ซิ่งทะลุพายุ)', 'การแข่งขันรถยนต์ระดับตำนานข้ามทวีป ความเร็ว ความท้าทาย และมิตรภาพบนเส้นทางอันตราย', 'แนะนำ', 'แอคชัน', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4'),
('Top Gun: Maverick', 'การหวนคืนสู่สนามบินฝึกของนักบินขับไล่ระดับตำนาน เพื่อภารกิจเสี่ยงตายที่ไม่เคยมีใครทำได้มาก่อน', 'แนะนำ', 'แอคชัน', 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
('Spirited Away (มิติวิญญาณมหัศจรรย์)', 'แอนิเมชันสตูดิโอจิบลิ เรื่องราวของเด็กหญิงที่หลงเข้าไปในโลกของเหล่าภูติผีและเทพเจ้า', 'แนะนำ', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
('Interstellar (ทะยานดาวผ่าจักรวาล)', 'การเดินทางข้ามรูหนอนเพื่อหาสิ่งมีชีวิตและดาวดวงใหม่ในการอพยพเผ่าพันธุ์มนุษย์', 'แนะนำ', 'ไซไฟ', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
('The Conjuring (คนเรียกผี)', 'ภาพยนตร์สยองขวัญสุดคลาสสิก เรื่องราวของสองสามีภรรยานักปราบผีที่เข้าช่วยครอบครัวในบ้านเฮี้ยน', 'แนะนำ', 'สยองขวัญ', 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
('For Bigger Blazes (ผจญเพลิงมหาภัย)', 'ภาพยนตร์แอคชันสารคดีนำเสนอความกล้าหาญของเหล่านักผจญเพลิงกลางป่าลึกและการรับมือกับภัยธรรมชาติ', 'ยอดนิยม', 'ระทึกขวัญ', 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
('Jujutsu Kaisen 0 (มหาเวทย์ผนึกมาร 0)', 'แอนิเมชันเรื่องราวของยูตะ อคคตสึ และความรักวิญญาณคำสาปที่ร่วมต่อสู้ในโรงเรียนเวทมนตร์', 'ยอดนิยม', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('For Bigger Joyrides (ท่องโลกสุดหรรษา)', 'ทริปท่องเที่ยวแบบคาดไม่ถึงของแก๊งเพื่อนสนิท เสียงหัวเราะ รอยยิ้ม และบทเรียนชีวิตสุดประทับใจ', 'ยอดนิยม', 'คอมเมดี้', 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4'),
('The Secret Life of Pets (ชีวิตลับๆ ของสัตว์เลี้ยง)', 'เรื่องราวฮาๆ ของเหล่าสัตว์เลี้ยงแสนรักเมื่อเจ้าของไม่อยู่บ้าน ภารกิจป่วนเมืองที่คุณต้องอมยิ้ม', 'ยอดนิยม', 'ครอบครัว', 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
('The Avengers (ดิ อเวนเจอร์ส)', 'การรวมตัวครั้งประวัติศาสตร์ของเหล่าซูเปอร์ฮีโร่เพื่อปกป้องโลกจากการรุกรานของโลกลึกลับ', 'ยอดนิยม', 'แอคชัน', 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4'),
('One Piece Film: Red', 'แอนิเมชันเสียงเพลงสุดยิ่งใหญ่ของอูตะ เจ้าหญิงแห่งบทเพลงและเรื่องราวตระกูลกลุ่มหมวกฟาง', 'ยอดนิยม', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'),
('Frozen II (ผจญภัยปริศนาราชินีหิมะ 2)', 'แอนิเมชันการเดินทางของอันนาและเอลซ่าสู่ป่าต้องห้ามเพื่อตามหาจุดกำเนิดพลังเวทมนตร์', 'ยอดนิยม', 'ครอบครัว', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
('The Dark Knight (แบทแมน อัศวินรัตติกาล)', 'การเผชิญหน้าระหว่างแบทแมนและโจ๊กเกอร์ ตัวร้ายผู้ต้องการสร้างความโกลาหลทั่วเมืองก็อธแธม', 'ยอดนิยม', 'ระทึกขวัญ', 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
('Train to Busan (ด่วนนรกซอมบี้คลั่ง)', 'การเอาชีวิตรอดของพ่อและลูกสาวบนรถไฟด่วนมุ่งหน้าสู่ปูซาน ท่ามกลางฝูงซอมบี้กระหายเลือด', 'ยอดนิยม', 'สยองขวัญ', 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('Tears of Steel (สงครามไซบอร์กวิกฤตโลกา)', 'ภาพยนตร์ไซไฟแนวอนาคตในเมืองอัมสเตอร์ดัม กลุ่มนักต่อสู้พยายามกอบกู้โลกจากกองทัพหุ่นยนต์สังหาร', 'ได้รางวัล', 'ไซไฟ', 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('Coco (วันอลเวงวิญญาณอลเวง)', 'แอนิเมชันยอดเยี่ยมรางวัลออสการ์ เรื่องราวของเด็กหนุ่มผู้หลงรักเสียงเพลงในดินแดนแห่งความตาย', 'ได้รางวัล', 'ครอบครัว', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
('For Bigger Meltdowns (วิกฤตการณ์วันสิ้นโลก)', 'ภาพยนตร์ดราม่าระทึกขวัญ การแย่งชิงทรัพยากรและการเอาชีวิตรอดของมวลมนุษยชาติในยุคน้ำแข็งใหม่', 'ได้รางวัล', 'ดราม่า', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4'),
('Spider-Man: Into the Spider-Verse', 'แอนิเมชันยอดเยี่ยมรางวัลออสการ์ จุดเริ่มต้นของไมลส์ โมราเลส สไปเดอร์แมนคนใหม่แห่งนิวยอร์ก', 'ได้รางวัล', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'),
('Interstellar Mission (สำรวจกาแลกซีสุดขอบฟ้า)', 'ภาพยนตร์ยอดเยี่ยมรางวัลออสการ์ การเดินทางข้ามรูหนอนเพื่อหากลุ่มดาวใหม่สำหรับอพยพมนุษยชาติ', 'ได้รางวัล', 'ไซไฟ', 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('Parasite (ชนชั้นปรสิต)', 'ภาพยนตร์ยอดเยี่ยมรางวัลออสการ์ สะท้อนความเหลื่อมล้ำทางสังคมระหว่างสองครอบครัวต่างชนชั้น', 'ได้รางวัล', 'ดราม่า', 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
('Guillermo del Toro Pinocchio', 'แอนิเมชันสต็อปโมชันชิ้นเอก การตีความพินอคคิโอกับชีวิตในยุคสงครามโลก', 'ได้รางวัล', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'),
('Everything Everywhere All at Once', 'ภาพยนตร์คว้า 7 รางวัลออสการ์ หญิงวัยกลางคนผู้ต้องกอบกู้พหุภพและกระชับความสัมพันธ์ครอบครัว', 'ได้รางวัล', 'ไซไฟ', 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4'),
('Subaru Outland (ล่าข้ามแผ่นดิน)', 'ภาพยนตร์ทำเงินสูงสุดแห่งปี การไล่ล่าข้ามทวีปของสายลับมือหนึ่งและเครือข่ายก่อการร้ายระดับโลก', 'ทำเงิน', 'แอคชัน', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreet.mp4'),
('The Super Mario Bros. Movie', 'แอนิเมชันทำเงินมหาศาลแห่งปี การผจญภัยของมาริโอและลุยจิในอาณาจักรเห็ดและปราสาทบาวเซอร์', 'ทำเงิน', 'ครอบครัว', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4'),
('Cyberpunk 2099 (สงครามไฮเทคผ่าเมืองมืด)', 'ภาพยนตร์แอคชันบล็อกบัสเตอร์ เรื่องราวของมือสังหารครึ่งมนุษย์ครึ่งเครื่องจักรในโลกนีออนอนาคต', 'ทำเงิน', 'ไซไฟ', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4'),
('Minions (มินเนี่ยน)', 'แอนิเมชันตลกทำเงินทั่วโลก การตามหาเจ้านายวายร้ายคนใหม่ของเหล่าน้องเหลืองมินเนี่ยนแสนป่วน', 'ทำเงิน', 'คอมเมดี้', 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'),
('Sintel (ซินเทล มังกรแห่งหุบเขา)', 'การเดินทางของหญิงสาวผู้ไม่ยอมแพ้เพื่อตามหาลูกมังกรเพื่อนรักที่ถูกลักพาตัวไปในดินแดนหิมะ', 'ทำเงิน', 'แอนิเมชัน', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4'),
('The Lion King (เดอะ ไลออน คิง)', 'แอนิเมชันคลาสสิกทำเงินประวัติศาสตร์ การกลับมาทวงคืนบัลลังก์เจ้าแห่งป่าทุ่งหญ้าสะวันนาของซิมบา', 'ทำเงิน', 'ครอบครัว', 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4'),
('Avengers: Endgame', 'บทสรุปมหากาพย์ทำเงินอันดับหนึ่งของโลก การย้อนเวลาของเหล่าอเวนเจอร์สเพื่อกอบกู้จักรวาล', 'ทำเงิน', 'แอคชัน', 'https://images.unsplash.com/photo-1635863138275-d9b33299680b?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4'),
('Avatar (อวตาร)', 'ภาพยนตร์ทำเงินอันดับหนึ่งตลอดกาล มหากาพย์สงครามและเทคโนโลยีภาพ 3D ทะลุมิติบนดาวแพนดอร่า', 'ทำเงิน', 'ไซไฟ', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4');
