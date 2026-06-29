@extends('admin.layout')

@section('title', 'Kategori & Petugas')
@section('content')
<div class="container-fluid">
    <div class="admin-header animate-in">
        <div>
            <h4>Kategori & Petugas</h4>
            <p>Kelola kategori sampah dan data petugas kebersihan.</p>
        </div>
        <div>
            <button class="btn btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddCategory()">
                <i class="bi bi-plus-lg"></i>Tambah Kategori
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7 animate-in">
            <div class="admin-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-tags me-2"></i>Kategori Sampah</h5>
                    <div id="categoryList"></div>
                    <div id="loadingCategories" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat...
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 animate-in">
            <div class="admin-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-people me-2"></i>Data Petugas</h5>
                    <div id="petugasList"></div>
                    <div id="loadingPetugas" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="categoryModalTitle">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="categoryId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kategori</label>
                    <input type="text" class="form-control" id="categoryName" placeholder="Contoh: Sampah Organik">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control" id="categoryDesc" rows="3" placeholder="Deskripsi kategori"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tingkat Risiko</label>
                    <select class="form-select" id="categoryRisk">
                        <option value="rendah">Rendah</option>
                        <option value="sedang">Sedang</option>
                        <option value="tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Warna (opsional)</label>
                    <input type="color" class="form-control form-control-color" id="categoryColor" value="#2e7d32">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-admin btn-admin-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-admin btn-admin-primary" id="saveCategoryBtn" onclick="saveCategory()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let categories = [];
let editingCategoryId = null;

async function loadCategories() {
    const res = await API.get('/admin/categories');
    document.getElementById('loadingCategories').classList.add('d-none');
    if (res.ok) {
        categories = Array.isArray(res.body.data) ? res.body.data : [];
    } else {
        categories = [];
    }
    renderCategories();
}

function renderCategories() {
    const container = document.getElementById('categoryList');
    if (!categories.length) {
        container.innerHTML = `<div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            <p>Belum ada kategori.</p>
        </div>`;
        return;
    }
    container.innerHTML = categories.map(k => {
        const risk = Helpers.risikoBadge(k.tingkat_risiko || k.risk_level);
        const warna = k.warna || k.color || '#2e7d32';
        return `
            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-2 border" style="border-left:4px solid ${warna}">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="width:10px;height:10px;border-radius:50%;background:${warna};display:inline-block"></span>
                        <strong>${Helpers.escapeHtml(k.nama || k.name)}</strong>
                        <span class="badge ${risk.class}">${risk.label}</span>
                    </div>
                    <small class="text-muted">${Helpers.escapeHtml(k.deskripsi || k.description || '-')}</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning" onclick="openEditCategory(${k.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${k.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function openAddCategory() {
    editingCategoryId = null;
    document.getElementById('categoryModalTitle').textContent = 'Tambah Kategori';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryDesc').value = '';
    document.getElementById('categoryRisk').value = 'rendah';
    document.getElementById('categoryColor').value = '#2e7d32';
    document.getElementById('saveCategoryBtn').textContent = 'Simpan';
}

function openEditCategory(id) {
    const k = categories.find(c => c.id === id);
    if (!k) return;
    editingCategoryId = id;
    document.getElementById('categoryModalTitle').textContent = 'Edit Kategori';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = k.nama || k.name || '';
    document.getElementById('categoryDesc').value = k.deskripsi || k.description || '';
    document.getElementById('categoryRisk').value = k.tingkat_risiko || k.risk_level || 'rendah';
    document.getElementById('categoryColor').value = k.warna || k.color || '#2e7d32';
    document.getElementById('saveCategoryBtn').textContent = 'Update';
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal'));
    modal.show();
}

async function saveCategory() {
    const nama = document.getElementById('categoryName').value.trim();
    const deskripsi = document.getElementById('categoryDesc').value.trim();
    const tingkat_risiko = document.getElementById('categoryRisk').value;
    const warna = document.getElementById('categoryColor').value;
    if (!nama) {
        Helpers.showAlert('Nama kategori harus diisi.', 'warning');
        return;
    }
    const payload = { nama, deskripsi, tingkat_risiko, warna };
    let res;
    if (editingCategoryId) {
        res = await API.put(`/admin/categories/${editingCategoryId}`, payload);
    } else {
        res = await API.post('/admin/categories', payload);
    }
    if (res.ok) {
        Helpers.showAlert(editingCategoryId ? 'Kategori berhasil diperbarui!' : 'Kategori berhasil ditambahkan!', 'success');
        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        loadCategories();
    } else {
        Helpers.showAlert(res.body?.message || 'Gagal menyimpan kategori.', 'danger');
    }
}

async function deleteCategory(id) {
    if (!confirm('Yakin ingin menghapus kategori ini?')) return;
    const res = await API.delete(`/admin/categories/${id}`);
    if (res.ok) {
        Helpers.showAlert('Kategori berhasil dihapus!', 'success');
        loadCategories();
    } else {
        Helpers.showAlert('Gagal menghapus kategori.', 'danger');
    }
}

async function loadPetugas() {
    const res = await API.get('/admin/petugas');
    document.getElementById('loadingPetugas').classList.add('d-none');
    const container = document.getElementById('petugasList');
    if (res.ok) {
        const list = Array.isArray(res.body.data) ? res.body.data : [];
        if (!list.length) {
            container.innerHTML = `<div class="text-center text-muted py-4">
                <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                <p>Belum ada petugas.</p>
            </div>`;
            return;
        }
        container.innerHTML = list.map(p => {
            const initial = (p.nama || p.name || '?')[0].toUpperCase();
            const email = p.email || '-';
            const telepon = p.telepon || p.no_hp || '-';
            const lokasi = p.lokasi_sekitar || '-';
            return `
                <div class="p-3 rounded-3 mb-2 border">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--adm-primary-lighter);display:flex;align-items:center;justify-content:center;color:var(--adm-primary);font-weight:700;font-size:1.1rem">
                            ${initial}
                        </div>
                        <div>
                            <strong>${Helpers.escapeHtml(p.nama || p.name)}</strong>
                            <br><small class="text-muted">${Helpers.escapeHtml(email)}</small>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted d-flex gap-3 flex-wrap">
                        <span><i class="bi bi-telephone me-1"></i>${Helpers.escapeHtml(telepon)}</span>
                        <span><i class="bi bi-geo-alt me-1"></i>${Helpers.escapeHtml(lokasi)}</span>
                    </div>
                </div>
            `;
        }).join('');
    } else {
        container.innerHTML = `<div class="text-center text-muted py-4">
            <i class="bi bi-exclamation-circle fs-1 d-block mb-2 opacity-25"></i>
            <p>Gagal memuat data petugas.</p>
        </div>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadPetugas();
});
</script>
@endpush
