<?php
$pageTitle = "Albums & Tracks";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// 1. Handle Add Album
if (isset($_POST['action']) && $_POST['action'] === 'add_album') {
    $albId       = trim($_POST['album_id'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $releaseDate = trim($_POST['release_date'] ?? date('Y-m-d'));
    $coverPath   = trim($_POST['cover_path'] ?? 'images/greengreenalbum.jpg');

    // Handle file upload if provided
    if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['cover_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $safeName = 'album_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['cover_file']['tmp_name'], $uploadDir . $safeName)) {
                $coverPath = 'images/' . $safeName;
            }
        }
    }

    if (!empty($albId) && !empty($title)) {
        $stmt = $conn->prepare("INSERT INTO `albums` (`id`, `title`, `release_date`, `cover_path`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $albId, $title, $releaseDate, $coverPath);
        if ($stmt->execute()) {
            $alertMsg = "Album <strong>" . htmlspecialchars($title) . "</strong> created successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to add album: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// 2. Handle Edit Album
if (isset($_POST['action']) && $_POST['action'] === 'edit_album') {
    $oldAlbId    = trim($_POST['old_album_id'] ?? '');
    $newAlbId    = trim($_POST['album_id'] ?? $oldAlbId);
    $title       = trim($_POST['title'] ?? '');
    $releaseDate = trim($_POST['release_date'] ?? '');
    $coverPath   = trim($_POST['cover_path'] ?? '');

    // Handle file upload if provided
    if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['cover_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $safeName = 'album_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['cover_file']['tmp_name'], $uploadDir . $safeName)) {
                $coverPath = 'images/' . $safeName;
            }
        }
    }

    if (!empty($oldAlbId) && !empty($newAlbId) && !empty($title)) {
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        $stmt = $conn->prepare("UPDATE `albums` SET `id` = ?, `title` = ?, `release_date` = ?, `cover_path` = ? WHERE `id` = ?");
        $stmt->bind_param("sssss", $newAlbId, $title, $releaseDate, $coverPath, $oldAlbId);
        if ($stmt->execute()) {
            if ($oldAlbId !== $newAlbId) {
                $updTrk = $conn->prepare("UPDATE `tracks` SET `album_id` = ? WHERE `album_id` = ?");
                $updTrk->bind_param("ss", $newAlbId, $oldAlbId);
                $updTrk->execute();
                $updTrk->close();
            }
            $alertMsg = "Album <strong>" . htmlspecialchars($title) . "</strong> updated successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update album: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    }
}

// 3. Handle Delete Album
if (isset($_POST['action']) && $_POST['action'] === 'delete_album') {
    $albId = trim($_POST['album_id'] ?? '');
    if (!empty($albId)) {
        $stmt = $conn->prepare("DELETE FROM `albums` WHERE `id` = ?");
        $stmt->bind_param("s", $albId);
        if ($stmt->execute()) {
            $alertMsg = "Album {$albId} and all its tracks deleted successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete album: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// 4. Handle Add Track
if (isset($_POST['action']) && $_POST['action'] === 'add_track') {
    $albId       = trim($_POST['album_id'] ?? '');
    $trackNum    = (int)($_POST['track_number'] ?? 1);
    $trackTitle  = trim($_POST['title'] ?? '');
    $duration    = trim($_POST['duration'] ?? '3:00');
    $badge       = trim($_POST['badge'] ?? 'Full');
    $audioSource = trim($_POST['audio_source'] ?? '');

    // Handle audio upload if provided
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../musics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['audio_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
            $safeName = 'track_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $uploadDir . $safeName)) {
                $audioSource = 'musics/' . $safeName;
            }
        }
    }

    if (!empty($albId) && !empty($trackTitle)) {
        $stmt = $conn->prepare("INSERT INTO `tracks` (`album_id`, `track_number`, `title`, `duration`, `badge`, `audio_source`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $albId, $trackNum, $trackTitle, $duration, $badge, $audioSource);
        if ($stmt->execute()) {
            $alertMsg = "Song <strong>" . htmlspecialchars($trackTitle) . "</strong> added to album {$albId}!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to add track: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// 5. Handle Edit Track
if (isset($_POST['action']) && $_POST['action'] === 'edit_track') {
    $trackId     = (int)($_POST['track_id'] ?? 0);
    $albId       = trim($_POST['album_id'] ?? '');
    $trackNum    = (int)($_POST['track_number'] ?? 1);
    $trackTitle  = trim($_POST['title'] ?? '');
    $duration    = trim($_POST['duration'] ?? '');
    $badge       = trim($_POST['badge'] ?? 'Full');
    $audioSource = trim($_POST['audio_source'] ?? '');

    // Handle audio upload if provided
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../musics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['audio_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
            $safeName = 'track_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $uploadDir . $safeName)) {
                $audioSource = 'musics/' . $safeName;
            }
        }
    }

    if ($trackId > 0 && !empty($trackTitle)) {
        $stmt = $conn->prepare("UPDATE `tracks` SET `album_id` = ?, `track_number` = ?, `title` = ?, `duration` = ?, `badge` = ?, `audio_source` = ? WHERE `id` = ?");
        $stmt->bind_param("sissssi", $albId, $trackNum, $trackTitle, $duration, $badge, $audioSource, $trackId);
        if ($stmt->execute()) {
            $alertMsg = "Song <strong>" . htmlspecialchars($trackTitle) . "</strong> updated successfully!";
            $alertType = "success";
        } else {
            $alertMsg = "Failed to update track: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// 6. Handle Delete Track
if (isset($_POST['action']) && $_POST['action'] === 'delete_track') {
    $trackId = (int)($_POST['track_id'] ?? 0);
    if ($trackId > 0) {
        $stmt = $conn->prepare("DELETE FROM `tracks` WHERE `id` = ?");
        $stmt->bind_param("i", $trackId);
        if ($stmt->execute()) {
            $alertMsg = "Track removed successfully.";
            $alertType = "warning";
        } else {
            $alertMsg = "Failed to delete track: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch albums with tracks
$albums = [];
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

include __DIR__ . '/includes/header.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">Music Discography</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#addAlbumModal">
                        <i class="fas fa-plus-circle mr-1"></i> Add Album
                    </button>
                    <button class="btn btn-dark font-weight-bold ml-1" data-toggle="modal" data-target="#addTrackModal">
                        <i class="fas fa-music mr-1"></i> Add Song / Track
                    </button>
                    <a href="../music.php" target="_blank" class="btn btn-outline-dark ml-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Live Music Page
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
                <?php if (empty($albums)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No albums found. Click "Add Album" to create one.
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($albums as $alb): ?>
                    <div class="col-lg-6">
                        <div class="card card-outline card-danger elevation-2">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold m-0">
                                    <i class="fas fa-compact-disc mr-1 text-danger"></i> <?php echo htmlspecialchars($alb['title']); ?>
                                </h3>
                                <div class="card-tools ml-auto">
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick='openEditAlbumModal(<?php echo htmlspecialchars(json_encode($alb, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)'>
                                        <i class="fas fa-edit"></i> Edit Album
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Deleting album <?php echo htmlspecialchars($alb['title']); ?> will delete all its songs! Continue?');">
                                        <input type="hidden" name="action" value="delete_album">
                                        <input type="hidden" name="album_id" value="<?php echo htmlspecialchars($alb['id']); ?>">
                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <img src="../<?php echo htmlspecialchars($alb['cover_path']); ?>" alt="Cover" class="img-fluid rounded elevation-1" style="background:#222; max-height:140px; width:100%; object-fit:cover;" onerror="this.src='../images/greengreenalbum.jpg';">
                                    </div>
                                    <div class="col-8">
                                        <h5 class="font-weight-bold mb-1"><?php echo htmlspecialchars($alb['title']); ?></h5>
                                        <p class="text-muted text-sm mb-1">Album ID: <code class="font-weight-bold text-danger"><?php echo htmlspecialchars($alb['id']); ?></code></p>
                                        <p class="text-muted text-sm mb-1">Released: <strong><?php echo date("M d, Y", strtotime($alb['release_date'])); ?></strong></p>
                                        <p class="text-muted text-sm mb-2">Total: <strong><?php echo count($alb['tracks']); ?> Songs</strong></p>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-xs btn-primary font-weight-bold" onclick='openEditAlbumModal(<?php echo htmlspecialchars(json_encode($alb, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)'>
                                                <i class="fas fa-edit"></i> Edit Info & Cover
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-danger ml-1" onclick="openAddTrackForAlbum('<?php echo htmlspecialchars($alb['id']); ?>')">
                                                <i class="fas fa-plus"></i> Add Track
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-sm table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 35px;">#</th>
                                            <th>Song Title (Klik untuk edit)</th>
                                            <th style="width: 60px;">Dur</th>
                                            <th style="width: 65px;">Type</th>
                                            <th style="width: 80px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($alb['tracks'])): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted text-sm py-2">No songs in this album yet.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($alb['tracks'] as $t): ?>
                                            <tr>
                                                <td class="font-weight-bold text-muted"><?php echo (int)$t['track_number']; ?></td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick='openEditTrackModal(<?php echo htmlspecialchars(json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)' class="text-dark font-weight-bold text-decoration-none d-inline-block" title="Klik untuk edit track ini">
                                                        <span><?php echo htmlspecialchars($t['title']); ?></span>
                                                        <i class="fas fa-pencil-alt fa-xs text-primary ml-1" title="Edit track"></i>
                                                    </a>
                                                    <div class="text-xs text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <code><?php echo htmlspecialchars($t['audio_source']); ?></code>
                                                    </div>
                                                </td>
                                                <td><small class="badge badge-light"><?php echo htmlspecialchars($t['duration']); ?></small></td>
                                                <td>
                                                    <?php if ($t['badge'] === 'Full'): ?>
                                                        <span class="badge badge-danger">Full</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Preview</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick='openEditTrackModal(<?php echo htmlspecialchars(json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)' title="Edit Track">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete track <?php echo htmlspecialchars($t['title']); ?>?');">
                                                        <input type="hidden" name="action" value="delete_track">
                                                        <input type="hidden" name="track_id" value="<?php echo $t['id']; ?>">
                                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete Track">
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
                <?php endforeach; ?>
            </div>

        </div>
    </section>
</div>

<!-- Modal: Add Album -->
<div class="modal fade" id="addAlbumModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_album">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-compact-disc mr-1"></i> Add New Album</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Album Code / ID <span class="text-danger">*</span></label>
                        <input type="text" name="album_id" class="form-control" placeholder="e.g. album3" required>
                        <small class="text-muted">Unique ID, no spaces (e.g. album1, album2, album3)</small>
                    </div>
                    <div class="form-group">
                        <label>Album Title / Nama Album <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. NEXT GENERATION" required>
                    </div>
                    <div class="form-group">
                        <label>Release Date <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Foto Cover Album</label>
                        <input type="file" name="cover_file" class="form-control-file" accept="image/*">
                        <small class="text-muted">Pilih file foto dari komputer (otomatis disimpan ke folder <code>images/</code>)</small>
                    </div>
                    <div class="form-group">
                        <label>Atau Path File Foto Cover</label>
                        <input type="text" name="cover_path" class="form-control" value="images/greengreenalbum.jpg">
                        <small class="text-muted">Path relatif project (e.g. <code>images/whatyouwantalbum.jpg</code>)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-save mr-1"></i> Save Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Album -->
<div class="modal fade" id="editAlbumModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_album">
                <input type="hidden" name="old_album_id" id="edit_album_id_old">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Album</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <label class="d-block font-weight-bold">Cover Preview</label>
                        <img id="edit_album_cover_preview" src="" alt="Album Cover" class="img-thumbnail elevation-1" style="max-height: 140px; max-width: 140px; object-fit: cover;">
                    </div>
                    <div class="form-group">
                        <label>Album ID <span class="text-danger">*</span></label>
                        <input type="text" name="album_id" id="edit_album_id" class="form-control" required>
                        <small class="text-muted">ID Album (e.g. <code>album1</code>, <code>album2</code>). Jika diubah, relasi lagu otomatis diperbarui.</small>
                    </div>
                    <div class="form-group">
                        <label>Album Title / Nama Album <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_album_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Release Date / Tanggal Rilis <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" id="edit_album_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Ganti Foto Album (Upload File Baru)</label>
                        <input type="file" name="cover_file" id="edit_album_file" class="form-control-file" accept="image/*" onchange="previewEditCover(this)">
                        <small class="text-muted">Pilih file foto cover baru untuk mengganti gambar saat ini.</small>
                    </div>
                    <div class="form-group">
                        <label>Path File Foto Album</label>
                        <input type="text" name="cover_path" id="edit_album_cover" class="form-control" required oninput="document.getElementById('edit_album_cover_preview').src = '../' + this.value">
                        <small class="text-muted">Path relatif: e.g. <code>images/greengreenalbum.jpg</code></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Track -->
<div class="modal fade" id="addTrackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_track">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-music mr-1"></i> Add Track to Album</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Album <span class="text-danger">*</span></label>
                        <select name="album_id" id="track_album_select" class="form-control" required>
                            <?php foreach ($albums as $alb): ?>
                                <option value="<?php echo htmlspecialchars($alb['id']); ?>"><?php echo htmlspecialchars($alb['title']); ?> (<?php echo htmlspecialchars($alb['id']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Song Title / Nama Track <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control font-weight-bold" placeholder="e.g. REDRED" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Track Number <span class="text-danger">*</span></label>
                                <input type="number" name="track_number" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Duration <span class="text-danger">*</span></label>
                                <input type="text" name="duration" class="form-control" placeholder="e.g. 2:45" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Type / Badge <span class="text-danger">*</span></label>
                        <select name="badge" class="form-control" required>
                            <option value="Full">Full (Plays local MP3 player)</option>
                            <option value="Preview">Preview (Plays Spotify embed / Preview player)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload File Audio MP3 (Opsional)</label>
                        <input type="file" name="audio_file" class="form-control-file" accept=".mp3,.wav,.ogg,.m4a">
                        <small class="text-muted">Upload file MP3 (akan otomatis disimpan ke folder <code>musics/</code>)</small>
                    </div>
                    <div class="form-group">
                        <label>Audio Source / File Path / Spotify Embed URL <span class="text-danger">*</span></label>
                        <input type="text" name="audio_source" class="form-control" placeholder="e.g. musics/acai.mp3 OR spotify embed url" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-save mr-1"></i> Save Track</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Track -->
<div class="modal fade" id="editTrackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_track">
                <input type="hidden" name="track_id" id="edit_track_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Track / Lagu</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Track / Judul Lagu <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_track_title" class="form-control font-weight-bold" required>
                        <small class="text-muted">Ubah nama judul lagu di sini.</small>
                    </div>
                    <div class="form-group">
                        <label>Album <span class="text-danger">*</span></label>
                        <select name="album_id" id="edit_track_album_id" class="form-control" required>
                            <?php foreach ($albums as $alb): ?>
                                <option value="<?php echo htmlspecialchars($alb['id']); ?>"><?php echo htmlspecialchars($alb['title']); ?> (<?php echo htmlspecialchars($alb['id']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Track Number <span class="text-danger">*</span></label>
                                <input type="number" name="track_number" id="edit_track_number" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Duration <span class="text-danger">*</span></label>
                                <input type="text" name="duration" id="edit_track_duration" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Type / Badge <span class="text-danger">*</span></label>
                        <select name="badge" id="edit_track_badge" class="form-control" required>
                            <option value="Full">Full (Memutar audio MP3 langsung)</option>
                            <option value="Preview">Preview (Memutar Spotify embed / preview)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload File Audio Baru (Opsional)</label>
                        <input type="file" name="audio_file" class="form-control-file" accept=".mp3,.wav,.ogg,.m4a">
                        <small class="text-muted">Pilih file audio baru untuk mengganti audio yang ada.</small>
                    </div>
                    <div class="form-group">
                        <label>Audio Source / File Path / Spotify URL <span class="text-danger">*</span></label>
                        <input type="text" name="audio_source" id="edit_track_source" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Track</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditAlbumModal(alb) {
    document.getElementById('edit_album_id_old').value = alb.id;
    document.getElementById('edit_album_id').value = alb.id;
    document.getElementById('edit_album_title').value = alb.title;
    document.getElementById('edit_album_date').value = alb.release_date;
    document.getElementById('edit_album_cover').value = alb.cover_path;
    document.getElementById('edit_album_cover_preview').src = '../' + alb.cover_path;
    if (document.getElementById('edit_album_file')) {
        document.getElementById('edit_album_file').value = '';
    }
    $('#editAlbumModal').modal('show');
}

function previewEditCover(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit_album_cover_preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function openAddTrackForAlbum(albumId) {
    document.getElementById('track_album_select').value = albumId;
    $('#addTrackModal').modal('show');
}

function openEditTrackModal(t) {
    document.getElementById('edit_track_id').value = t.id;
    document.getElementById('edit_track_album_id').value = t.album_id;
    document.getElementById('edit_track_number').value = t.track_number;
    document.getElementById('edit_track_title').value = t.title;
    document.getElementById('edit_track_duration').value = t.duration;
    document.getElementById('edit_track_badge').value = t.badge;
    document.getElementById('edit_track_source').value = t.audio_source;
    $('#editTrackModal').modal('show');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
