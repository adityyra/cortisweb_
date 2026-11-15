<?php
$pageTitle = "Official Notices";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// Handle Add Notice
if (isset($_POST['action']) && $_POST['action'] === 'add_notice') {
    $noticeNum = (int)($_POST['notice_number'] ?? 0);
    $category  = trim($_POST['category'] ?? 'info');
    $title     = trim($_POST['title'] ?? '');
    $date      = trim($_POST['notice_date'] ?? date('Y-m-d'));
    $content   = trim($_POST['content'] ?? '');

    if ($noticeNum > 0 && !empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO `notices` (`notice_number`, `category`, `title`, `notice_date`, `content`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $noticeNum, $category, $title, $date, $content);
        if ($stmt->execute()) {
            $alertMsg = "Notice #{$noticeNum} posted successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to add notice: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Edit Notice
if (isset($_POST['action']) && $_POST['action'] === 'edit_notice') {
    $noticeId  = (int)($_POST['notice_id'] ?? 0);
    $noticeNum = (int)($_POST['notice_number'] ?? 0);
    $category  = trim($_POST['category'] ?? 'info');
    $title     = trim($_POST['title'] ?? '');
    $date      = trim($_POST['notice_date'] ?? date('Y-m-d'));
    $content   = trim($_POST['content'] ?? '');

    if ($noticeId > 0 && $noticeNum > 0 && !empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE `notices` SET `notice_number` = ?, `category` = ?, `title` = ?, `notice_date` = ?, `content` = ? WHERE `id` = ?");
        $stmt->bind_param("issssi", $noticeNum, $category, $title, $date, $content, $noticeId);
        if ($stmt->execute()) {
            $alertMsg = "Notice #{$noticeNum} updated successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update notice: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Delete Notice
if (isset($_POST['action']) && $_POST['action'] === 'delete_notice') {
    $noticeId = (int)($_POST['notice_id'] ?? 0);
    if ($noticeId > 0) {
        $stmt = $conn->prepare("DELETE FROM `notices` WHERE `id` = ?");
        $stmt->bind_param("i", $noticeId);
        if ($stmt->execute()) {
            $alertMsg = "Notice deleted successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete notice: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch notices
$notices = [];
$res = $conn->query("SELECT * FROM `notices` ORDER BY `notice_number` DESC");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $notices[] = $r;
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">Official Band Notices</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="../notice.php" target="_blank" class="btn btn-outline-dark">
                        <i class="fas fa-external-link-alt mr-1"></i> View Live Notice Page
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
                    <button type="button" data-dismiss="alert" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Notice List -->
                <div class="col-lg-8">
                    <div class="card card-outline card-danger elevation-2">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-bullhorn mr-1"></i> Published Announcements (Table: <code>notices</code>)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover datatable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 70px;">No.</th>
                                            <th style="width: 90px;">Category</th>
                                            <th>Notice Title</th>
                                            <th style="width: 100px;">Date</th>
                                            <th style="width: 110px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notices as $n): ?>
                                            <tr>
                                                <td class="font-weight-bold text-center">#<?php echo htmlspecialchars($n['notice_number']); ?></td>
                                                <td>
                                                    <?php if ($n['category'] === 'birthday'): ?>
                                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-birthday-cake mr-1"></i> Birthday</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-info-circle mr-1"></i> Info</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($n['title']); ?></strong>
                                                    <div><small class="text-muted"><?php echo strip_tags(substr($n['content'], 0, 80)) . '...'; ?></small></div>
                                                </td>
                                                <td><small class="text-muted"><?php echo htmlspecialchars($n['notice_date']); ?></small></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick='openEditNoticeModal(<?php echo htmlspecialchars(json_encode($n, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)'>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete notice #<?php echo $n['notice_number']; ?>?');">
                                                        <input type="hidden" name="action" value="delete_notice">
                                                        <input type="hidden" name="notice_id" value="<?php echo $n['id']; ?>">
                                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Notice Form -->
                <div class="col-lg-4">
                    <div class="card card-outline card-dark elevation-2">
                        <div class="card-header bg-dark">
                            <h3 class="card-title font-weight-bold">
                                <i class="fas fa-edit mr-1"></i> Post New Announcement
                            </h3>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_notice">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Notice Number <span class="text-danger">*</span></label>
                                    <input type="number" name="notice_number" class="form-control" placeholder="e.g. 110" required>
                                </div>
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control" required>
                                        <option value="info">Info (Tour / Release / General)</option>
                                        <option value="birthday">Birthday (Member Celebration)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="Announcement title..." required>
                                </div>
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="notice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Content (Text or HTML) <span class="text-danger">*</span></label>
                                    <textarea name="content" class="form-control" rows="5" placeholder="Enter notice content..." required></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-danger btn-block font-weight-bold">
                                    <i class="fas fa-paper-plane mr-1"></i> PUBLISH NOTICE
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Edit Notice Modal -->
<div class="modal fade" id="editNoticeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_notice">
                <input type="hidden" name="notice_id" id="edit_notice_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Announcement</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Notice Number <span class="text-danger">*</span></label>
                        <input type="number" name="notice_number" id="edit_notice_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category" id="edit_category" class="form-control" required>
                            <option value="info">Info</option>
                            <option value="birthday">Birthday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="notice_date" id="edit_notice_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="edit_content" class="form-control" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditNoticeModal(n) {
    document.getElementById('edit_notice_id').value = n.id;
    document.getElementById('edit_notice_number').value = n.notice_number;
    document.getElementById('edit_category').value = n.category;
    document.getElementById('edit_title').value = n.title;
    document.getElementById('edit_notice_date').value = n.notice_date;
    document.getElementById('edit_content').value = n.content;
    $('#editNoticeModal').modal('show');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
