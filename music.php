<?php
require_once __DIR__ . '/db_connect.php';

// Fetch albums and their tracks from database
$albums = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $resAlb = $conn->query("SELECT * FROM `albums` ORDER BY `release_date` DESC");
    if ($resAlb) {
        while ($a = $resAlb->fetch_assoc()) {
            $albId = $a['id'];
            $tracks = [];
            $resTrk = $conn->query("SELECT * FROM `tracks` WHERE `album_id` = '{$albId}' ORDER BY `track_number` ASC");
            if ($resTrk) {
                while ($t = $resTrk->fetch_assoc()) {
                    $tracks[] = $t;
                }
            }
            $a['tracks'] = $tracks;
            $albums[] = $a;
        }
    }
}

// Fallback dummy data if DB empty
if (empty($albums)) {
    $albums = [
        [
            'id' => 'album1',
            'title' => 'GREENGREEN',
            'release_date' => '2023-11-20',
            'cover_path' => 'images/greengreenalbum.jpg',
            'tracks' => [
                ['id' => 1, 'track_number' => 1, 'title' => 'TNT', 'duration' => '2:02', 'badge' => 'Preview', 'audio_source' => 'https://open.spotify.com/embed/track/7diH1qnFziArYzynk3vkr6?utm_source=generator&si=3747db085eae4ab3'],
                ['id' => 2, 'track_number' => 2, 'title' => 'REDRED', 'duration' => '2:43', 'badge' => 'Full', 'audio_source' => "musics/CORTIS (코르티스) 'REDRED'.mp3"],
                ['id' => 3, 'track_number' => 3, 'title' => 'ACAI', 'duration' => '2:53', 'badge' => 'Full', 'audio_source' => 'musics/acai.mp3'],
                ['id' => 4, 'track_number' => 4, 'title' => 'YOUNGCREATORCREW', 'duration' => '2:56', 'badge' => 'Preview', 'audio_source' => 'https://open.spotify.com/embed/track/2vKNzC9T3kdU2JECcvob3V?utm_source=generator&si=8c17dd8494444c5d'],
                ['id' => 5, 'track_number' => 5, 'title' => 'Wassup', 'duration' => '3:04', 'badge' => 'Preview', 'audio_source' => 'https://open.spotify.com/embed/track/3dCdcURa0Kk5UWXRY00HQy?utm_source=generator&si=2d27e866c9de46da'],
                ['id' => 6, 'track_number' => 6, 'title' => 'Blue Lips', 'duration' => '2:21', 'badge' => 'Full', 'audio_source' => 'musics/blue_lips.mp3'],
            ]
        ],
        [
            'id' => 'album2',
            'title' => 'COLOR OUTSIDE THE LINES',
            'release_date' => '2024-05-15',
            'cover_path' => 'images/whatyouwantalbum.jpg',
            'tracks' => [
                ['id' => 7, 'track_number' => 1, 'title' => 'GO!', 'duration' => '2:50', 'badge' => 'Full', 'audio_source' => "musics/CORTIS (코르티스) 'GO!'.mp3"],
                ['id' => 8, 'track_number' => 2, 'title' => 'What You Want', 'duration' => '3:14', 'badge' => 'Full', 'audio_source' => "musics/CORTIS (코르티스) 'What You Want'.mp3"],
                ['id' => 9, 'track_number' => 3, 'title' => 'FaSHioN', 'duration' => '2:54', 'badge' => 'Full', 'audio_source' => 'musics/FaSHioN.mp3'],
                ['id' => 10, 'track_number' => 4, 'title' => 'JoyRide', 'duration' => '2:37', 'badge' => 'Preview', 'audio_source' => 'https://open.spotify.com/embed/track/6T17ZI0glfM2IGLdUwtnLK?utm_source=generator&si=dddc291385974113'],
                ['id' => 11, 'track_number' => 5, 'title' => 'Lullaby', 'duration' => '2:44', 'badge' => 'Preview', 'audio_source' => 'https://open.spotify.com/embed/track/0nyhsAcyDj533QrXa4hUug?utm_source=generator&si=75b3ae64906042d2'],
            ]
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Discography - CORTIS Official</title>
    <!-- Add Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Inter:wght@400&family=Syne:wght@800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="music.css">
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
                <a href="music.php" class="active">MUSIC</a>
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
        <div class="side-number">03</div>
        <div class="side-line"></div>
        <div class="side-text">MUSIC</div>
    </div>

    <div class="music-layout"
        style="display: flex; gap: 80px; max-width: 1200px; margin: 40px auto; padding: 0 20px; align-items: flex-start;">
        <!-- Kiri: Daftar Album (DYNAMIC DARI DATABASE) -->
        <div class="album-sidebar" style="display: flex; flex-direction: column; gap: 40px; flex: 0 0 400px;">
            <?php foreach ($albums as $alb): ?>
                <div class="album-item" onclick="showTracklist('<?php echo htmlspecialchars($alb['id']); ?>')" style="cursor: pointer;">
                    <div class="cover-wrapper">
                        <img src="<?php echo htmlspecialchars($alb['cover_path']); ?>" alt="Cover <?php echo htmlspecialchars($alb['title']); ?>" class="main-cover" onerror="this.src='images/greengreenalbum.jpg';">
                        <div class="hover-overlay">
                            <span class="cross-icon"></span>
                        </div>
                    </div>
                    <div class="album-meta">
                        <span class="album-title"><?php echo htmlspecialchars($alb['title']); ?></span>
                        <span class="album-date"><?php echo htmlspecialchars($alb['release_date']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Kanan: Playlist & Audio Player -->
        <div class="playlist-pane" id="playlist-pane"
            style="flex: 1; background: #111; padding: 40px; border-radius: 12px;">
            <div id="default-msg"
                style="text-align: center; color: #888; font-weight: 300; letter-spacing: 2px; padding: 100px 0;">
                SELECT AN ALBUM TO PLAY
            </div>

            <!-- CUSTOM PLAYER (Global for the right pane) -->
            <div id="custom-player" style="display: none; flex-direction: column; width: 100%; margin-bottom: 30px;">
                <div class="media-card"
                    style="padding: 25px 20px; border-radius: 8px; background: #111; border: 1px solid #333; height: auto; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 20px;">
                    <div class="vinyl-record" id="musicVinyl" style="margin-bottom: 5px; width: 150px; height: 150px;">
                        <img src="images/greengreenalbum.jpg" alt="Album Art" id="playerCover">
                        <div class="vinyl-hole"></div>
                    </div>

                    <h3 id="playerTitle"
                        style="margin: 0; font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px; color: #fff; text-align: center;">
                        CORTIS - Song</h3>

                    <!-- Progress Bar -->
                    <input type="range" id="progressBar" value="0" min="0" max="100"
                        style="width: 100%; cursor: pointer; accent-color: #e60000; height: 4px; margin-bottom: 5px; background: #333; outline: none; border-radius: 2px;">

                    <!-- Controls Row -->
                    <div
                        style="width: 100%; display: flex; justify-content: center; align-items: center; margin-top: 5px;">
                        <span id="timeDisplay"
                            style="color: #ccc; font-size: 0.95rem; font-family: monospace; letter-spacing: 1px; font-weight: bold;">0:00
                            / 0:00</span>
                    </div>
                    <audio id="globalAudioPlayer" style="display: none;"></audio>
                </div>
            </div>

            <!-- SPOTIFY PLAYER -->
            <div id="spotify-player" style="display: none; width: 100%; margin-bottom: 30px;">
                <iframe id="spotifyIframe" src="" width="100%" height="152" frameBorder="0" allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>
            </div>

            <!-- Playlists (DYNAMIC DARI DATABASE) -->
            <?php foreach ($albums as $alb): ?>
                <div class="tracklist-container" id="tracklist-<?php echo htmlspecialchars($alb['id']); ?>" data-cover="<?php echo htmlspecialchars($alb['cover_path']); ?>" style="display: none;">
                    <h3 style="margin-top: 0; color: #fff; font-family: 'Syne', sans-serif; letter-spacing: 1px;">
                        <?php echo htmlspecialchars($alb['title']); ?> - TRACKS
                    </h3>
                    <div class="track-list-header">
                        <div class="col-num">#</div>
                        <div class="col-title">Title</div>
                        <div class="col-dur">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                <path
                                    d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z" />
                            </svg>
                        </div>
                    </div>
                    <ol class="track-list-new">
                        <?php if (empty($alb['tracks'])): ?>
                            <li style="padding: 15px; color: #777;">No tracks found in this album.</li>
                        <?php endif; ?>
                        <?php foreach ($alb['tracks'] as $t): 
                            $isFull = ($t['badge'] === 'Full');
                            $badgeColor = $isFull ? '#e60000' : '#1DB954';
                            $badgeText = $isFull ? 'Full' : 'Preview';
                        ?>
                            <li data-num="<?php echo $t['track_number']; ?>"
                                <?php if ($isFull): ?>
                                    onclick="playCustomSong(`<?php echo addslashes($t['audio_source']); ?>`, `<?php echo addslashes($t['title']); ?>`, this)"
                                <?php else: ?>
                                    onclick="playSpotifySong(`<?php echo addslashes($t['audio_source']); ?>`, `<?php echo addslashes($t['title']); ?>`, this)"
                                <?php endif; ?>
                            >
                                <div class="col-num"><span class="track-num"><?php echo $t['track_number']; ?></span></div>
                                <div class="col-title">
                                    <?php echo htmlspecialchars($t['title']); ?> 
                                    <span style="font-size: 0.7em; padding: 2px 5px; border-radius: 4px; background: <?php echo $badgeColor; ?>; color: white; margin-left: 8px;"><?php echo $badgeText; ?></span>
                                    <br><span style="font-size: 0.85em; color: #888;">CORTIS</span>
                                </div>
                                <div class="col-dur"><?php echo htmlspecialchars($t['duration']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-social">
            <span style="display: flex; align-items: center; gap: 8px;"><img src="images/instagram.png" alt="Instagram"
                    class="footer-icon" style="filter: none;"> INSTAGRAM</span>
            <span style="display: flex; align-items: center; gap: 8px;"><img src="images/tiktok.png" alt="TikTok"
                    class="footer-icon" style="filter: none;"> TIKTOK</span>
            <span style="display: flex; align-items: center; gap: 8px;"><img src="images/x.png" alt="X"
                    class="footer-icon" style="filter: none;"> X</span>
            <a href="https://www.youtube.com/@HYBELABELS" target="_blank"
                style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: opacity 0.2s;"
                onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                <img src="images/Youtube.png" alt="Youtube" class="footer-icon" style="filter: none;"> YOUTUBE
            </a>
        </div>
        <div class="footer-bottom">
            &copy; 2024 CORTIS OFFICIAL. PRIVACY | CONTACT
        </div>
    </footer>

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

        // --- CUSTOM AUDIO PLAYER JS ---
        const formatTime = (time) => {
            if (isNaN(time)) return "0:00";
            const minutes = Math.floor(time / 60);
            const seconds = Math.floor(time % 60).toString().padStart(2, '0');
            return `${minutes}:${seconds}`;
        };

        const gAudio = document.getElementById('globalAudioPlayer');
        const vinyl = document.getElementById('musicVinyl');
        const timeTxt = document.getElementById('timeDisplay');
        const prog = document.getElementById('progressBar');

        if (gAudio) {
            gAudio.addEventListener('loadedmetadata', () => {
                timeTxt.textContent = `0:00 / ${formatTime(gAudio.duration)}`;
                prog.max = gAudio.duration;
            });
            gAudio.addEventListener('timeupdate', () => {
                timeTxt.textContent = `${formatTime(gAudio.currentTime)} / ${formatTime(gAudio.duration)}`;
                prog.value = gAudio.currentTime;
            });
            prog.addEventListener('input', () => {
                gAudio.currentTime = prog.value;
            });
            gAudio.addEventListener('ended', () => {
                vinyl.style.animationPlayState = 'paused';
                const activeIcon = document.querySelector('.active-track .play-icon');
                if (activeIcon) {
                    activeIcon.innerText = '▶';
                }
            });
        }

        function showTracklist(albumId) {
            document.getElementById('default-msg').style.display = 'none';
            document.getElementById('custom-player').style.display = 'flex';
            if (document.getElementById('spotify-player')) document.getElementById('spotify-player').style.display = 'none';

            // Hide all tracklists
            document.querySelectorAll('.tracklist-container').forEach(el => {
                el.style.display = 'none';
            });

            // Show selected tracklist
            const target = document.getElementById('tracklist-' + albumId);
            if (target) {
                target.style.display = 'block';
                const cover = target.getAttribute('data-cover');
                if (cover) {
                    document.getElementById('playerCover').src = cover;
                }
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        let currentPlayingSrc = "";

        function playSpotifySong(urlOrId, title, element) {
            const e = window.event;
            const clickedPlayBtn = e && e.target.closest('.col-num');
            const isActive = element.classList.contains('active-track');

            document.querySelectorAll('.spotify-accordion-row').forEach(el => el.remove());

            currentPlayingSrc = urlOrId;
            gAudio.pause();
            vinyl.style.animationPlayState = 'paused';

            document.querySelectorAll('.track-list-new li:not(.spotify-accordion-row)').forEach(item => {
                item.classList.remove('active-track');
                item.style.borderRadius = '4px';
                const colNum = item.querySelector('.col-num');
                if (colNum) {
                    const firstChild = colNum.firstElementChild;
                    if (firstChild && firstChild.classList.contains('play-icon')) {
                        const num = item.getAttribute('data-num') || '*';
                        firstChild.outerHTML = `<span class="track-num">${num}</span>`;
                    }
                }
            });

            if (isActive) {
                if (document.getElementById('spotify-player')) {
                    document.getElementById('spotify-player').style.display = 'none';
                }
                currentPlayingSrc = "";
                return;
            }

            let spotifyUrl = urlOrId;
            if (!spotifyUrl.startsWith('http')) {
                spotifyUrl = `https://open.spotify.com/embed/track/${urlOrId}?utm_source=generator`;
            }

            if (clickedPlayBtn && !spotifyUrl.includes('autoplay=1')) {
                spotifyUrl += (spotifyUrl.includes('?') ? '&' : '?') + 'autoplay=1';
            }

            if (document.getElementById('spotify-player')) {
                document.getElementById('spotify-player').style.display = 'block';
                document.getElementById('spotifyIframe').src = spotifyUrl;
            }
            document.getElementById('custom-player').style.display = 'none';

            element.classList.add('active-track');
            element.style.borderBottomLeftRadius = '0';
            element.style.borderBottomRightRadius = '0';

            const colNum = element.querySelector('.col-num');
            if (colNum) {
                colNum.innerHTML = `<span class="play-icon">▶</span>`;
            }

            const accordionRow = document.createElement('li');
            accordionRow.className = 'spotify-accordion-row';
            accordionRow.style.display = 'block';
            accordionRow.style.padding = '10px 16px 16px 16px';
            accordionRow.style.background = 'rgba(255, 255, 255, 0.05)';
            accordionRow.style.borderBottomLeftRadius = '6px';
            accordionRow.style.borderBottomRightRadius = '6px';
            accordionRow.style.marginTop = '0px';
            accordionRow.innerHTML = `<iframe data-testid="embed-iframe" style="border-radius:12px" src="${spotifyUrl}" width="100%" height="80" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>`;

            element.insertAdjacentElement('afterend', accordionRow);
        }

        function playCustomSong(src, title, element) {
            const e = window.event;
            const clickedPlayBtn = e && e.target.closest('.col-num');

            document.getElementById('spotify-player').style.display = 'none';
            document.getElementById('custom-player').style.display = 'flex';

            const tracklist = element.closest('.tracklist-container');
            if (tracklist) {
                const cover = tracklist.getAttribute('data-cover');
                if (cover) {
                    document.getElementById('playerCover').src = cover;
                }
            }

            if (currentPlayingSrc === src) {
                if (clickedPlayBtn) {
                    if (gAudio.paused) {
                        gAudio.play();
                        vinyl.style.animationPlayState = 'running';
                        const icon = element.querySelector('.play-icon');
                        if (icon) icon.innerText = '⏸';
                    } else {
                        gAudio.pause();
                        vinyl.style.animationPlayState = 'paused';
                        const icon = element.querySelector('.play-icon');
                        if (icon) icon.innerText = '▶';
                    }
                }
                return;
            }

            currentPlayingSrc = src;
            gAudio.src = src;
            document.getElementById('playerTitle').innerText = title;
            
            if (clickedPlayBtn) {
                gAudio.play();
                vinyl.style.animationPlayState = 'running';
            } else {
                gAudio.pause();
                vinyl.style.animationPlayState = 'paused';
            }

            document.querySelectorAll('.track-list-new li').forEach(item => {
                item.classList.remove('active-track');
                const colNum = item.querySelector('.col-num');
                if (colNum) {
                    const firstChild = colNum.firstElementChild;
                    if (firstChild && firstChild.classList.contains('play-icon')) {
                        const num = item.getAttribute('data-num') || '*';
                        firstChild.outerHTML = `<span class="track-num">${num}</span>`;
                    }
                }
            });

            element.classList.add('active-track');
            const colNum = element.querySelector('.col-num');
            if (colNum) {
                colNum.innerHTML = clickedPlayBtn ? `<span class="play-icon">⏸</span>` : `<span class="play-icon">▶</span>`;
            }
        }
    </script>
</body>

</html>
