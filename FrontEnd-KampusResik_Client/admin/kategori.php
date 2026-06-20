<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
if (!isset($_SESSION['token'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

require_once __DIR__ . '/../includes/api_client.php';

// Handle POST actions (Create/Update/Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $data = [
            'nama_kategori' => $_POST['nama_kategori'] ?? '',
            'deskripsi_penanganan' => $_POST['deskripsi_penanganan'] ?? '',
        ];
        $res = callApi('POST', 'admin/kategori', $data);
        if (isset($res['status']) && $res['status'] === 'success') {
            $msgSuccess = "Kategori berhasil ditambahkan.";
        } else {
            $msgError = $res['message'] ?? "Gagal menambahkan kategori.";
        }
    } 
    elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $data = [
            'nama_kategori' => $_POST['nama_kategori'] ?? '',
            'deskripsi_penanganan' => $_POST['deskripsi_penanganan'] ?? '',
        ];
        $res = callApi('PUT', 'admin/kategori/' . $id, $data);
        if (isset($res['status']) && $res['status'] === 'success') {
            $msgSuccess = "Kategori berhasil diperbarui.";
        } else {
            $msgError = $res['message'] ?? "Gagal memperbarui kategori.";
        }
    } 
    elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $res = callApi('DELETE', 'admin/kategori/' . $id);
        if (isset($res['status']) && $res['status'] === 'success') {
            $msgSuccess = "Kategori berhasil dihapus.";
        } else {
            $msgError = $res['message'] ?? "Gagal menghapus kategori.";
        }
    }
}

// Fetch Kategori Data
$kategoriRes = callApi('GET', 'admin/kategori');
$categories = (isset($kategoriRes['status']) && $kategoriRes['status'] === 'success') ? ($kategoriRes['data'] ?? []) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Sampah - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbarPrivate.php'; ?>
    
    <main class="container py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold">Manajemen Kategori Sampah</h2>
            <button class="btn btn-teal text-white" style="background-color: #0d9488;" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </button>
        </div>

        <?php if (isset($msgSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msgSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($msgError)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msgError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3">Nama Kategori</th>
                                <th class="p-3">Deskripsi Penanganan</th>
                                <th class="p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr class="border-bottom">
                                        <td class="p-3 fw-semibold"><?= htmlspecialchars($cat['nama_kategori']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($cat['deskripsi_penanganan']) ?></td>
                                        <td class="p-3">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        data-id="<?= $cat['id'] ?>"
                                                        data-name="<?= htmlspecialchars($cat['nama_kategori']) ?>"
                                                        data-desc="<?= htmlspecialchars($cat['deskripsi_penanganan']) ?>">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" class="m-0">
                                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createModalLabel">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" required class="form-control" placeholder="Contoh: Sampah Organik">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Penanganan</label>
                        <textarea name="deskripsi_penanganan" required class="form-control" rows="3" placeholder="Langkah pengelolaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal text-white" style="background-color: #0d9488;">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editModalLabel">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="edit_nama_kategori" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Penanganan</label>
                        <textarea name="deskripsi_penanganan" id="edit_deskripsi_penanganan" required class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal text-white" style="background-color: #0d9488;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const desc = button.getAttribute('data-desc');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nama_kategori').value = name;
                document.getElementById('edit_deskripsi_penanganan').value = desc;
            });
        }
    </script>
</body>
</html>