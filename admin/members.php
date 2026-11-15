<?php
$pageTitle = "Band Members";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// Handle Add Member
if (isset($_POST['action']) && $_POST['action'] === 'add_member') {
    $stageName = trim($_POST['stage_name'] ?? '');
    $role      = trim($_POST['role'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');
    $photoPath = trim($_POST['photo_path'] ?? 'images/profile/JAMES.jpeg');

    // Optional file upload
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/profile/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['photo_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $safeName = 'profile_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadDir . $safeName)) {
                $photoPath = 'images/profile/' . $safeName;
            }
        }
    }

    if (!empty($stageName) && !empty($role) && !empty($bio)) {
        $stmt = $conn->prepare("INSERT INTO `members` (`stage_name`, `role`, `bio`, `photo_path`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $stageName, $role, $bio, $photoPath);
        if ($stmt->execute()) {
            $alertMsg = "Member <strong>" . htmlspecialchars($stageName) . "</strong> added successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to add member: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Edit Member
if (isset($_POST['action']) && $_POST['action'] === 'edit_member') {
    $memberId  = (int)($_POST['member_id'] ?? 0);
    $stageName = trim($_POST['stage_name'] ?? '');
    $role      = trim($_POST['role'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');
    $photoPath = trim($_POST['photo_path'] ?? '');

    // Optional file upload
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/profile/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['photo_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $safeName = 'profile_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadDir . $safeName)) {
                $photoPath = 'images/profile/' . $safeName;
            }
        }
    }

    if ($memberId > 0 && !empty($stageName) && !empty($role) && !empty($bio)) {
        $stmt = $conn->prepare("UPDATE `members` SET `stage_name` = ?, `role` = ?, `bio` = ?, `photo_path` = ? WHERE `id` = ?");
        $stmt->bind_param("ssssi", $stageName, $role, $bio, $photoPath, $memberId);
        if ($stmt->execute()) {
            $alertMsg = "Member <strong>" . htmlspecialchars($stageName) . "</strong> updated successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update member: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Delete Member
if (isset($_POST['action']) && $_POST['action'] === 'delete_member') {
    $memberId = (int)($_POST['member_id'] ?? 0);
    if ($memberId > 0) {
        $stmt = $conn->prepare("DELETE FROM `members` WHERE `id` = ?");
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute()) {
            $alertMsg = "Member removed successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete member: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch members
$members = [];
$res = $conn->query("SELECT * FROM `members` ORDER BY `id` ASC");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $members[] = $r;
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
                    <h1 class="m-0 font-weight-bold">CORTIS Band Members</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#addMemberModal">
                        <i class="fas fa-user-plus mr-1"></i> Add New Member
                    </button>
                    <a href="../about.php" target="_blank" class="btn btn-outline-dark ml-2">
                        <i class="fas fa-external-link-alt mr-1"></i> View Live About Page
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
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
                <?php if (empty($members)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No band members found. Click "Add New Member" to add one.
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($members as $m): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card card-widget widget-user elevation-2">
                            <div class="widget-user-header bg-dark" style="background: linear-gradient(135deg, #1f2937, #111827);">
                                <h3 class="widget-user-username font-weight-bold"><?php echo htmlspecialchars($m['stage_name']); ?></h3>
                                <h5 class="widget-user-desc text-danger font-weight-bold"><?php echo htmlspecialchars($m['role']); ?></h5>
                            </div>
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" src="../<?php echo htmlspecialchars($m['photo_path']); ?>" alt="<?php echo htmlspecialchars($m['stage_name']); ?>" style="width: 90px; height: 90px; object-fit: cover; background:#222;" onerror="this.src='../images/profile/JAMES.jpeg';">
                            </div>
                            <div class="card-body pt-5">
                                <p class="text-muted text-sm text-center mt-2 mb-0" style="min-height: 70px;">
                                    "<?php echo htmlspecialchars($m['bio']); ?>"
                                </p>
                            </div>
                            <div class="card-footer bg-light p-2 d-flex justify-content-between align-items-center">
                                <small class="text-muted"><code><?php echo htmlspecialchars($m['photo_path']); ?></code></small>
                                <div>
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick='openEditMemberModal(<?php echo htmlspecialchars(json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete member <?php echo htmlspecialchars($m['stage_name']); ?>?');">
                                        <input type="hidden" name="action" value="delete_member">
                                        <input type="hidden" name="member_id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_member">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-1"></i> Add Band Member</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Stage Name <span class="text-danger">*</span></label>
                        <input type="text" name="stage_name" class="form-control" placeholder="e.g. JAMES" required>
                    </div>
                    <div class="form-group">
                        <label>Role / Position <span class="text-danger">*</span></label>
                        <input type="text" name="role" class="form-control" placeholder="e.g. Leader / Main Vocal" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Foto (Opsional)</label>
                        <input type="file" name="photo_file" class="form-control-file" accept="image/*">
                        <small class="text-muted">Upload file baru untuk otomatis disimpan ke folder <code>images/profile/</code></small>
                    </div>
                    <div class="form-group">
                        <label>Photo Path <span class="text-danger">*</span></label>
                        <input type="text" name="photo_path" class="form-control" value="images/profile/JAMES.jpeg" placeholder="e.g. images/profile/JAMES.jpeg" required>
                        <small class="text-muted">Path relatif gambar jika tidak upload file (e.g. <code>images/profile/JAMES.jpeg</code>)</small>
                    </div>
                    <div class="form-group">
                        <label>Biography / Description <span class="text-danger">*</span></label>
                        <textarea name="bio" class="form-control" rows="4" placeholder="Brief member description..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-save mr-1"></i> Save Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_member">
                <input type="hidden" name="member_id" id="edit_member_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit mr-1"></i> Edit Band Member</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <img id="edit_photo_preview" src="" alt="Profile Preview" class="img-circle elevation-2" style="width: 100px; height: 100px; object-fit: cover; background: #222;" onerror="this.src='../images/profile/JAMES.jpeg';">
                    </div>
                    <div class="form-group">
                        <label>Stage Name <span class="text-danger">*</span></label>
                        <input type="text" name="stage_name" id="edit_stage_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role / Position <span class="text-danger">*</span></label>
                        <input type="text" name="role" id="edit_role" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Ganti Foto (Upload File Baru)</label>
                        <input type="file" name="photo_file" id="edit_photo_file" class="form-control-file" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file foto.</small>
                    </div>
                    <div class="form-group">
                        <label>Photo Path <span class="text-danger">*</span></label>
                        <input type="text" name="photo_path" id="edit_photo_path" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Biography / Description <span class="text-danger">*</span></label>
                        <textarea name="bio" id="edit_bio" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditMemberModal(m) {
    if (typeof m === 'string') {
        try { m = JSON.parse(m); } catch(e) {}
    }
    document.getElementById('edit_member_id').value = m.id || '';
    document.getElementById('edit_stage_name').value = m.stage_name || '';
    document.getElementById('edit_role').value = m.role || '';
    document.getElementById('edit_photo_path').value = m.photo_path || '';
    var preview = document.getElementById('edit_photo_preview');
    if (preview && m.photo_path) {
        preview.src = '../' + m.photo_path;
    }
    var fileInput = document.getElementById('edit_photo_file');
    if (fileInput) {
        fileInput.value = '';
    }
    document.getElementById('edit_bio').value = m.bio || '';
    $('#editMemberModal').modal('show');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
