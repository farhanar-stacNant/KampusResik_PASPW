const Helpers = {
    formatTanggal(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    },

    statusBadge(status) {
        const map = {
            'baru': { label: 'Baru', class: 'bg-secondary' },
            'dikirim': { label: 'Dikirim', class: 'bg-secondary' },
            'diterima': { label: 'Diterima', class: 'bg-info' },
            'diproses': { label: 'Diproses', class: 'bg-warning text-dark' },
            'selesai': { label: 'Selesai', class: 'bg-success' },
            'ditolak': { label: 'Ditolak', class: 'bg-danger' },
        };
        return map[status] || { label: status || 'Unknown', class: 'bg-secondary' };
    },

    risikoBadge(level) {
        const map = {
            'rendah': { label: 'Rendah', class: 'bg-success' },
            'sedang': { label: 'Sedang', class: 'bg-warning text-dark' },
            'tinggi': { label: 'Tinggi', class: 'bg-danger' },
        };
        return map[level] || { label: level || 'Unknown', class: 'bg-secondary' };
    },

    escapeHtml(text) {
        if (!text) return '-';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    showAlert(message, type = 'danger') {
        const container = document.getElementById('alertContainer') || document.body;
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        container.prepend(alert);
        setTimeout(() => alert.remove(), 5000);
    }
};
