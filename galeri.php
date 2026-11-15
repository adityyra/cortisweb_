<?php
require_once __DIR__ . '/db_connect.php';

// Fetch gallery photos with captions from database
$allPhotos = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $res = $conn->query("SELECT * FROM `gallery_photos` ORDER BY `id` ASC");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $allPhotos[] = [
                'id'         => (int)$r['id'],
                'image_path' => $r['image_path'],
                'caption'    => $r['caption'] ?? ''
            ];
        }
    }
}

// Fallback if database has no photos
if (empty($allPhotos)) {
    $defaultPhotos = [
        ['id' => 1, 'image_path' => 'images_gallery/1.jpg', 'caption' => 'World Tour The First Chapter - Opening Seoul'],
        ['id' => 2, 'image_path' => 'images_gallery/2.jpg', 'caption' => 'Backstage Moment with Members'],
        ['id' => 3, 'image_path' => 'images_gallery/3.jpg', 'caption' => 'Juhoon Dance Solo Performance'],
        ['id' => 4, 'image_path' => 'images_gallery/4.jpg', 'caption' => 'Rehearsal Soundcheck Live'],
        ['id' => 5, 'image_path' => 'images_gallery/5.jpg', 'caption' => 'Encore Stage Crowd Interaction'],
        ['id' => 6, 'image_path' => 'images_gallery/6.jpg', 'caption' => 'Press Conference & Media Showcase'],
        ['id' => 7, 'image_path' => 'images_gallery/7.jpg', 'caption' => 'Record Shop Concept Shoot'],
        ['id' => 8, 'image_path' => 'images_gallery/8.jpg', 'caption' => 'Behind The Scenes Acoustic Jam'],
        ['id' => 9, 'image_path' => 'images_gallery/9.jpg', 'caption' => 'Outdoor Music Video Filming'],
        ['id' => 10, 'image_path' => 'images_gallery/10.jpg', 'caption' => 'Album Concept Photo Shoot'],
        ['id' => 11, 'image_path' => 'images_gallery/11.jpg', 'caption' => 'Street Casual Studio Session'],
        ['id' => 12, 'image_path' => 'images_gallery/12.jpg', 'caption' => 'Studio Portrait Session'],
        ['id' => 13, 'image_path' => 'images_gallery/13.jpg', 'caption' => 'Sports Club Concept Editorial'],
        ['id' => 14, 'image_path' => 'images_gallery/14.jpg', 'caption' => 'Graffiti Wall Street Vibe'],
        ['id' => 15, 'image_path' => 'images_gallery/The Pirlos.jpg', 'caption' => 'The Pirlos Outdoor Moment'],
        ['id' => 16, 'image_path' => 'images_gallery/15.jpg', 'caption' => 'Stairway High Fashion Poses'],
        ['id' => 17, 'image_path' => 'images_gallery/16.jpg', 'caption' => 'Stairway Selfie with Fans'],
        ['id' => 18, 'image_path' => 'images_gallery/17.jpg', 'caption' => 'Choreography Rehearsal Hall'],
    ];
    $allPhotos = $defaultPhotos;
}

// Split into pages of 18 photos each (6 columns x 3 photos)
$perPage = 18;
$pages = array_chunk($allPhotos, $perPage);
$totalPages = count($pages) ?: 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery & Fans – Cortis Official</title>
    <meta name="description"
        content="Galeri foto dan media sosial Cortis Official. Temukan koleksi foto eksklusif dan ikuti kami di semua platform sosial.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Outfit:wght@300;400;500;600;700;800;900&family=Syne:wght@700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- MAIN GRID CONTAINER -->
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
                        <a href="videos.php">VIDEOS</a>
                    </div>
                </div>
                <a href="jadwal_konser.php">TOUR</a>
                <a href="galeri.php" class="active">GALLERY</a>
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
            <div class="side-number">06</div>
            <div class="side-line"></div>
            <div class="side-text">GALLERY</div>
        </div>

        <!-- PAGE TITLE HERO -->
        <div class="page-title-hero">
            <h1>GALLERY</h1>
        </div>

        <!-- GALLERY SECTION (DYNAMIC WITH DB PHOTOS & CAPTIONS) -->
        <section class="gallery-section" id="gallery-section">
            <?php foreach ($pages as $pIdx => $pageItems): 
                $pageNum = $pIdx + 1;
            ?>
                <div class="gallery-grid gallery-page <?php echo ($pageNum === 1) ? 'active' : ''; ?>" id="galleryGrid<?php echo $pageNum; ?>" style="<?php echo ($pageNum === 1) ? '' : 'display: none;'; ?>">
                    <?php foreach ($pageItems as $iIdx => $photo): ?>
                        <div class="gallery-item" onclick="openLightbox('<?php echo htmlspecialchars($photo['image_path']); ?>', '<?php echo htmlspecialchars(addslashes($photo['caption'])); ?>')">
                            <img src="<?php echo htmlspecialchars($photo['image_path']); ?>" alt="<?php echo htmlspecialchars($photo['caption'] ?: 'Concert Photo ' . ($iIdx + 1)); ?>" loading="lazy" onerror="this.src='images_gallery/1.jpg';">

                            <!-- Caption Overlay -->
                            <?php if (!empty($photo['caption'])): ?>
                                <div class="gallery-caption-overlay">
                                    <span class="gallery-caption-text"><?php echo htmlspecialchars($photo['caption']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <!-- PAGINATION -->
            <div class="pagination">
                <a href="javascript:void(0)" class="page-arrow" onclick="changeGalleryPage(-1)">&#10094;</a>
                <span class="page-numbers"><strong id="currentPageNum">1</strong> / <?php echo $totalPages; ?></span>
                <a href="javascript:void(0)" class="page-arrow" onclick="changeGalleryPage(1)">&#10095;</a>
            </div>
        </section>

        <!-- LIGHTBOX MODAL -->
        <div class="gallery-lightbox" id="galleryLightbox" onclick="closeLightbox(event)">
            <div class="lightbox-content">
                <button class="lightbox-close" onclick="closeLightbox(event)">&times;</button>
                <img src="" alt="Enlarged Photo" class="lightbox-img" id="lightboxImg">
                <div class="lightbox-caption-bar" id="lightboxCaption"></div>
            </div>
        </div>

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
        let currentGalleryPage = 1;
        const totalGalleryPages = <?php echo $totalPages; ?>;

        function changeGalleryPage(direction) {
            let newPage = currentGalleryPage + direction;
            if (newPage < 1) newPage = totalGalleryPages;
            if (newPage > totalGalleryPages) newPage = 1;

            for (let i = 1; i <= totalGalleryPages; i++) {
                const el = document.getElementById('galleryGrid' + i);
                if (el) {
                    el.style.display = 'none';
                    el.classList.remove('active');
                }
            }

            const target = document.getElementById('galleryGrid' + newPage);
            if (target) {
                target.style.display = '';
                target.classList.add('active');
                document.getElementById('currentPageNum').innerText = newPage;
                currentGalleryPage = newPage;
            }
        }

        // Lightbox Functions
        function openLightbox(src, caption) {
            const lb = document.getElementById('galleryLightbox');
            const img = document.getElementById('lightboxImg');
            const cap = document.getElementById('lightboxCaption');
            img.src = src;
            cap.innerText = caption || 'CORTIS Official Gallery';
            lb.classList.add('active');
            document.body.classList.add('no-scroll');
        }

        function closeLightbox(event) {
            if (event.target.id === 'galleryLightbox' || event.target.classList.contains('lightbox-close')) {
                const lb = document.getElementById('galleryLightbox');
                lb.classList.remove('active');
                document.body.classList.remove('no-scroll');
            }
        }

        // Keyboard ESC to close lightbox
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const lb = document.getElementById('galleryLightbox');
                if (lb && lb.classList.contains('active')) {
                    lb.classList.remove('active');
                    document.body.classList.remove('no-scroll');
                }
            }
        });

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
    </script>
</body>

</html>
