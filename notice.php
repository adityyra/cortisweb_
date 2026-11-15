<?php
require_once __DIR__ . '/db_connect.php';

// Fetch notices from database
$notices = [];
if (isset($conn) && $conn && !$conn->connect_error) {
    $res = $conn->query("SELECT * FROM `notices` ORDER BY `notice_number` DESC, `notice_date` DESC");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $notices[] = $r;
        }
    }
}

// Fallback dummy notices if database empty
if (empty($notices)) {
    $notices = [
        [
            'notice_number' => 109,
            'category' => 'birthday',
            'title' => 'Happy Birthday JAMES',
            'notice_date' => '2026-06-15',
            'content' => '<p>Happy Birthday to <strong>JAMES</strong>, Leader and Main Vocalist of CORTIS! Let\'s celebrate this special day together by sending our warmest wishes and love.</p><p>May James always be healthy, happy, and continue to lead CORTIS with inspiration to a bigger world stage.</p>'
        ],
        [
            'notice_number' => 108,
            'category' => 'info',
            'title' => 'CORTIS World Tour <THE FIRST CHAPTER> Ticket Open Info',
            'notice_date' => '2026-06-01',
            'content' => '<p>Hello, this is CORTIS Official Staff. We are pleased to announce the official ticket sale schedule for <strong>CORTIS World Tour &lt;THE FIRST CHAPTER&gt;</strong>.</p><p>Please make sure to prepare your verified fanclub membership and identity card prior to ticketing.</p>'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice - CORTIS Official</title>
    <!-- Font Loader -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Oswald:wght@500;700&family=Syne:wght@700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="notice.css">
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
                <a href="about.php">ABOUT</a>
                <div class="dropdown">
                    <a href="music.php">MUSIC</a>
                    <div class="dropdown-content">
                        <a href="videos.php">VIDEOS</a>
                    </div>
                </div>
                <a href="jadwal_konser.php">TOUR</a>
                <a href="galeri.php">GALLERY</a>
                <a href="notice.php" class="active">NOTICE</a>
            </nav>

            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </header>

        <!-- SIDE INDICATOR -->
        <div class="side-indicator">
            <div class="side-number">07</div>
            <div class="side-line"></div>
            <div class="side-text">NOTICE</div>
        </div>

        <main>

            <div class="notice-layout">

                <!-- Kolom Kiri: Judul Halaman -->
                <div class="notice-left">
                    <h1 class="notice-main-title">NOTICE</h1>
                </div>

                <!-- Kolom Kanan: Search, Filter, List -->
                <div class="notice-right">

                    <div class="filter-bar">
                        <div class="search-wrapper">
                            <input type="text" class="search-input" id="searchInput"
                                placeholder="Search info or birthdays..." onkeyup="filterNotices()">
                        </div>
                        <div class="category-buttons">
                            <button class="cat-btn active" id="btn-all" onclick="filterCategory('all')">All</button>
                            <button class="cat-btn" id="btn-birthday"
                                onclick="filterCategory('birthday')">Birthday</button>
                            <button class="cat-btn" id="btn-info" onclick="filterCategory('info')">Info</button>
                        </div>
                    </div>

                    <!-- Notice List (DYNAMIC FROM DATABASE) -->
                    <div class="notice-list" id="noticeList">

                        <?php foreach ($notices as $n): ?>
                            <div class="notice-item" data-category="<?php echo htmlspecialchars($n['category']); ?>" onclick="toggleNotice(this)">
                                <div class="notice-header">
                                    <div class="notice-number"><?php echo htmlspecialchars($n['notice_number']); ?></div>
                                    <div class="notice-summary">
                                        <h3 class="notice-title"><?php echo htmlspecialchars($n['title']); ?></h3>
                                        <p class="notice-date"><?php echo htmlspecialchars($n['notice_date']); ?></p>
                                    </div>
                                    <div class="notice-badge"><?php echo ($n['category'] === 'birthday') ? 'Birthday' : 'Info'; ?></div>
                                </div>
                                <div class="notice-body">
                                    <div class="notice-content-inner">
                                        <?php echo $n['content']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <div class="no-result" id="noResult" style="display: none;">
                        No notices match your search.
                    </div>

                    <!-- Pagination -->
                    <div class="pagination" id="noticePagination" style="display: none;">
                        <a href="#noticeList" class="page-arrow" id="prevNoticePage">&#10094;</a>
                        <span class="page-numbers" id="noticePageIndicator"><strong>1</strong> / 1</span>
                        <a href="#noticeList" class="page-arrow" id="nextNoticePage">&#10095;</a>
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

    </div>

    <script>
        let currentCategory = 'all';
        let currentPage = 1;
        const itemsPerPage = 7;
        let filteredItems = [];

        function toggleNotice(element) {
            const isExpanded = element.classList.contains('expanded');
            const items = document.querySelectorAll('.notice-item');
            items.forEach(item => item.classList.remove('expanded'));

            if (!isExpanded) {
                element.classList.add('expanded');
            }
        }

        function filterCategory(category) {
            currentCategory = category;
            const buttons = document.querySelectorAll('.cat-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            document.getElementById('btn-' + category).classList.add('active');

            currentPage = 1;
            applyFilter();
        }

        function filterNotices() {
            currentPage = 1;
            applyFilter();
        }

        function applyFilter() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.notice-item');
            filteredItems = [];

            items.forEach(item => {
                const category = item.getAttribute('data-category');
                const title = item.querySelector('.notice-title').textContent.toLowerCase();
                const content = item.querySelector('.notice-body').textContent.toLowerCase();

                const matchesCategory = (currentCategory === 'all') || (category === currentCategory);
                const matchesSearch = query === '' || title.includes(query) || content.includes(query);

                if (matchesCategory && matchesSearch) {
                    filteredItems.push(item);
                }
            });

            const noResult = document.getElementById('noResult');
            const pagination = document.getElementById('noticePagination');

            if (filteredItems.length === 0) {
                items.forEach(item => item.style.display = 'none');
                noResult.style.display = 'block';
                pagination.style.display = 'none';
                return;
            }

            noResult.style.display = 'none';
            renderPage();
        }

        function renderPage() {
            const items = document.querySelectorAll('.notice-item');
            items.forEach(item => item.style.display = 'none');

            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            const pageItems = filteredItems.slice(startIndex, endIndex);
            pageItems.forEach(item => {
                item.style.display = 'block';
            });

            const pagination = document.getElementById('noticePagination');
            const pageIndicator = document.getElementById('noticePageIndicator');

            if (totalPages > 1) {
                pagination.style.display = 'flex';
                pageIndicator.innerHTML = `<strong>${currentPage}</strong> / ${totalPages}`;
            } else {
                pagination.style.display = 'none';
            }
        }

        document.getElementById('prevNoticePage').addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderPage();
            }
        });

        document.getElementById('nextNoticePage').addEventListener('click', (e) => {
            e.preventDefault();
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
            }
        });

        // Initialize on load
        applyFilter();

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
