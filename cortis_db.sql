-- =====================================================================
-- DATABASE DEFINITION SCRIPT (DDL) & DUMMY DATA FOR CORTIS OFFICIAL WEB
-- Database Name : cortis_db
-- Character Set : utf8mb4
-- Collation     : utf8mb4_unicode_ci
-- Storage Engine: InnoDB
-- =====================================================================

-- 1. PEMBUATAN DATABASE
CREATE DATABASE IF NOT EXISTS `cortis_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `cortis_db`;

-- Nonaktifkan sementara pemeriksaan foreign key untuk pembersihan tabel
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ticket_orders`;
DROP TABLE IF EXISTS `concert_schedules`;
DROP TABLE IF EXISTS `tracks`;
DROP TABLE IF EXISTS `albums`;
DROP TABLE IF EXISTS `videos`;
DROP TABLE IF EXISTS `gallery_photos`;
DROP TABLE IF EXISTS `notices`;
DROP TABLE IF EXISTS `members`;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- 2. DATA DEFINITION LANGUAGE (DDL) - STRUKTUR TABEL
-- =====================================================================

-- ---------------------------------------------------------------------
-- A. Tabel: members (Profil Personil Band - Standalone)
-- ---------------------------------------------------------------------
CREATE TABLE `members` (
    `id` INT AUTO_INCREMENT,
    `stage_name` VARCHAR(50) NOT NULL,
    `role` VARCHAR(100) NOT NULL,
    `bio` TEXT NOT NULL,
    `photo_path` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- B. Tabel: albums (Induk Album - Parent Table)
-- ---------------------------------------------------------------------
CREATE TABLE `albums` (
    `id` VARCHAR(20) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `release_date` DATE NOT NULL,
    `cover_path` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- C. Tabel: tracks (Lagu Album - Child Table dari albums)
-- ---------------------------------------------------------------------
CREATE TABLE `tracks` (
    `id` INT AUTO_INCREMENT,
    `album_id` VARCHAR(20) NOT NULL,
    `track_number` INT NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `duration` VARCHAR(10) NOT NULL,
    `badge` ENUM('Full', 'Preview') DEFAULT 'Full',
    `audio_source` VARCHAR(500) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_tracks_album` 
        FOREIGN KEY (`album_id`) 
        REFERENCES `albums` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- D. Tabel: videos (Katalog Video - Standalone)
-- ---------------------------------------------------------------------
CREATE TABLE `videos` (
    `id` INT AUTO_INCREMENT,
    `title` VARCHAR(150) NOT NULL,
    `source_type` ENUM('youtube', 'local') NOT NULL,
    `video_url` VARCHAR(255) NOT NULL,
    `thumbnail_path` VARCHAR(255) NOT NULL,
    `release_date` DATE NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- E. Tabel: concert_schedules (Jadwal Konser - Parent Table)
-- ---------------------------------------------------------------------
CREATE TABLE `concert_schedules` (
    `id` INT AUTO_INCREMENT,
    `tour_title` VARCHAR(150) NOT NULL,
    `concert_date` DATE NOT NULL,
    `location` VARCHAR(100) NOT NULL,
    `venue` VARCHAR(150) NOT NULL,
    `ticket_status` ENUM('AVAILABLE', 'SOLD OUT') DEFAULT 'AVAILABLE',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- F. Tabel: ticket_orders (Transaksi Tiket - Child Table dari concert_schedules)
-- ---------------------------------------------------------------------
CREATE TABLE `ticket_orders` (
    `id` INT AUTO_INCREMENT,
    `concert_id` INT NOT NULL,
    `fullname` VARCHAR(100) NOT NULL,
    `identity_no` VARCHAR(16) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `ticket_category` VARCHAR(50) NOT NULL,
    `quantity` INT DEFAULT 1,
    `payment_method` VARCHAR(50) NOT NULL,
    `order_status` ENUM('PENDING', 'SUCCESS', 'FAILED') DEFAULT 'PENDING',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_orders_concert` 
        FOREIGN KEY (`concert_id`) 
        REFERENCES `concert_schedules` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- G. Tabel: gallery_photos (Dokumentasi Galeri - Standalone)
-- ---------------------------------------------------------------------
CREATE TABLE `gallery_photos` (
    `id` INT AUTO_INCREMENT,
    `image_path` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- H. Tabel: notices (Pengumuman Resmi - Standalone)
-- ---------------------------------------------------------------------
CREATE TABLE `notices` (
    `id` INT AUTO_INCREMENT,
    `notice_number` INT NOT NULL,
    `category` ENUM('birthday', 'info') NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `notice_date` DATE NOT NULL,
    `content` LONGTEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- 3. DATA MANIPULATION LANGUAGE (DML) - INSERT SAMPLE DUMMY DATA
-- =====================================================================

-- A. Data Personil (members)
INSERT INTO `members` (`stage_name`, `role`, `bio`, `photo_path`) VALUES
('JAMES', 'Leader / Main Vocal', 'James is the calm yet charismatic leader of the group, boasting extraordinary vocal abilities and a nurturing leadership style that protects all members.', 'images/profile/JAMES.jpeg'),
('JUHOON', 'Main Dancer / Visual', 'Known for his precise dance style and sharp, intense gaze on stage, Juhoon is the main visual who always commands attention.', 'images/profile/JUHOON.jpeg'),
('KEONHO', 'Main Rapper', 'Keonho is characterized by his deep rap voice and explosive stage energy. He also frequently produces songs for CORTIS.', 'images/profile/KEONHO.jpeg'),
('MARTIN', 'Lead Vocal', 'An emotional vocalist who serves as the perfect balance in the group\'s songs. Martin is widely recognized for his captivating charm.', 'images/profile/MARTIN.jpeg'),
('SEONGHYEON', 'Maknae / Lead Dancer', 'The youngest member full of smiles and boundless positive energy. Seonghyeon always radiates joy in every performance.', 'images/profile/SEONGHYEON.jpeg');

-- B. Data Album (albums - Induk)
INSERT INTO `albums` (`id`, `title`, `release_date`, `cover_path`) VALUES
('album1', 'GREENGREEN', '2023-11-20', 'images/greengreenalbum.jpg'),
('album2', 'COLOR OUTSIDE THE LINES', '2024-05-15', 'images/whatyouwantalbum.jpg');

-- C. Data Lagu (tracks - Anak dari albums)
INSERT INTO `tracks` (`album_id`, `track_number`, `title`, `duration`, `badge`, `audio_source`) VALUES
-- Tracks Album 1: GREENGREEN
('album1', 1, 'TNT', '2:02', 'Preview', 'https://open.spotify.com/embed/track/7diH1qnFziArYzynk3vkr6?utm_source=generator&si=3747db085eae4ab3'),
('album1', 2, 'REDRED', '2:43', 'Full', 'musics/CORTIS (코르티스) \'REDRED\'.mp3'),
('album1', 3, 'ACAI', '2:53', 'Full', 'musics/acai.mp3'),
('album1', 4, 'YOUNGCREATORCREW', '2:56', 'Preview', 'https://open.spotify.com/embed/track/2vKNzC9T3kdU2JECcvob3V?utm_source=generator&si=8c17dd8494444c5d'),
('album1', 5, 'Wassup', '3:04', 'Preview', 'https://open.spotify.com/embed/track/3dCdcURa0Kk5UWXRY00HQy?utm_source=generator&si=2d27e866c9de46da'),
('album1', 6, 'Blue Lips', '2:21', 'Full', 'musics/blue_lips.mp3'),
-- Tracks Album 2: COLOR OUTSIDE THE LINES
('album2', 1, 'GO!', '2:50', 'Full', 'musics/CORTIS (코르티스) \'GO!\'.mp3'),
('album2', 2, 'What You Want', '3:14', 'Full', 'musics/CORTIS (코르티스) \'What You Want\'.mp3'),
('album2', 3, 'FaSHioN', '2:54', 'Full', 'musics/FaSHioN.mp3'),
('album2', 4, 'JoyRide', '2:37', 'Preview', 'https://open.spotify.com/embed/track/6T17ZI0glfM2IGLdUwtnLK?utm_source=generator&si=dddc291385974113'),
('album2', 5, 'Lullaby', '2:44', 'Preview', 'https://open.spotify.com/embed/track/0nyhsAcyDj533QrXa4hUug?utm_source=generator&si=75b3ae64906042d2');

-- D. Data Video (videos)
INSERT INTO `videos` (`title`, `source_type`, `video_url`, `thumbnail_path`, `release_date`) VALUES
('ME+YOU Official MV', 'youtube', 'https://www.youtube.com/embed/nCElUD0jvgo', 'thumnails/mayyou.jpeg', '2025-10-10'),
('SHOOT (Firecracker) Official MV', 'youtube', 'https://www.youtube.com/embed/kRpaqR5sbf0', 'thumnails/shoot.jpeg', '2025-08-12'),
('ACAI Local MV', 'local', 'videos/CORTIS (코르티스) \'ACAI\' Official MV.mp4', 'thumnails/acai.jpeg', '2025-05-01');

-- E. Data Jadwal Konser (concert_schedules - Induk)
INSERT INTO `concert_schedules` (`tour_title`, `concert_date`, `location`, `venue`, `ticket_status`) VALUES
('World Tour: "The First Chapter"', '2026-07-10', 'SEOUL, KOREA', 'OLYMPIC HALL', 'SOLD OUT'),
('World Tour: "The First Chapter"', '2026-07-11', 'SEOUL, KOREA', 'OLYMPIC HALL', 'SOLD OUT'),
('World Tour: "The First Chapter"', '2026-08-21', 'TOKYO, JAPAN', 'TOKYO DOME', 'AVAILABLE'),
('World Tour: "The First Chapter"', '2026-08-31', 'JAKARTA, INDONESIA', 'ICE BSD CITY', 'AVAILABLE'),
('World Tour: "The First Chapter"', '2026-09-15', 'BANGKOK, THAILAND', 'IMPACT ARENA', 'AVAILABLE');

-- F. Data Pemesanan Tiket (ticket_orders - Anak dari concert_schedules)
INSERT INTO `ticket_orders` (`concert_id`, `fullname`, `identity_no`, `email`, `phone`, `ticket_category`, `quantity`, `payment_method`, `order_status`) VALUES
(3, 'Andi Pratama', '3171012304950001', 'andi.pratama@example.com', '081234567890', 'VIP Soundcheck', 2, 'Credit Card', 'SUCCESS'),
(4, 'Siti Nurhaliza', '3273014508980002', 'siti.nur@example.com', '082198765432', 'CAT 1 Standing', 1, 'Virtual Account BCA', 'SUCCESS'),
(4, 'Budi Santoso', '3578021201990003', 'budi.santoso@example.com', '085712341234', 'CAT 2 Seated', 2, 'GoPay', 'PENDING'),
(5, 'Jessica Tan', '3175035511000004', 'jessica.tan@example.com', '081388997766', 'VIP Festival', 1, 'Credit Card', 'FAILED');

-- G. Data Galeri Foto (gallery_photos)
INSERT INTO `gallery_photos` (`image_path`, `caption`) VALUES
('images_gallery/1.jpg', 'World Tour The First Chapter - Opening Seoul'),
('images_gallery/2.jpg', 'Backstage Moment with Members'),
('images_gallery/3.jpg', 'Juhoon Dance Solo Performance'),
('images_gallery/4.jpg', 'Rehearsal Soundcheck Live'),
('images_gallery/5.jpg', 'Encore Stage Crowd Interaction'),
('images_gallery/6.jpg', 'Press Conference & Media Showcase'),
('images_gallery/7.jpg', 'Record Shop Concept Shoot'),
('images_gallery/8.jpg', 'Behind The Scenes Acoustic Jam'),
('images_gallery/9.jpg', 'Outdoor Music Video Filming'),
('images_gallery/10.jpg', 'Album Concept Photo Shoot'),
('images_gallery/11.jpg', 'Street Casual Studio Session'),
('images_gallery/12.jpg', 'Studio Portrait Session'),
('images_gallery/13.jpg', 'Sports Club Concept Editorial'),
('images_gallery/14.jpg', 'Graffiti Wall Street Vibe'),
('images_gallery/The Pirlos.jpg', 'The Pirlos Outdoor Moment'),
('images_gallery/15.jpg', 'Stairway High Fashion Poses'),
('images_gallery/16.jpg', 'Stairway Selfie with Fans'),
('images_gallery/17.jpg', 'Choreography Rehearsal Hall'),
('images_gallery/18.jpg', 'Live Sound Check & Warmup'),
('images_gallery/19.jpg', 'Festival Stage Performance'),
('images_gallery/20.jpg', 'Backstage Warm Hugs & High Fives'),
('images_gallery/21.jpg', 'Fan Meeting Sign Event'),
('images_gallery/22.jpg', 'Stage Lights & Crowd Cheers'),
('images_gallery/23.jpg', 'Studio Recording Session'),
('images_gallery/24.jpg', 'Night Street Casual Walks'),
('images_gallery/25.jpg', 'Dressing Room Candid Moment'),
('images_gallery/28.jpg', 'Acoustic Live Session Behind'),
('images_gallery/27.jpg', 'Sunset Concert Encore'),
('images_gallery/30.jpg', 'World Tour Finale Celebration'),
('images_gallery/31.jpg', 'Cortis With Global Fans'),
('images_gallery/26.jpg', 'Midnight Rehearsal Break'),
('images_gallery/29.jpg', 'Backstage Group Photo Before Show'),
('images_gallery/photo_1794723340_hamgardner2.jpg', 'Ham Gardner'),
('images_gallery/photo_1794723354_hamgardner2.jpg', 'Ham Gardner');

-- H. Data Pengumuman (notices)
INSERT INTO `notices` (`notice_number`, `category`, `title`, `notice_date`, `content`) VALUES
(109, 'birthday', 'Happy Birthday JAMES', '2026-06-15', '<p>Happy Birthday to <strong>JAMES</strong>, Leader and Main Vocalist of CORTIS! Let''s celebrate this special day together by sending our warmest wishes and love.</p><p>May James always be healthy, happy, and continue to lead CORTIS with inspiration to a bigger world stage. Don''t forget to share your birthday wishes using the official hashtags: <strong>#HappyJamesDay #CORTISLeader</strong>.</p>'),
(108, 'info', 'CORTIS World Tour <THE FIRST CHAPTER> Ticket Open Info', '2026-06-01', '<p>Hello, this is CORTIS Official Staff. We are pleased to announce the official ticket sale schedule for <strong>CORTIS World Tour &lt;THE FIRST CHAPTER&gt;</strong>.</p><p>Please make sure to prepare your verified fanclub membership and identity card prior to ticketing.</p>');


-- =====================================================================
-- 4. CONTOH QUERY PENGUJIAN RELASI (JOIN TESTING)
-- =====================================================================

-- Test JOIN 1: Relasi albums -> tracks (Menampilkan Album beserta daftar lagunya)
-- SELECT 
--     a.title AS album_title,
--     a.release_date,
--     t.track_number,
--     t.title AS track_title,
--     t.duration,
--     t.badge
-- FROM albums a
-- INNER JOIN tracks t ON a.id = t.album_id
-- ORDER BY a.id, t.track_number;

-- Test JOIN 2: Relasi concert_schedules -> ticket_orders (Menampilkan Jadwal Konser dan Data Transaksi Tiket)
-- SELECT 
--     c.tour_title,
--     c.concert_date,
--     c.location,
--     c.venue,
--     o.fullname,
--     o.ticket_category,
--     o.quantity,
--     o.payment_method,
--     o.order_status
-- FROM concert_schedules c
-- INNER JOIN ticket_orders o ON c.id = o.concert_id
-- ORDER BY c.concert_date ASC, o.created_at DESC;
