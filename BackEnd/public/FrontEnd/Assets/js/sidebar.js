// ============================================
// SIDEBAR PETUGAS — TOGGLE & INTERACTION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const floatBtn = document.getElementById('sidebarFloatBtn');
    
    // Cek cookie untuk state sidebar
    const isExpanded = getCookie('sidebar_expanded') === 'true';
    
    // Apply state
    if (window.innerWidth > 768) {
        if (isExpanded) {
            sidebar.classList.add('expanded');
            sidebar.classList.remove('compact');
        } else {
            sidebar.classList.add('compact');
            sidebar.classList.remove('expanded');
        }
    }

    // Load user info
    loadUserInfo();
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        // Mobile: slide in/out
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    } else {
        // Desktop: compact/expanded
        sidebar.classList.toggle('expanded');
        sidebar.classList.toggle('compact');
        
        const isExpanded = sidebar.classList.contains('expanded');
        setCookie('sidebar_expanded', isExpanded, 30);
    }
}

// Tutup sidebar saat klik menu di mobile
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            toggleSidebar();
        }
    });
});

// ============================================
// COOKIE HELPERS
// ============================================
function setCookie(name, value, days) {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/';
}

function getCookie(name) {
    return document.cookie.split('; ').reduce((r, v) => {
        const parts = v.split('=');
        return parts[0] === name ? decodeURIComponent(parts[1]) : r;
    }, '');
}

// ============================================
// LOAD USER INFO
// ============================================
function loadUserInfo() {
    const userData = sessionStorage.getItem('user');
    if (userData) {
        const user = JSON.parse(userData);
        const nameEl = document.getElementById('userName');
        if (nameEl) nameEl.textContent = user.nama || user.name || 'Petugas';
    }
}