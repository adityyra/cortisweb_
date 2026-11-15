<?php
$pageTitle = "Concert Schedules";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// Handle Add Concert
if (isset($_POST['action']) && $_POST['action'] === 'add_concert') {
    $tourTitle     = trim($_POST['tour_title'] ?? 'World Tour: "The First Chapter"');
    $concertDate   = trim($_POST['concert_date'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $venue         = trim($_POST['venue'] ?? '');
    $ticketStatus  = trim($_POST['ticket_status'] ?? 'AVAILABLE');

    if (!empty($concertDate) && !empty($location) && !empty($venue)) {
        $stmt = $conn->prepare("INSERT INTO `concert_schedules` (`tour_title`, `concert_date`, `location`, `venue`, `ticket_status`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $tourTitle, $concertDate, $location, $venue, $ticketStatus);
        if ($stmt->execute()) {
            $alertMsg = "New concert date for <strong>" . htmlspecialchars($location) . "</strong> added successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to add concert: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Edit Concert
if (isset($_POST['action']) && $_POST['action'] === 'edit_concert') {
    $cId          = (int)($_POST['concert_id'] ?? 0);
    $tourTitle    = trim($_POST['tour_title'] ?? '');
    $concertDate  = trim($_POST['concert_date'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $venue        = trim($_POST['venue'] ?? '');
    $ticketStatus = trim($_POST['ticket_status'] ?? 'AVAILABLE');

    if ($cId > 0 && !empty($concertDate) && !empty($location) && !empty($venue)) {
        $stmt = $conn->prepare("UPDATE `concert_schedules` SET `tour_title` = ?, `concert_date` = ?, `location` = ?, `venue` = ?, `ticket_status` = ? WHERE `id` = ?");
        $stmt->bind_param("sssssi", $tourTitle, $concertDate, $location, $venue, $ticketStatus, $cId);
        if ($stmt->execute()) {
            $alertMsg = "Concert #{$cId} (<strong>" . htmlspecialchars($location) . "</strong>) updated successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update concert: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Toggle Ticket Status
if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $cId = (int)$_POST['concert_id'];
    $newStatus = trim($_POST['new_status']);

    if ($cId > 0 && in_array($newStatus, ['AVAILABLE', 'SOLD OUT'])) {
        $stmt = $conn->prepare("UPDATE `concert_schedules` SET `ticket_status` = ? WHERE `id` = ?");
        $stmt->bind_param("si", $newStatus, $cId);
        if ($stmt->execute()) {
            $alertMsg = "Concert #{$cId} status changed to <strong>{$newStatus}</strong>.";
            $alertType = "info";
        } else {
            $alertMsg = "Failed to update status: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Delete Concert
if (isset($_POST['action']) && $_POST['action'] === 'delete_concert') {
    $cId = (int)$_POST['concert_id'];
    if ($cId > 0) {
        $stmt = $conn->prepare("DELETE FROM `concert_schedules` WHERE `id` = ?");
        $stmt->bind_param("i", $cId);
        if ($stmt->execute()) {
            $alertMsg = "Concert schedule #{$cId} (and its linked orders via CASCADE) deleted successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete concert: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch all concert schedules with orders count
$query = "SELECT c.*, COUNT(o.id) AS total_orders 
          FROM `concert_schedules` c 
          LEFT JOIN `ticket_orders` o ON c.id = o.concert_id 
          GROUP BY c.id 
          ORDER BY c.concert_date ASC";
$concerts = [];
$res = $conn->query($query);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $concerts[] = $row;
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
                    <h1 class="m-0 font-weight-bold">Concert Tour Schedules</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="../jadwal_konser.php" target="_blank" class="btn btn-outline-dark">
                        <i class="fas fa-external-link-alt mr-1"></i> View Live Tour Page
                    </a>
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

            <div class="row">
                <!-- Concert List Table -->
                <div class="col-lg-8">
                    <div class="card card-outline card-danger elevation-2">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-calendar-alt mr-1"></i> Tour Dates (Table: <code>concert_schedules</code>)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover datatable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Location</th>
                                            <th>Venue</th>
                                            <th>Status</th>
                                            <th>Orders</th>
                                            <th class="text-center" style="width: 170px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($concerts as $c): ?>
                                            <tr>
                                                <td class="font-weight-bold text-center">#<?php echo $c['id']; ?></td>
                                                <td><strong><?php echo date("M d, Y", strtotime($c['concert_date'])); ?></strong></td>
                                                <td><span class="font-weight-bold"><?php echo htmlspecialchars($c['location']); ?></span></td>
                                                <td><?php echo htmlspecialchars($c['venue']); ?></td>
                                                <td>
                                                    <?php if ($c['ticket_status'] === 'AVAILABLE'): ?>
                                                        <span class="badge badge-success px-2 py-1">AVAILABLE</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-2 py-1">SOLD OUT</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info"><?php echo (int)$c['total_orders']; ?> orders</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick='openEditConcertModal(<?php echo htmlspecialchars(json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)' title="Edit Concert">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Toggle Status -->
                                                        <?php if ($c['ticket_status'] === 'AVAILABLE'): ?>
                                                            <form method="POST" style="display:inline; margin-left:2px;">
                                                                <input type="hidden" name="action" value="toggle_status">
                                                                <input type="hidden" name="concert_id" value="<?php echo $c['id']; ?>">
                                                                <input type="hidden" name="new_status" value="SOLD OUT">
                                                                <button type="submit" class="btn btn-xs btn-outline-warning" title="Set to SOLD OUT">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form method="POST" style="display:inline; margin-left:2px;">
                                                                <input type="hidden" name="action" value="toggle_status">
                                                                <input type="hidden" name="concert_id" value="<?php echo $c['id']; ?>">
                                                                <input type="hidden" name="new_status" value="AVAILABLE">
                                                                <button type="submit" class="btn btn-xs btn-outline-success" title="Set to AVAILABLE">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <!-- Delete -->
                                                        <form method="POST" style="display:inline; margin-left: 2px;" onsubmit="return confirm('Deleting this concert will also delete related ticket orders. Continue?');">
                                                            <input type="hidden" name="action" value="delete_concert">
                                                            <input type="hidden" name="concert_id" value="<?php echo $c['id']; ?>">
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete Concert">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Concert Form -->
                <div class="col-lg-4">
                    <div class="card card-outline card-dark elevation-2">
                        <div class="card-header bg-dark">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-plus-circle mr-1"></i> Add New Tour Date
                            </h3>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_concert">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Tour Title</label>
                                    <input type="text" name="tour_title" class="form-control" value='World Tour: "The First Chapter"' required>
                                </div>
                                <div class="form-group">
                                    <label>Concert Date <span class="text-danger">*</span></label>
                                    <input type="date" name="concert_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Location (City, Country) <span class="text-danger">*</span></label>
                                    <input type="text" name="location" class="form-control" placeholder="e.g. SINGAPORE" required>
                                </div>
                                <div class="form-group">
                                    <label>Venue Name <span class="text-danger">*</span></label>
                                    <input type="text" name="venue" class="form-control" placeholder="e.g. SINGAPORE INDOOR STADIUM" required>
                                </div>
                                <div class="form-group">
                                    <label>Initial Ticket Status</label>
                                    <select name="ticket_status" class="form-control">
                                        <option value="AVAILABLE" selected>AVAILABLE</option>
                                        <option value="SOLD OUT">SOLD OUT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-danger btn-block font-weight-bold">
                                    <i class="fas fa-save mr-1"></i> SAVE CONCERT DATE
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Edit Concert Modal -->
<div class="modal fade" id="editConcertModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_concert">
                <input type="hidden" name="concert_id" id="edit_concert_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Concert Schedule</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tour Title</label>
                        <input type="text" name="tour_title" id="edit_tour_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Concert Date <span class="text-danger">*</span></label>
                        <input type="date" name="concert_date" id="edit_concert_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Location (City, Country) <span class="text-danger">*</span></label>
                        <input type="text" name="location" id="edit_location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Venue Name <span class="text-danger">*</span></label>
                        <input type="text" name="venue" id="edit_venue" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Ticket Status <span class="text-danger">*</span></label>
                        <select name="ticket_status" id="edit_ticket_status" class="form-control" required>
                            <option value="AVAILABLE">AVAILABLE</option>
                            <option value="SOLD OUT">SOLD OUT</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Concert</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditConcertModal(c) {
    document.getElementById('edit_concert_id').value = c.id;
    document.getElementById('edit_tour_title').value = c.tour_title;
    document.getElementById('edit_concert_date').value = c.concert_date;
    document.getElementById('edit_location').value = c.location;
    document.getElementById('edit_venue').value = c.venue;
    document.getElementById('edit_ticket_status').value = c.ticket_status;
    $('#editConcertModal').modal('show');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
