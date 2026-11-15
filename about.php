<?php
require_once __DIR__ . '/db_connect.php';

// Fetch members dynamically from database
$members = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $res = $conn->query("SELECT * FROM `members` ORDER BY `id` ASC");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $members[] = $r;
        }
    }
}

// Fallback if DB is empty or not yet connected
if (empty($members)) {
    $members = [
        ['id' => 1, 'stage_name' => 'JAMES', 'role' => 'Leader / Main Vocal', 'bio' => 'James is the calm yet charismatic leader of the group, boasting extraordinary vocal abilities and a nurturing leadership style that protects all members.', 'photo_path' => 'images/profile/JAMES.jpeg'],
        ['id' => 2, 'stage_name' => 'JUHOON', 'role' => 'Main Dancer / Visual', 'bio' => 'Known for his precise dance style and sharp, intense gaze on stage, Juhoon is the main visual who always commands attention.', 'photo_path' => 'images/profile/JUHOON.jpeg'],
        ['id' => 3, 'stage_name' => 'KEONHO', 'role' => 'Main Rapper', 'bio' => 'Keonho is characterized by his deep rap voice and explosive stage energy. He also frequently produces songs for CORTIS.', 'photo_path' => 'images/profile/KEONHO.jpeg'],
        ['id' => 4, 'stage_name' => 'MARTIN', 'role' => 'Lead Vocal', 'bio' => 'An emotional vocalist who serves as the perfect balance in the group\'s songs. Martin is widely recognized for his captivating charm.', 'photo_path' => 'images/profile/MARTIN.jpeg'],
        ['id' => 5, 'stage_name' => 'SEONGHYEON', 'role' => 'Maknae / Lead Dancer', 'bio' => 'The youngest member full of smiles and boundless positive energy. Seonghyeon always radiates joy in every performance.', 'photo_path' => 'images/profile/SEONGHYEON.jpeg'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang CORTIS - Profile Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Outfit:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="about.css">
</head>

<body>

    <div class="wireframe-container">

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
                <a href="about.php" class="active">ABOUT</a>
                <div class="dropdown">
                    <a href="music.php">MUSIC</a>
                    <div class="dropdown-content">
                        <a href="videos.php">VIDEOS</a>
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
            <div class="side-number">02</div>
            <div class="side-line"></div>
            <div class="side-text">ABOUT</div>
        </div>

        <main>

            <!-- PROFILE & HISTORY -->
            <section class="profile-section" id="profile-section">
                <div class="profile-image">
                    <img src="https://images.prestigeonline.com/wp-content/uploads/sites/3/2025/12/23213149/Cortis-1600x900.jpg"
                        alt="CORTIS Band">
                </div>
                <div class="profile-text">
                    <h2>About Cortis :</h2>
                    <p>CORTIS is a new generation boygroup that debuted with a concept of boundless energy and freedom. Formed by extraordinary talents with different backgrounds, they are united by a vision to create music that speaks the truth and inspires the younger generation.</p>
                    <p>Blending Pop, R&B, and modern EDM elements, CORTIS is known for their addictive melodies, powerful vocals, and sharp, captivating choreography. They continue to prove themselves as future icons in the global music industry, bringing an authentic charm to every masterpiece they create.</p>
                </div>
            </section>

            <!-- MEMBER PROFILE (DYNAMIC FROM DATABASE) -->
            <section class="members-section" id="members-section"
                style="position: relative; padding-top: 50px; margin-top: 60px;">
                <h2 class="section-title">Members Profile</h2>
                <div class="interactive-profile">
                    <div class="profile-sidebar">
                        <?php foreach ($members as $idx => $m): ?>
                            <button class="nav-member <?php echo ($idx === 0) ? 'active' : ''; ?>" data-id="<?php echo $m['id']; ?>" onclick="changeMember(<?php echo $m['id']; ?>)">
                                <?php echo htmlspecialchars($m['stage_name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="profile-content-area">
                        <div class="profile-photo-wrapper">
                            <div class="profile-photo-bg"></div>
                            <?php foreach ($members as $idx => $m): ?>
                                <img id="photo-<?php echo $m['id']; ?>" src="<?php echo htmlspecialchars($m['photo_path']); ?>" class="profile-photo <?php echo ($idx === 0) ? 'active' : ''; ?>" alt="<?php echo htmlspecialchars($m['stage_name']); ?>" onerror="this.src='images/profile/JAMES.jpeg';">
                            <?php endforeach; ?>
                        </div>

                        <?php foreach ($members as $idx => $m): ?>
                            <div id="detail-<?php echo $m['id']; ?>" class="profile-detail <?php echo ($idx === 0) ? 'active' : ''; ?>">
                                <h1><?php echo htmlspecialchars($m['stage_name']); ?></h1>
                                <div class="role"><?php echo htmlspecialchars($m['role']); ?></div>
                                <p><?php echo nl2br(htmlspecialchars($m['bio'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        </main>

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
        function changeMember(id) {
            // Update active nav button
            const navs = document.querySelectorAll('.nav-member');
            navs.forEach((nav) => {
                if (nav.getAttribute('data-id') == id) {
                    nav.classList.add('active');
                } else {
                    nav.classList.remove('active');
                }
            });

            // Update photo
            const photos = document.querySelectorAll('.profile-photo');
            photos.forEach(photo => photo.classList.remove('active'));
            const activePhoto = document.getElementById('photo-' + id);
            if (activePhoto) activePhoto.classList.add('active');

            // Update detail text
            const details = document.querySelectorAll('.profile-detail');
            details.forEach(detail => detail.classList.remove('active'));
            const activeDetail = document.getElementById('detail-' + id);
            if (activeDetail) activeDetail.classList.add('active');
        }

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
