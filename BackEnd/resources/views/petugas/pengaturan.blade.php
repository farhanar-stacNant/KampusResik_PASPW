@extends('petugas.layout')

@section('title', 'Pengaturan - Petugas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 animate-fade-in">
        <div class="col-12">
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-gear me-2"></i>Pengaturan</h4>
            <p class="text-muted mb-0">Kelola profil dan preferensi akun</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4 animate-fade-in">
            <div class="card card-stat h-100">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width:120px;height:120px;border:3px solid #2e7d32;">
                            <i class="bi bi-person" style="font-size:3.5rem;color:#2e7d32;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1" id="profileName">Petugas</h5>
                    <p class="text-muted mb-2" id="profileRole">Petugas Kebersihan</p>
                    <span class="badge bg-success bg-opacity-10 text-success">Petugas Aktif</span>
                    <hr class="my-3">
                    <div class="text-start">
                        <p class="mb-2"><i class="bi bi-envelope text-muted me-2"></i><small class="text-muted">Email</small><br><span class="fw-semibold" id="profileEmail">-</span></p>
                        <p class="mb-2"><i class="bi bi-telephone text-muted me-2"></i><small class="text-muted">Telepon</small><br><span class="fw-semibold" id="profileTelepon">-</span></p>
                        <p class="mb-0"><i class="bi bi-calendar text-muted me-2"></i><small class="text-muted">Bergabung</small><br><span class="fw-semibold" id="profileBergabung">-</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4 animate-fade-in">
            <div class="card card-stat">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-success"></i>Edit Profil</h5>
                    <form id="formProfil">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-person"></i></span><input type="text" name="nama" class="form-control" id="inputNama"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span><input type="email" name="email" class="form-control" id="inputEmail"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor Telepon</label>
                                <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span><input type="tel" name="telepon" class="form-control" id="inputTelepon"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lokasi Tugas</label>
                                <div class="input-group"><span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span><input type="text" name="lokasi_sekitar" class="form-control" id="inputLokasiTugas"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-petugas btn-petugas-primary"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row animate-fade-in">
        <div class="col-12">
            <div class="card card-stat border-danger border-opacity-25">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-shield-lock me-2"></i>Zona Berbahaya</h5>
                    <p class="text-muted mb-3">Keluar dari akun akan menghapus semua session dan cache.</p>
                    <button class="btn btn-logout-card" id="btnLogoutDanger"><i class="bi bi-box-arrow-right me-2"></i>Keluar dari Akun</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const user = Auth.getUser();
    let profile = {
        nama: user.name || user.nama || 'Petugas',
        email: user.email || '-',
        telepon: user.no_hp || user.telepon || '-',
        lokasi_tugas: user.lokasi_sekitar || user.lokasi_tugas || 'Belum ditentukan',
        created_at: user.created_at || ''
    };

    const res = await API.get('/me');
    if (res.ok && res.data?.data) {
        const apiUser = res.data.data;
        profile.nama = apiUser.nama || apiUser.name || profile.nama;
        profile.email = apiUser.email || profile.email;
        profile.telepon = apiUser.no_hp || apiUser.telepon || profile.telepon;
        profile.lokasi_tugas = apiUser.lokasi_sekitar || apiUser.lokasi_tugas || profile.lokasi_tugas;
        profile.created_at = apiUser.created_at || profile.created_at;
    }

    document.getElementById('profileName').textContent = profile.nama;
    document.getElementById('profileEmail').textContent = profile.email;
    document.getElementById('profileTelepon').textContent = profile.telepon;
    document.getElementById('inputNama').value = profile.nama;
    document.getElementById('inputEmail').value = profile.email;
    document.getElementById('inputTelepon').value = profile.telepon;
    document.getElementById('inputLokasiTugas').value = profile.lokasi_tugas;

    const elBergabung = document.getElementById('profileBergabung');
    if (profile.created_at) {
        try {
            elBergabung.textContent = Helpers.formatTanggal(profile.created_at);
        } catch (e) {
            elBergabung.textContent = profile.created_at;
        }
    } else {
        const user = Auth.getUser();
        if (user.created_at) {
            elBergabung.textContent = user.created_at;
        }
    }

    document.getElementById('formProfil').addEventListener('submit', async function (e) {
        e.preventDefault();
        const data = {
            nama: this.nama.value,
            email: this.email.value,
            no_hp: this.telepon.value,
            lokasi_sekitar: this.lokasi_sekitar.value
        };
        const res = await API.post('/auth/update-profile', data);
        if (res.ok) {
            Helpers.showAlert('Profil berhasil diperbarui', 'success');
            const updatedUser = {
                ...Auth.getUser(),
                ...data,
                nama: data.nama,
                name: data.nama,
                telepon: data.no_hp,
                no_hp: data.no_hp,
                lokasi_sekitar: data.lokasi_sekitar,
                lokasi_tugas: data.lokasi_sekitar
            };
            Auth.setUser(updatedUser);
            document.getElementById('profileName').textContent = data.nama;
            document.getElementById('profileEmail').textContent = data.email;
            document.getElementById('profileTelepon').textContent = data.no_hp;
        } else {
            const msg = res.data?.message || 'Gagal memperbarui profil';
            Helpers.showAlert(msg, 'danger');
        }
    });

    document.getElementById('btnLogoutDanger')?.addEventListener('click', async function () {
        if (!confirm('Apakah Anda yakin ingin keluar?')) return;
        await API.post('/auth/logout');
        Auth.logout();
    });
});
</script>
@endpush
