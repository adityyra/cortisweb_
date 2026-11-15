<?php
$pageTitle = "Ticket Orders";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// Handle Status Update
if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $newStatus = isset($_POST['new_status']) ? trim($_POST['new_status']) : '';

    if ($orderId > 0 && in_array($newStatus, ['PENDING', 'SUCCESS', 'FAILED'])) {
        $stmt = $conn->prepare("UPDATE `ticket_orders` SET `order_status` = ? WHERE `id` = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        if ($stmt->execute()) {
            $alertMsg = "Order #ORD-{$orderId} status updated to <strong>{$newStatus}</strong> successfully.";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update status: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Delete Order
if (isset($_POST['action']) && $_POST['action'] === 'delete_order') {
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    if ($orderId > 0) {
        $stmt = $conn->prepare("DELETE FROM `ticket_orders` WHERE `id` = ?");
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            $alertMsg = "Order #ORD-{$orderId} deleted successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete order: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch all orders with concert details
$query = "SELECT o.*, c.tour_title, c.location, c.venue, c.concert_date 
          FROM `ticket_orders` o 
          LEFT JOIN `concert_schedules` c ON o.concert_id = c.id 
          ORDER BY o.id DESC";
$orders = [];
$res = $conn->query($query);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">Ticket Orders Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Ticket Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">

            <?php if (!empty($alertMsg)): ?>
                <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-1"></i> <?php echo $alertMsg; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card card-outline card-danger elevation-2">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-ticket-alt mr-1"></i> All Concert Ticket Bookings (Database: <code>ticket_orders</code>)
                    </h3>
                    <div class="card-tools">
                        <a href="../jadwal_konser.html" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-plus mr-1"></i> New Order (Frontend Form)
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 80px;">Order ID</th>
                                <th>Buyer & Contact</th>
                                <th>Identity No (NIK)</th>
                                <th>Concert Details</th>
                                <th>Tier & Qty</th>
                                <th>Payment Method</th>
                                <th>Order Status</th>
                                <th>Date Created</th>
                                <th style="width: 130px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $ord): ?>
                                <tr>
                                    <td class="font-weight-bold text-center">#ORD-<?php echo $ord['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($ord['fullname']); ?></strong><br>
                                        <small class="text-muted"><i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($ord['email']); ?></small><br>
                                        <small class="text-muted"><i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($ord['phone']); ?></small>
                                    </td>
                                    <td>
                                        <code class="text-dark font-weight-bold"><?php echo htmlspecialchars($ord['identity_no']); ?></code>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($ord['location'] ?? 'Concert #' . $ord['concert_id']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($ord['venue'] ?? '-'); ?></small><br>
                                        <small class="badge badge-light border"><?php echo htmlspecialchars($ord['concert_date'] ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info px-2 py-1"><?php echo htmlspecialchars($ord['ticket_category']); ?></span>
                                        <span class="font-weight-bold ml-1">&times; <?php echo (int)$ord['quantity']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($ord['payment_method']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($ord['order_status'] === 'SUCCESS'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> SUCCESS</span>
                                        <?php elseif ($ord['order_status'] === 'PENDING'): ?>
                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> PENDING</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> FAILED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo date("Y-m-d H:i", strtotime($ord['created_at'])); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <!-- Dropdown Action -->
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-dark dropdown-toggle" data-toggle="dropdown">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <h6 class="dropdown-header text-uppercase">Change Status</h6>
                                                
                                                <form method="POST" class="dropdown-item p-0">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                    <input type="hidden" name="new_status" value="SUCCESS">
                                                    <button type="submit" class="btn btn-link btn-block text-left px-3 py-1 text-success">
                                                        <i class="fas fa-check-circle mr-1"></i> Mark as SUCCESS
                                                    </button>
                                                </form>

                                                <form method="POST" class="dropdown-item p-0">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                    <input type="hidden" name="new_status" value="PENDING">
                                                    <button type="submit" class="btn btn-link btn-block text-left px-3 py-1 text-warning">
                                                        <i class="fas fa-clock mr-1"></i> Mark as PENDING
                                                    </button>
                                                </form>

                                                <form method="POST" class="dropdown-item p-0">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                    <input type="hidden" name="new_status" value="FAILED">
                                                    <button type="submit" class="btn btn-link btn-block text-left px-3 py-1 text-danger">
                                                        <i class="fas fa-times-circle mr-1"></i> Mark as FAILED
                                                    </button>
                                                </form>

                                                <div class="dropdown-divider"></div>

                                                <form method="POST" class="dropdown-item p-0" onsubmit="return confirm('Are you sure you want to delete order #ORD-<?php echo $ord['id']; ?>? This cannot be undone.');">
                                                    <input type="hidden" name="action" value="delete_order">
                                                    <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                    <button type="submit" class="btn btn-link btn-block text-left px-3 py-1 text-danger">
                                                        <i class="fas fa-trash-alt mr-1"></i> Delete Order
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
