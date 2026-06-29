@extends('admin.layout')

@section('title', 'Pengaturan')
@section('content')
<div class="container-fluid">
    <div class="admin-header animate-in">
        <div>
            <h4>Pengaturan</h4>
            <p>Kelola profil dan pengaturan akun admin.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 animate-in">
            <div class="admin-card">
                <div class="card-body text-center">
                    <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--adm-primary),var(--adm-primary-light));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:3rem;color:#fff;box-shadow:0 4px 14px rgba(46,125,50,.3)">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold" id="profileName">-</h5>
                    <p class="text-muted" id="profileEmail">-</p>
                    <span class="badge bg-success">Administrator</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8 animate-in">
            <div class="admin-card mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Profil</h5>
                    <form id="editProfileForm" onsubmit="event.preventDefault(); updateProfile();">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control" id="editName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="editEmail" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-admin btn-admin-primary">
                                    <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-lock me-2"></i>Ubah Password</h5>
                    <form id="changePasswordForm" onsubmit="event.preventDefault(); changePassword();">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Password Lama</label>
                                <input type="password" class="form-control" id="oldPassword" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <input type="password" class="form-control" id="newPassword" required minlength="6">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirmPassword" required minlength="6">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-admin btn-admin-primary">
                                    <i class="bi bi-key me-2"></i>Ubah Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <button class="btn btn-outline-danger btn-lg px-5" onclick="logoutAdmin()">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function loadProfile() {
    const res = await API.get('/auth/me');
    if (res.ok) {
        const user = res.body.data ?? res.body.user ?? {};
        document.getElementById('profileName').textContent = user.name || '-';
        document.getElementById('profileEmail').textContent = user.email || '-';
        document.getElementById('editName').value = user.name || '';
        document.getElementById('editEmail').value = user.email || '';
    } else {
        const localUser = Auth.getUser();
        if (localUser.name) {
            document.getElementById('profileName').textContent = localUser.name;
            document.getElementById('profileEmail').textContent = localUser.email || '-';
            document.getElementById('editName').value = localUser.name || '';
            document.getElementById('editEmail').value = localUser.email || '';
        }
    }
}

async function updateProfile() {
    const name = document.getElementById('editName').value.trim();
    const email = document.getElementById('editEmail').value.trim();
    if (!name || !email) {
        Helpers.showAlert('Nama dan email harus diisi.', 'warning');
        return;
    }
    const res = await API.post('/auth/update-profile', { name, email });
    if (res.ok) {
        Helpers.showAlert('Profil berhasil diperbarui!', 'success');
        const user = Auth.getUser();
        user.name = name;
        user.email = email;
        Auth.setUser(user);
        document.getElementById('profileName').textContent = name;
        document.getElementById('profileEmail').textContent = email;
        document.getElementById('sidebarProfileName').textContent = name;
    } else {
        Helpers.showAlert(res.body?.message || 'Gagal memperbarui profil.', 'danger');
    }
}

async function changePassword() {
    const oldPassword = document.getElementById('oldPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (!oldPassword || !newPassword || !confirmPassword) {
        Helpers.showAlert('Semua field password harus diisi.', 'warning');
        return;
    }
    if (newPassword !== confirmPassword) {
        Helpers.showAlert('Konfirmasi password tidak cocok.', 'danger');
        return;
    }
    if (newPassword.length < 6) {
        Helpers.showAlert('Password minimal 6 karakter.', 'warning');
        return;
    }
    const res = await API.post('/auth/change-password', {
        current_password: oldPassword,
        password: newPassword,
        password_confirmation: confirmPassword,
    });
    if (res.ok) {
        Helpers.showAlert('Password berhasil diubah!', 'success');
        document.getElementById('oldPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    } else {
        Helpers.showAlert(res.body?.message || 'Gagal mengubah password.', 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadProfile);
</script>
@endpush
