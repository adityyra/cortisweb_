<?php
require_once __DIR__ . '/db_connect.php';

// Fetch videos from database
$videos = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $res = $conn->query("SELECT * FROM `videos` ORDER BY `release_date` DESC, `id` DESC");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $videos[] = $r;
        }
    }
}

// Fallback dummy videos if DB empty
if (empty($videos)) {
    $videos = [
        ['id' => 1, 'title' => 'ME+YOU Official MV', 'source_type' => 'youtube', 'video_url' => 'https://www.youtube.com/embed/nCElUD0jvgo', 'thumbnail_path' => 'thumnails/mayyou.jpeg', 'release_date' => '2025-10-10'],
        ['id' => 2, 'title' => 'SHOOT (Firecracker) Official MV', 'source_type' => 'youtube', 'video_url' => 'https://www.youtube.com/embed/kRpaqR5sbf0', 'thumbnail_path' => 'thumnails/shoot.jpeg', 'release_date' => '2025-08-12'],
    ];
}

$firstVideo = $videos[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video – Cortis Official</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Outfit:wght@300;400;500;600;700;800;900&family=Syne:wght@700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .video-main-layout {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .video-main-layout .main-video {
            grid-column: 1 / -1;
            width: 100%;
            position: relative;
        }

        .video-card {
            width: 100%;
            position: relative;
        }

        .video-card iframe, .video-card video {
            width: 100%;
            aspect-ratio: 16 / 9;
            display: block;
            border-radius: 8px;
            border: none;
            background: #000;
        }

        .video-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85em;
            color: var(--text-secondary);
            margin-top: 8px;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
        }

        .video-info .title {
            font-weight: 700;
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .video-main-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <header>
            <div class="logo-container">
                <a href="index.php" class="profile-logo" style="text-decoration: none;">
                    <img src="images/CORTISLOGO.png" alt="Cortis Logo">
                </a>
                <a href="index.php" class="logo-brand">CORTIS</a>
            </div>
            <nav class="nav-menu" id="navMenu">
                <a href="index.php">HOME</a>
                <a href="about.php">ABOUT</a>
                <div class="dropdown">
                    <a href="music.php">MUSIC</a>
                    <div class="dropdown-content">
                        <a href="videos.php" class="active">VIDEOS</a>
                    </div>
                </div>
                <a href="jadwal_konser.php">TOUR</a>
                <a href="galeri.php">GALLERY</a>
                <a href="notice.php">NOTICE</a>
            </nav>
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </header>

        <!-- SIDE INDICATOR -->
        <div class="side-indicator">
            <div class="side-number">04</div>
            <div class="side-line"></div>
            <div class="side-text">VIDEO</div>
        </div>

        <!-- PAGE TITLE HERO -->
        <div class="page-title-hero" style="display: flex; justify-content: center; width: 100%;">
            <h1 style="text-align: center;">VIDEO</h1>
        </div>

        <!-- VIDEO SECTION -->
        <section class="gallery-section" id="video-section">
            <div class="video-main-layout" id="videoGrid1">
                <!-- Large Video (Top) -->
                <div class="video-card main-video">
                    <iframe id="mainVideoPlayer" src="<?php echo htmlspecialchars($firstVideo['video_url']); ?>"
                        title="<?php echo htmlspecialchars($firstVideo['title']); ?>"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                </div>

                <!-- Video Cards (DYNAMIC FROM DATABASE) -->
                <?php foreach ($videos as $v): 
                    $rawUrl = $v['video_url'];
                    // extract embed URL
                    $embedUrl = $rawUrl;
                    if (strpos($rawUrl, 'watch?v=') !== false) {
                        $parts = explode('watch?v=', $rawUrl);
                        $embedUrl = 'https://www.youtube.com/embed/' . $parts[1];
                    }
                ?>
                    <div class="video-card" onclick="playInMain('<?php echo addslashes($embedUrl); ?>')"
                        style="cursor: pointer; transition: transform 0.2s;"
                        onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; aspect-ratio: 16/9; z-index: 10;">
                        </div>
                        <iframe src="<?php echo htmlspecialchars($embedUrl); ?>" title="<?php echo htmlspecialchars($v['title']); ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen style="pointer-events: none;"></iframe>
                        <div class="video-info">
                            <span class="title"><?php echo htmlspecialchars($v['title']); ?></span>
                            <span><?php echo htmlspecialchars($v['release_date']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </section>

        <!-- FOOTER -->
        <footer>
            <div class="footer-social">
                <a href="https://www.instagram.com/cortis/" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"><img src="images/instagram.png" alt="Instagram"
                        class="footer-icon" style="filter: none;"> INSTAGRAM</a>
                <a href="https://www.tiktok.com/@cortis_bighit" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"><img src="images/tiktok.png" alt="TikTok"
                        class="footer-icon" style="filter: none;"> TIKTOK</a>
                <a href="https://x.com/cortis_bighit" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"><img src="images/x.png" alt="X"
                        class="footer-icon" style="filter: none;" > X</a>
                <a href="https://youtube.com/@cortis_bighit?si=zvE_dOeq7-w3zoBZ" target="_blank"
                    style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"
                    onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                    <img src="images/Youtube.png" alt="Youtube" class="footer-icon" style="filter: none;"> YOUTUBE
                </a>
            </div>
            <div class="footer-bottom">
                &copy; 2024 CORTIS OFFICIAL. PRIVACY | CONTACT
            </div>
        </footer>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');

        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        });

        const navLinks = document.querySelectorAll('.nav-menu a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('no-scroll');
            });
        });

        function playInMain(url) {
            const mainVideo = document.getElementById('mainVideoPlayer');
            let playUrl = url;
            if (playUrl.includes('?')) {
                playUrl += '&autoplay=1';
            } else {
                playUrl += '?autoplay=1';
            }
            mainVideo.src = playUrl;
            mainVideo.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
</body>

</html>
