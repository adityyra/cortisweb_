<?php
require_once __DIR__ . '/db_connect.php';

// Fetch the nearest upcoming concert schedule
$upcomingConcert = null;
if (isset($conn) && $conn && !$conn->connect_error) {
    $resUp = $conn->query("SELECT * FROM `concert_schedules` WHERE `ticket_status` = 'AVAILABLE' ORDER BY `concert_date` ASC LIMIT 1");
    if ($resUp && $rowUp = $resUp->fetch_assoc()) {
        $upcomingConcert = $rowUp;
    }
}

// Fallback if none found
if (!$upcomingConcert) {
    $upcomingConcert = [
        'location' => 'TOKYO, JAPAN',
        'tour_title' => 'World Tour: "The First Chapter"',
        'concert_date' => '2026-08-21',
        'venue' => 'TOKYO DOME'
    ];
}
$upDateFormatted = date("M d", strtotime($upcomingConcert['concert_date']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortis Official</title>
    <!-- Load Oswald font to match the image font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Inter:wght@400&family=Syne:wght@800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>

<body>

    <!-- Header / Navigation Bar -->
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
        <div class="side-number">01</div>
        <div class="side-line"></div>
        <div class="side-text">HOME</div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="main">
            <div class="hero-content">
                <!-- Main Title -->
                <h1 class="main-title">CORTIS</h1>
                <p class="hero-tagline">YOUTH IS TOO WILD TO STAY<br>INSIDE THE LINES.</p>
                <p class="hero-desc">
                    Meet the 5th generation creators who write their own songs, build their own world, and speak their
                    own unfiltered truth.
                </p>

                <!-- Action Button -->
                <a href="about.php" class="bio-btn">SEE THEIR BIO</a>
            </div>
        </div>
        <div class="scroll-content-area">

            <!-- UPCOMING TOUR BANNER (DYNAMIC FROM DATABASE) -->
            <div class="tour-banner" style="max-width: 1200px; width: 100%; margin: 0 auto 40px auto; background: linear-gradient(135deg, #e60000 0%, #990000 100%); color: white; padding: 30px 40px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: 0 10px 30px rgba(230, 0, 0, 0.2);">
                <div>
                    <span style="display: inline-block; background: #fff; color: #e60000; font-size: 0.75rem; font-weight: bold; padding: 4px 10px; border-radius: 20px; margin-bottom: 10px; letter-spacing: 1px;">UPCOMING TOUR</span>
                    <h2 style="margin: 0; font-family: 'Syne', sans-serif; font-size: 1.8rem; letter-spacing: 1px; text-transform: uppercase;">
                        <?php echo htmlspecialchars($upcomingConcert['location']); ?>
                    </h2>
                    <p style="margin: 5px 0 0 0; font-size: 1.05rem; opacity: 0.9; font-weight: 300;">
                        <?php echo htmlspecialchars($upcomingConcert['tour_title']); ?> &nbsp;|&nbsp; <?php echo $upDateFormatted; ?> &nbsp;|&nbsp; <?php echo htmlspecialchars($upcomingConcert['venue']); ?>
                    </p>
                </div>
                <a href="jadwal_konser.php" style="background: white; color: #e60000; padding: 14px 28px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'">Get Tickets</a>
            </div>

            <!-- AREA AFTER SCROLL: MUSIC & VIDEO SECTION -->
            <div class="media-section">
                <!-- Music Section -->
                <div class="music-block">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0;">Popular Music</h2>
                        <a href="music.php" style="color: #fff; text-decoration: none; font-family: 'Inter', sans-serif; font-weight: bold; font-size: 0.9rem; letter-spacing: 1px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">VIEW ALL &rarr;</a>
                    </div>
                    <div class="music-grid">
                        <!-- Local Audio Player 1: REDRED -->
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 450px;">
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px;">CORTIS
                                (코르티스) 'REDRED'</h3>
                            <div class="media-card"
                                style="padding: 25px 20px; border-radius: 8px; background: #111; border: 1px solid #333; height: auto; flex-direction: column; justify-content: center; align-items: center; gap: 20px;">
                                <div class="vinyl-record" id="vinylRecord" style="margin-bottom: 5px;">
                                    <img src="thumnails music/thumnail music 1.jpg" alt="Album Art">
                                    <div class="vinyl-hole"></div>
                                </div>

                                <!-- Progress Bar -->
                                <input type="range" id="progressBar" value="0" min="0" max="100"
                                    style="width: 100%; cursor: pointer; accent-color: #e60000; height: 4px; margin-bottom: 5px; background: #333; outline: none; border-radius: 2px;">

                                <!-- Controls Row -->
                                <div
                                    style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;"></div>
                                    <button id="playPauseBtn"
                                        style="background: #ffffff; border: none; width: 55px; height: 55px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: transform 0.2s ease, background 0.2s; box-shadow: 0 4px 12px rgba(255,255,255,0.2);"
                                        onmouseover="this.style.transform='scale(1.1)'; this.style.background='#f0f0f0'"
                                        onmouseout="this.style.transform='scale(1)'; this.style.background='#ffffff'">
                                        <!-- Play Icon -->
                                        <svg id="playIcon" viewBox="0 0 24 24" width="28" height="28" fill="#000000"
                                            style="margin-left: 4px;">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        <!-- Pause Icon -->
                                        <svg id="pauseIcon" viewBox="0 0 24 24" width="28" height="28" fill="#000000"
                                            style="display: none;">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                                        </svg>
                                    </button>
                                    <div style="flex: 1; display: flex; justify-content: flex-end;">
                                        <span id="timeDisplay"
                                            style="color: #ccc; font-size: 0.95rem; font-family: monospace; letter-spacing: 1px; font-weight: bold;">0:00
                                             / 0:00</span>
                                    </div>
                                    <audio id="audioPlayer" style="display: none;">
                                        <source src="musics/CORTIS (코르티스) 'REDRED'.mp3" type="audio/mpeg">
                                    </audio>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Player 2: GO! -->
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 450px;">
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px;">CORTIS
                                (코르티스) 'GO!'</h3>
                            <div class="media-card"
                                style="padding: 25px 20px; border-radius: 8px; background: #111; border: 1px solid #333; height: auto; flex-direction: column; justify-content: center; align-items: center; gap: 20px;">
                                <div class="vinyl-record" id="vinylRecord2" style="margin-bottom: 5px;">
                                    <img src="thumnails music/thumnail music 2.jpg" alt="Album Art">
                                    <div class="vinyl-hole"></div>
                                </div>

                                <!-- Progress Bar -->
                                <input type="range" id="progressBar2" value="0" min="0" max="100"
                                    style="width: 100%; cursor: pointer; accent-color: #e60000; height: 4px; margin-bottom: 5px; background: #333; outline: none; border-radius: 2px;">

                                <!-- Controls Row -->
                                <div
                                    style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;"></div>
                                    <button id="playPauseBtn2"
                                        style="background: #ffffff; border: none; width: 55px; height: 55px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: transform 0.2s ease, background 0.2s; box-shadow: 0 4px 12px rgba(255,255,255,0.2);"
                                        onmouseover="this.style.transform='scale(1.1)'; this.style.background='#f0f0f0'"
                                        onmouseout="this.style.transform='scale(1)'; this.style.background='#ffffff'">
                                        <svg id="playIcon2" viewBox="0 0 24 24" width="28" height="28" fill="#000000"
                                            style="margin-left: 4px;">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        <svg id="pauseIcon2" viewBox="0 0 24 24" width="28" height="28" fill="#000000"
                                            style="display: none;">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                                        </svg>
                                    </button>
                                    <div style="flex: 1; display: flex; justify-content: flex-end;">
                                        <span id="timeDisplay2"
                                            style="color: #ccc; font-size: 0.95rem; font-family: monospace; letter-spacing: 1px; font-weight: bold;">0:00
                                             / 0:00</span>
                                    </div>
                                    <audio id="audioPlayer2" style="display: none;">
                                        <source src="musics/CORTIS (코르티스) 'GO!'.mp3" type="audio/mpeg">
                                    </audio>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Player 3: What You Want -->
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 450px;">
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px;">CORTIS
                                (코르티스) 'What You Want'</h3>
                            <div class="media-card"
                                style="padding: 25px 20px; border-radius: 8px; background: #111; border: 1px solid #333; height: auto; flex-direction: column; justify-content: center; align-items: center; gap: 20px;">
                                <div class="vinyl-record" id="vinylRecord3" style="margin-bottom: 5px;">
                                    <img src="thumnails music/thumnail music what you want.jpg" alt="Album Art">
                                    <div class="vinyl-hole"></div>
                                </div>

                                <!-- Progress Bar -->
                                <input type="range" id="progressBar3" value="0" min="0" max="100"
                                    style="width: 100%; cursor: pointer; accent-color: #e60000; height: 4px; margin-bottom: 5px; background: #333; outline: none; border-radius: 2px;">

                                <!-- Controls Row -->
                                <div
                                    style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;"></div>
                                    <button id="playPauseBtn3"
                                        style="background: #ffffff; border: none; width: 55px; height: 55px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: transform 0.2s ease, background 0.2s; box-shadow: 0 4px 12px rgba(255,255,255,0.2);"
                                        onmouseover="this.style.transform='scale(1.1)'; this.style.background='#f0f0f0'"
                                        onmouseout="this.style.transform='scale(1)'; this.style.background='#ffffff'">
                                        <svg id="playIcon3" viewBox="0 0 24 24" width="28" height="28" fill="#000000"
                                            style="margin-left: 4px;">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        <svg id="pauseIcon3" viewBox="0 0 24 24" width="28" height="28" fill="#000000"
                                            style="display: none;">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                                        </svg>
                                    </button>
                                    <div style="flex: 1; display: flex; justify-content: flex-end;">
                                        <span id="timeDisplay3"
                                            style="color: #ccc; font-size: 0.95rem; font-family: monospace; letter-spacing: 1px; font-weight: bold;">0:00
                                             / 0:00</span>
                                    </div>
                                    <audio id="audioPlayer3" style="display: none;">
                                        <source src="musics/CORTIS (코르티스) 'What You Want'.mp3" type="audio/mpeg">
                                    </audio>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Video Section -->
                <div class="video-block">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0;">Featured Videos</h2>
                        <a href="videos.php" style="color: #fff; text-decoration: none; font-family: 'Inter', sans-serif; font-weight: bold; font-size: 0.9rem; letter-spacing: 1px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">VIEW ALL &rarr;</a>
                    </div>
                    <div class="video-grid">
                        <div class="media-card video-card">
                            <iframe src="https://www.youtube.com/embed/nCElUD0jvgo" title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        </div>
                        <div class="media-card video-card">
                            <iframe src="https://www.youtube.com/embed/kRpaqR5sbf0" title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <!-- JavaScript to toggle Mobile Menu and handle audio -->
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

        function setupAudioPlayer(audioId, btnId, playIconId, pauseIconId, vinylId, timeId, progId) {
            const audio = document.getElementById(audioId);
            const playPauseBtn = document.getElementById(btnId);
            const playIcon = document.getElementById(playIconId);
            const pauseIcon = document.getElementById(pauseIconId);
            const vinyl = document.getElementById(vinylId);
            const timeDisplay = document.getElementById(timeId);
            const progressBar = document.getElementById(progId);

            if (!audio || !playPauseBtn) return;

            const formatTime = (time) => {
                if (isNaN(time)) return "0:00";
                const minutes = Math.floor(time / 60);
                const seconds = Math.floor(time % 60).toString().padStart(2, '0');
                return `${minutes}:${seconds}`;
            };

            playPauseBtn.addEventListener('click', () => {
                document.querySelectorAll('audio').forEach(otherAudio => {
                    if (otherAudio !== audio && !otherAudio.paused) {
                        otherAudio.pause();
                        otherAudio.dispatchEvent(new Event('pause'));
                    }
                });

                if (audio.paused) {
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            audio.addEventListener('play', () => {
                playIcon.style.display = 'none';
                pauseIcon.style.display = 'block';
                vinyl.style.animationPlayState = 'running';
            });

            audio.addEventListener('pause', () => {
                playIcon.style.display = 'block';
                pauseIcon.style.display = 'none';
                vinyl.style.animationPlayState = 'paused';
            });

            audio.addEventListener('ended', () => {
                playIcon.style.display = 'block';
                pauseIcon.style.display = 'none';
                vinyl.style.animationPlayState = 'paused';
                audio.currentTime = 0;
                progressBar.value = 0;
            });

            audio.addEventListener('loadedmetadata', () => {
                timeDisplay.textContent = `0:00 / ${formatTime(audio.duration)}`;
                progressBar.max = audio.duration;
            });

            audio.addEventListener('timeupdate', () => {
                timeDisplay.textContent = `${formatTime(audio.currentTime)} / ${formatTime(audio.duration)}`;
                progressBar.value = audio.currentTime;
            });

            progressBar.addEventListener('input', () => {
                audio.currentTime = progressBar.value;
            });
        }

        setupAudioPlayer('audioPlayer', 'playPauseBtn', 'playIcon', 'pauseIcon', 'vinylRecord', 'timeDisplay', 'progressBar');
        setupAudioPlayer('audioPlayer2', 'playPauseBtn2', 'playIcon2', 'pauseIcon2', 'vinylRecord2', 'timeDisplay2', 'progressBar2');
        setupAudioPlayer('audioPlayer3', 'playPauseBtn3', 'playIcon3', 'pauseIcon3', 'vinylRecord3', 'timeDisplay3', 'progressBar3');
    </script>
</body>

</html>
