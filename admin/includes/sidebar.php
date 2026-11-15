<?php
// Calculate pending orders badge count if connection is available
$pendingCount = 0;
if (isset($conn) && $conn) {
    $resBadge = $conn->query("SELECT COUNT(*) AS total FROM `ticket_orders` WHERE `order_status` = 'PENDING'");
    if ($resBadge && $rowBadge = $resBadge->fetch_assoc()) {
        $pendingCount = $rowBadge['total'];
    }
}
$currentScript = basename($_SERVER['PHP_SELF']);
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link border-bottom-0">
        <img src="../images/CORTISLOGO.png" alt="CORTIS Logo" class="brand-image img-circle elevation-3" style="opacity: .9">
        <span class="brand-text font-weight-bold ml-1" style="letter-spacing: 1px;">CORTIS ADMIN</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex border-bottom-0">
            <div class="image">
                <img src="../images/profile/JAMES.jpeg" class="img-circle elevation-2" alt="Leader James" style="width: 34px; height: 34px; object-fit: cover;">
            </div>
            <div class="info">
                <a href="#" class="d-block font-weight-bold">Super Admin</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($currentScript === 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase font-weight-bold text-muted mt-2">TICKETING & TOUR</li>

                <li class="nav-item">
                    <a href="ticket_orders.php" class="nav-link <?php echo ($currentScript === 'ticket_orders.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>
                            Ticket Orders
                            <?php if ($pendingCount > 0): ?>
                                <span class="badge badge-warning right"><?php echo $pendingCount; ?></span>
                            <?php endif; ?>
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="concerts.php" class="nav-link <?php echo ($currentScript === 'concerts.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Concert Schedules</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase font-weight-bold text-muted mt-2">CONTENT MANAGEMENT</li>

                <li class="nav-item">
                    <a href="members.php" class="nav-link <?php echo ($currentScript === 'members.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Band Members</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="music.php" class="nav-link <?php echo ($currentScript === 'music.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-music"></i>
                        <p>Albums & Tracks</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="notices.php" class="nav-link <?php echo ($currentScript === 'notices.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Official Notices</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="videos.php" class="nav-link <?php echo ($currentScript === 'videos.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-video"></i>
                        <p>Band Videos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="gallery.php" class="nav-link <?php echo ($currentScript === 'gallery.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-images"></i>
                        <p>Gallery Photos</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase font-weight-bold text-muted mt-2">FRONTEND SHORTCUTS</li>

                <li class="nav-item">
                    <a href="../index.php" target="_blank" class="nav-link">
                        <i class="nav-icon fas fa-home text-info"></i>
                        <p>Visit Home Page</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../jadwal_konser.php" target="_blank" class="nav-link">
                        <i class="nav-icon fas fa-globe text-success"></i>
                        <p>Visit Tour & Ticket</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
