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
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'petugas',
            'wilayah_tugas' => $_POST['wilayah_tugas'] ?? '',
        ];
        $res = callApi('POST', 'admin/users', $data);
        if (isset($res['status']) && $res['status'] === 'success') {
            $msgSuccess = "User berhasil ditambahkan.";
        } else {
            $msgError = $res['message'] ?? "Gagal menambahkan user.";
        }
    } 
    elseif ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'petugas',
            'wilayah_tugas' => $_POST['wilayah_tugas'] ?? '',
        ];
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        $res = callApi('PUT', 'admin/users/' . $id, $data);
        if (isset($res['status']) && $res['status'] === 'success') {
            $msgSuccess = "User berhasil diperbarui.";
        } else {
            $msgError = $res['message'] ?? "Gagal memperbarui user.";
        }
    } 
    elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $res = callApi('DELETE', 'admin/users/' . $id);
        if (isset($res['status']) && $res['status'] === 'success') {
            $msgSuccess = "User berhasil dihapus.";
        } else {
            $msgError = $res['message'] ?? "Gagal menghapus user.";
        }
    }
}

// Fetch All Users
$usersRes = callApi('GET', 'admin/users');
$usersList = (isset($usersRes['status']) && $usersRes['status'] === 'success') ? ($usersRes['data'] ?? []) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Panel Admin</title>
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
            <h2 class="h4 fw-bold">Kelola Pengguna (Admin/Petugas)</h2>
            <button class="btn btn-teal text-white" style="background-color: #0d9488;" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg"></i> Tambah User
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
                                <th class="p-3">Nama</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Role</th>
                                <th class="p-3">Wilayah Tugas</th>
                                <th class="p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usersList)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data user.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usersList as $u): ?>
                                    <tr class="border-bottom">
                                        <td class="p-3 fw-semibold"><?= htmlspecialchars($u['name']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($u['email']) ?></td>
                                        <td class="p-3">
                                            <?php if (strtolower($u['role']) === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Petugas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3"><?= htmlspecialchars($u['wilayah_tugas'] ?? '-') ?></td>
                                        <td class="p-3">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        data-id="<?= $u['id'] ?>"
                                                        data-name="<?= htmlspecialchars($u['name']) ?>"
                                                        data-email="<?= htmlspecialchars($u['email']) ?>"
                                                        data-role="<?= htmlspecialchars($u['role']) ?>"
                                                        data-wilayah="<?= htmlspecialchars($u['wilayah_tugas'] ?? '') ?>">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')" class="m-0">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
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
                    <h5 class="modal-title fw-bold" id="createModalLabel">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" required class="form-control" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" required class="form-control" placeholder="budi@kampusresik.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" required class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select">
                            <option value="petugas" selected>Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wilayah Tugas</label>
                        <input type="text" name="wilayah_tugas" class="form-control" placeholder="Contoh: Gedung Rektorat (kosongkan untuk Admin)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal text-white" style="background-color: #0d9488;">Simpan User</button>
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
                    <h5 class="modal-title fw-bold" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="edit_email" required class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" id="edit_role" class="form-select">
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wilayah Tugas</label>
                        <input type="text" name="wilayah_tugas" id="edit_wilayah" class="form-control">
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
                const email = button.getAttribute('data-email');
                const role = button.getAttribute('data-role');
                const wilayah = button.getAttribute('data-wilayah');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_role').value = role;
                document.getElementById('edit_wilayah').value = wilayah;
            });
        }
    </script>
</body>
</html>