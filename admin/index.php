<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../db_connect.php';

// Stats queries
$totalOrders = 0;
$pendingOrders = 0;
$successOrders = 0;
$totalConcerts = 0;
$totalMembers = 0;
$totalTracks = 0;

$res1 = $conn->query("SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN order_status = 'PENDING' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN order_status = 'SUCCESS' THEN 1 ELSE 0 END) AS success
FROM `ticket_orders`");
if ($res1 && $row = $res1->fetch_assoc()) {
    $totalOrders = (int)$row['total'];
    $pendingOrders = (int)$row['pending'];
    $successOrders = (int)$row['success'];
}

$res2 = $conn->query("SELECT COUNT(*) AS total FROM `concert_schedules`");
if ($res2 && $row = $res2->fetch_assoc()) {
    $totalConcerts = (int)$row['total'];
}

$res3 = $conn->query("SELECT COUNT(*) AS total FROM `members`");
if ($res3 && $row = $res3->fetch_assoc()) {
    $totalMembers = (int)$row['total'];
}

$res4 = $conn->query("SELECT COUNT(*) AS total FROM `tracks`");
if ($res4 && $row = $res4->fetch_assoc()) {
    $totalTracks = (int)$row['total'];
}

// Fetch recent 5 ticket orders
$recentOrders = [];
$resRecent = $conn->query("SELECT o.*, c.location, c.venue, c.concert_date 
    FROM `ticket_orders` o 
    LEFT JOIN `concert_schedules` c ON o.concert_id = c.id 
    ORDER BY o.id DESC LIMIT 5");
if ($resRecent) {
    while ($r = $resRecent->fetch_assoc()) {
        $recentOrders[] = $r;
    }
}

// Fetch concert schedules
$concertList = [];
$resC = $conn->query("SELECT * FROM `concert_schedules` ORDER BY `concert_date` ASC");
if ($resC) {
    while ($r = $resC->fetch_assoc()) {
        $concertList[] = $r;
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">Band Management Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning elevation-2">
                        <div class="inner">
                            <h3><?php echo $pendingOrders; ?></h3>
                            <p>Pending Ticket Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="ticket_orders.php" class="small-box-footer">Review Orders <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success elevation-2">
                        <div class="inner">
                            <h3><?php echo $successOrders; ?></h3>
                            <p>Paid / Success Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="ticket_orders.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger elevation-2">
                        <div class="inner">
                            <h3><?php echo $totalConcerts; ?></h3>
                            <p>World Tour Concerts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-guitar"></i>
                        </div>
                        <a href="concerts.php" class="small-box-footer">Manage Tour <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info elevation-2">
                        <div class="inner">
                            <h3><?php echo $totalMembers; ?></h3>
                            <p>CORTIS Band Members</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="members.php" class="small-box-footer">View Profiles <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <!-- Main row -->
            <div class="row">
                <!-- Left col: Recent Ticket Orders -->
                <div class="col-lg-8">
                    <div class="card card-outline card-danger elevation-2">
                        <div class="card-header border-transparent">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-receipt mr-1"></i> Latest Ticket Transactions</h3>
                            <div class="card-tools">
                                <a href="ticket_orders.php" class="btn btn-sm btn-danger">View All Orders</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0 table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Buyer Name</th>
                                            <th>Concert</th>
                                            <th>Tier</th>
                                            <th>Qty</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentOrders)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No ticket orders found.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recentOrders as $ord): ?>
                                                <tr>
                                                    <td><a href="ticket_orders.php" class="font-weight-bold text-dark">#ORD-<?php echo $ord['id']; ?></a></td>
                                                    <td><?php echo htmlspecialchars($ord['fullname']); ?></td>
                                                    <td>
                                                        <span class="badge badge-light border">
                                                            <?php echo htmlspecialchars($ord['location'] ?? 'Concert #' . $ord['concert_id']); ?>
                                                        </span>
                                                    </td>
                                                    <td><span class="badge badge-secondary"><?php echo htmlspecialchars($ord['ticket_category']); ?></span></td>
                                                    <td><?php echo (int)$ord['quantity']; ?></td>
                                                    <td><small class="text-muted"><?php echo htmlspecialchars($ord['payment_method']); ?></small></td>
                                                    <td>
                                                        <?php if ($ord['order_status'] === 'SUCCESS'): ?>
                                                            <span class="badge badge-success px-2 py-1">SUCCESS</span>
                                                        <?php elseif ($ord['order_status'] === 'PENDING'): ?>
                                                            <span class="badge badge-warning px-2 py-1">PENDING</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger px-2 py-1">FAILED</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <a href="../jadwal_konser.html" target="_blank" class="btn btn-sm btn-outline-secondary float-left">
                                <i class="fas fa-plus mr-1"></i> Test Buy Ticket Form (Frontend)
                            </a>
                            <a href="ticket_orders.php" class="btn btn-sm btn-outline-primary float-right">
                                Manage All (<?php echo $totalOrders; ?> Orders)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right col: Concert Schedules & Quick Info -->
                <div class="col-lg-4">
                    <div class="card card-outline card-dark elevation-2">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-calendar-check mr-1"></i> World Tour Schedules</h3>
                            <div class="card-tools">
                                <a href="concerts.php" class="btn btn-xs btn-outline-dark">Manage</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-3 pr-3">
                                <?php foreach ($concertList as $c): ?>
                                    <li class="item py-2">
                                        <div class="product-info ml-0">
                                            <a href="concerts.php" class="product-title font-weight-bold text-dark">
                                                <?php echo htmlspecialchars($c['location']); ?>
                                                <?php if ($c['ticket_status'] === 'AVAILABLE'): ?>
                                                    <span class="badge badge-success float-right">AVAILABLE</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger float-right">SOLD OUT</span>
                                                <?php endif; ?>
                                            </a>
                                            <span class="product-description text-sm text-muted">
                                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($c['venue']); ?> &bull; 
                                                <i class="far fa-clock ml-1"></i> <?php echo date("M d, Y", strtotime($c['concert_date'])); ?>
                                            </span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="concerts.php" class="text-muted text-sm font-weight-bold">View Full Tour Schedule &rarr;</a>
                        </div>
                    </div>

                    <!-- Database Status Card -->
                    <div class="card card-outline card-secondary elevation-2">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-database text-primary mr-1"></i> Database Environment</h6>
                            <p class="text-sm text-muted mb-1"><strong>Database:</strong> <code>cortis_db</code></p>
                            <p class="text-sm text-muted mb-1"><strong>Collation:</strong> <code>utf8mb4_unicode_ci</code></p>
                            <p class="text-sm text-muted mb-0"><strong>Server Engine:</strong> <code>InnoDB</code></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include __DIR__ . '/includes/footer.php'; ?>
