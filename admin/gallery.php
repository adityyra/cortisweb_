<?php
$pageTitle = "Gallery Photos";
require_once __DIR__ . '/../db_connect.php';

$alertMsg = "";
$alertType = "";

// 1. Handle Add Photo
if (isset($_POST['action']) && $_POST['action'] === 'add_photo') {
    $imagePath = trim($_POST['image_path'] ?? '');
    $caption   = trim($_POST['caption'] ?? '');

    // Handle file upload if provided
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images_gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['photo_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $safeName = 'photo_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadDir . $safeName)) {
                $imagePath = 'images_gallery/' . $safeName;
            }
        }
    }

    if (!empty($imagePath)) {
        $stmt = $conn->prepare("INSERT INTO `gallery_photos` (`image_path`, `caption`) VALUES (?, ?)");
        $stmt->bind_param("ss", $imagePath, $caption);
        if ($stmt->execute()) {
            $alertMsg = "Foto berhasil ditambahkan ke galeri!";
            $alertType = "success";
        } else {
            $alertMsg = "Gagal menambah foto: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    } else {
        $alertMsg = "Silakan upload file foto atau isi path foto.";
        $alertType = "warning";
    }
}

// 2. Handle Edit Photo
if (isset($_POST['action']) && $_POST['action'] === 'edit_photo') {
    $photoId   = (int)($_POST['photo_id'] ?? 0);
    $imagePath = trim($_POST['image_path'] ?? '');
    $caption   = trim($_POST['caption'] ?? '');

    // Handle file upload if provided
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images_gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $origName = basename($_FILES['photo_file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $safeName = 'photo_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadDir . $safeName)) {
                $imagePath = 'images_gallery/' . $safeName;
            }
        }
    }

    if ($photoId > 0 && !empty($imagePath)) {
        $stmt = $conn->prepare("UPDATE `gallery_photos` SET `image_path` = ?, `caption` = ? WHERE `id` = ?");
        $stmt->bind_param("ssi", $imagePath, $caption, $photoId);
        if ($stmt->execute()) {
            $alertMsg = "Foto dan caption #{$photoId} berhasil diperbarui!";
            $alertType = "success";
        } else {
            $alertMsg = "Gagal update foto: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// 3. Handle Delete Photo
if (isset($_POST['action']) && $_POST['action'] === 'delete_photo') {
    $pId = (int)($_POST['photo_id'] ?? 0);
    if ($pId > 0) {
        $stmt = $conn->prepare("DELETE FROM `gallery_photos` WHERE `id` = ?");
        $stmt->bind_param("i", $pId);
        if ($stmt->execute()) {
            $alertMsg = "Foto berhasil dihapus dari galeri.";
            $alertType = "warning";
        } else {
            $alertMsg = "Gagal menghapus foto: " . $conn->error;
            $alertType = "danger";
        }
        $stmt->close();
    }
}

// Fetch photos
$photos = [];
$res = $conn->query("SELECT * FROM `gallery_photos` ORDER BY `id` ASC");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $photos[] = $r;
    }
}

// Check if specific edit ID requested via GET
$autoEditPhoto = null;
if (isset($_GET['edit_id'])) {
    $eId = (int)$_GET['edit_id'];
    foreach ($photos as $p) {
        if ((int)$p['id'] === $eId) {
            $autoEditPhoto = $p;
            break;
        }
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
                    <h1 class="m-0 font-weight-bold">Gallery Photos & Captions</h1>
                    <small class="text-muted">Kelola koleksi foto galeri, edit foto, dan update caption yang tampil di website.</small>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-danger font-weight-bold shadow-sm" data-toggle="modal" data-target="#addPhotoModal">
                        <i class="fas fa-camera mr-1"></i> + Tambah Foto Galeri
                    </button>
                    <a href="../galeri.php" target="_blank" class="btn btn-outline-dark ml-2 shadow-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Halaman Galeri
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
                <?php if (empty($photos)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-images fa-2x mb-2 d-block text-muted"></i>
                            Belum ada foto di galeri. Klik tombol <strong>+ Tambah Foto Galeri</strong> untuk mulai mengunggah foto.
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($photos as $p): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card elevation-2 h-100 border-0 shadow-sm">
                            <div style="position: relative; overflow: hidden; background: #000; height: 200px;">
                                <img src="../<?php echo htmlspecialchars($p['image_path']); ?>" class="card-img-top" style="width: 100%; height: 100%; object-fit: cover;" alt="Photo" onerror="this.src='../images/cortisstreet.jpg';">
                                <span class="badge badge-dark" style="position: absolute; top: 8px; left: 8px; opacity: 0.85;">#<?php echo $p['id']; ?></span>
                            </div>
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="mb-2">
                                        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Caption:</small>
                                        <p class="card-text text-sm font-weight-bold text-dark mb-1" style="min-height: 38px; line-height: 1.3;">
                                            <?php if (!empty($p['caption'])): ?>
                                                <i class="fas fa-quote-left fa-xs text-danger mr-1"></i><?php echo htmlspecialchars($p['caption']); ?>
                                            <?php else: ?>
                                                <span class="text-muted font-italic">Tidak ada caption</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <small class="text-muted d-block text-truncate" title="<?php echo htmlspecialchars($p['image_path']); ?>">
                                        <i class="fas fa-link fa-xs mr-1"></i><code><?php echo htmlspecialchars($p['image_path']); ?></code>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-light p-2 d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-xs btn-primary font-weight-bold px-2 py-1" onclick='openEditPhotoModal(<?php echo htmlspecialchars(json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, "UTF-8"); ?>)'>
                                    <i class="fas fa-edit mr-1"></i> Edit Foto
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus foto ini dari galeri?');">
                                    <input type="hidden" name="action" value="delete_photo">
                                    <input type="hidden" name="photo_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-xs btn-outline-danger px-2 py-1">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>
</div>

<!-- Modal: Add Photo -->
<div class="modal fade" id="addPhotoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_photo">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-camera mr-1"></i> Tambah Foto Baru ke Galeri</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <img id="add_photo_preview" src="../images/cortisstreet.jpg" class="img-thumbnail elevation-1 mb-2" style="max-height: 150px; display: none;">
                    </div>
                    <div class="form-group">
                        <label>Upload File Foto <span class="text-danger">*</span></label>
                        <input type="file" name="photo_file" class="form-control-file" accept="image/*" onchange="previewAddPhoto(this)">
                        <small class="text-muted">Pilih foto dari komputer (otomatis disimpan ke <code>images_gallery/</code>).</small>
                    </div>
                    <div class="form-group">
                        <label>Atau Path File Foto</label>
                        <input type="text" name="image_path" id="add_image_path" class="form-control" placeholder="e.g. images_gallery/1.jpg">
                        <small class="text-muted">Jika tidak upload file, isi path relatif seperti <code>images_gallery/1.jpg</code></small>
                    </div>
                    <div class="form-group">
                        <label>Caption Foto (Teks yang tampil di Galeri) <span class="text-danger">*</span></label>
                        <input type="text" name="caption" class="form-control" placeholder="Contoh: World Tour Seoul Opening Stage" required>
                        <small class="text-muted">Caption ini akan langsung terlihat pada foto di halaman galeri.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Photo -->
<div class="modal fade" id="editPhotoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_photo">
                <input type="hidden" name="photo_id" id="edit_photo_id">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Foto & Caption Galeri</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <label class="d-block font-weight-bold">Preview Foto Saat Ini</label>
                        <img id="edit_photo_preview" src="" class="img-thumbnail elevation-1 mb-2" style="max-height: 180px; max-width: 100%; object-fit: contain;">
                    </div>
                    <div class="form-group">
                        <label>Ganti File Foto (Upload Baru)</label>
                        <input type="file" name="photo_file" id="edit_photo_file" class="form-control-file" accept="image/*" onchange="previewEditPhoto(this)">
                        <small class="text-muted">Pilih file baru jika ingin mengganti gambar foto.</small>
                    </div>
                    <div class="form-group">
                        <label>Path Foto <span class="text-danger">*</span></label>
                        <input type="text" name="image_path" id="edit_image_path" class="form-control" required oninput="document.getElementById('edit_photo_preview').src = '../' + this.value">
                        <small class="text-muted">Path relatif: e.g. <code>images_gallery/1.jpg</code></small>
                    </div>
                    <div class="form-group">
                        <label>Caption Foto (Tampil pada Foto di Galeri) <span class="text-danger">*</span></label>
                        <input type="text" name="caption" id="edit_caption" class="form-control font-weight-bold" placeholder="Masukkan caption foto..." required>
                        <small class="text-muted">Caption ini langsung terlihat pada foto di galeri website.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Update Foto & Caption</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewAddPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var prev = document.getElementById('add_photo_preview');
            prev.src = e.target.result;
            prev.style.display = 'inline-block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function openEditPhotoModal(p) {
    document.getElementById('edit_photo_id').value = p.id;
    document.getElementById('edit_image_path').value = p.image_path;
    document.getElementById('edit_caption').value = p.caption || '';
    document.getElementById('edit_photo_preview').src = '../' + p.image_path;
    if (document.getElementById('edit_photo_file')) {
        document.getElementById('edit_photo_file').value = '';
    }
    $('#editPhotoModal').modal('show');
}

function previewEditPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit_photo_preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

<?php if ($autoEditPhoto): ?>
$(document).ready(function() {
    openEditPhotoModal(<?php echo json_encode($autoEditPhoto); ?>);
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
