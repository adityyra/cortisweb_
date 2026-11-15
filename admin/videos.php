<?php
$pageTitle = "Band Videos";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// Handle Add Video
if (isset($_POST['action']) && $_POST['action'] === 'add_video') {
    $title         = trim($_POST['title'] ?? '');
    $sourceType    = trim($_POST['source_type'] ?? 'youtube');
    $videoUrl      = trim($_POST['video_url'] ?? '');
    $thumbnailPath = trim($_POST['thumbnail_path'] ?? 'thumnails/mayyou.jpeg');
    $releaseDate   = trim($_POST['release_date'] ?? date('Y-m-d'));

    if (!empty($title) && !empty($videoUrl)) {
        $stmt = $conn->prepare("INSERT INTO `videos` (`title`, `source_type`, `video_url`, `thumbnail_path`, `release_date`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $sourceType, $videoUrl, $thumbnailPath, $releaseDate);
        if ($stmt->execute()) {
            $alertMsg = "Video <strong>" . htmlspecialchars($title) . "</strong> added successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to add video: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Edit Video
if (isset($_POST['action']) && $_POST['action'] === 'edit_video') {
    $vId           = (int)($_POST['video_id'] ?? 0);
    $title         = trim($_POST['title'] ?? '');
    $sourceType    = trim($_POST['source_type'] ?? 'youtube');
    $videoUrl      = trim($_POST['video_url'] ?? '');
    $thumbnailPath = trim($_POST['thumbnail_path'] ?? '');
    $releaseDate   = trim($_POST['release_date'] ?? '');

    if ($vId > 0 && !empty($title) && !empty($videoUrl)) {
        $stmt = $conn->prepare("UPDATE `videos` SET `title` = ?, `source_type` = ?, `video_url` = ?, `thumbnail_path` = ?, `release_date` = ? WHERE `id` = ?");
        $stmt->bind_param("sssssi", $title, $sourceType, $videoUrl, $thumbnailPath, $releaseDate, $vId);
        if ($stmt->execute()) {
            $alertMsg = "Video <strong>" . htmlspecialchars($title) . "</strong> updated successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update video: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Handle Delete Video
if (isset($_POST['action']) && $_POST['action'] === 'delete_video') {
    $vId = (int)($_POST['video_id'] ?? 0);
    if ($vId > 0) {
        $stmt = $conn->prepare("DELETE FROM `videos` WHERE `id` = ?");
        $stmt->bind_param("i", $vId);
        if ($stmt->execute()) {
            $alertMsg = "Video deleted successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete video: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch videos
$videos = [];
$res = $conn->query("SELECT * FROM `videos` ORDER BY `release_date` DESC, `id` DESC");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $videos[] = $r;
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
                    <h1 class="m-0 font-weight-bold">Band Videos & Music Videos</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#addVideoModal">
                        <i class="fas fa-video mr-1"></i> Add Video
                    </button>
                    <a href="../videos.php" target="_blank" class="btn btn-outline-dark ml-2">
                        <i class="fas fa-external-link-alt mr-1"></i> View Live Videos Page
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

            <div class="card card-outline card-danger elevation-2">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-play-circle mr-1"></i> Video Catalog (Table: <code>videos</code>)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Video Title</th>
                                    <th style="width: 100px;">Type</th>
                                    <th>Video URL / Embed</th>
                                    <th style="width: 110px;">Release Date</th>
                                    <th style="width: 120px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($videos as $v): ?>
                                    <tr>
                                        <td class="font-weight-bold text-center">#<?php echo $v['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($v['title']); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($v['source_type'] === 'youtube'): ?>
                                                <span class="badge badge-danger"><i class="fab fa-youtube mr-1"></i> YouTube</span>
                                            <?php else: ?>
                                                <span class="badge badge-primary"><i class="fas fa-file-video mr-1"></i> Local</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><code><?php echo htmlspecialchars($v['video_url']); ?></code></small>
                                        </td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($v['release_date']); ?></small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick='openEditVideoModal(<?php echo htmlspecialchars(json_encode($v, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)'>
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete video <?php echo htmlspecialchars($v['title']); ?>?');">
                                                <input type="hidden" name="action" value="delete_video">
                                                <input type="hidden" name="video_id" value="<?php echo $v['id']; ?>">
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
    </section>
</div>

<!-- Modal: Add Video -->
<div class="modal fade" id="addVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_video">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-video mr-1"></i> Add Video</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. ME+YOU Official MV" required>
                    </div>
                    <div class="form-group">
                        <label>Source Type <span class="text-danger">*</span></label>
                        <select name="source_type" class="form-control" required>
                            <option value="youtube">YouTube Embed (e.g. https://www.youtube.com/embed/nCElUD0jvgo)</option>
                            <option value="local">Local MP4 file (e.g. videos/FaSHioN.mp4)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Video URL / Path <span class="text-danger">*</span></label>
                        <input type="text" name="video_url" class="form-control" placeholder="e.g. https://www.youtube.com/embed/..." required>
                    </div>
                    <div class="form-group">
                        <label>Thumbnail Path <span class="text-danger">*</span></label>
                        <input type="text" name="thumbnail_path" class="form-control" value="thumnails/mayyou.jpeg" required>
                    </div>
                    <div class="form-group">
                        <label>Release Date <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-save mr-1"></i> Save Video</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Video -->
<div class="modal fade" id="editVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_video">
                <input type="hidden" name="video_id" id="edit_video_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Video</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Source Type <span class="text-danger">*</span></label>
                        <select name="source_type" id="edit_source_type" class="form-control" required>
                            <option value="youtube">YouTube</option>
                            <option value="local">Local MP4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Video URL / Path <span class="text-danger">*</span></label>
                        <input type="text" name="video_url" id="edit_video_url" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Thumbnail Path <span class="text-danger">*</span></label>
                        <input type="text" name="thumbnail_path" id="edit_thumbnail_path" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Release Date <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" id="edit_release_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Video</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditVideoModal(v) {
    document.getElementById('edit_video_id').value = v.id;
    document.getElementById('edit_title').value = v.title;
    document.getElementById('edit_source_type').value = v.source_type;
    document.getElementById('edit_video_url').value = v.video_url;
    document.getElementById('edit_thumbnail_path').value = v.thumbnail_path;
    document.getElementById('edit_release_date').value = v.release_date;
    $('#editVideoModal').modal('show');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
