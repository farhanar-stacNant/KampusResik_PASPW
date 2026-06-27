document.addEventListener('DOMContentLoaded', function() {
    const burgerToggle = document.getElementById('burgerToggle');
    const petugasSidebar = document.getElementById('petugasSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.main-content');
    const burgerIcon = document.getElementById('burgerIcon');
    const burgerText = document.getElementById('burgerText');

    function toggleSidebar() {
        const isOpen = petugasSidebar.classList.contains('open');
        if (isOpen) {
            petugasSidebar.classList.remove('open'); sidebarOverlay.classList.remove('show');
            if (mainContent) mainContent.classList.remove('sidebar-open');
            burgerToggle.classList.remove('open');
            if (burgerIcon) burgerIcon.classList.remove('d-none');
            if (burgerText) burgerText.classList.add('d-none');
        } else {
            petugasSidebar.classList.add('open'); sidebarOverlay.classList.add('show');
            if (mainContent) mainContent.classList.add('sidebar-open');
            burgerToggle.classList.add('open');
            if (burgerIcon) burgerIcon.classList.add('d-none');
            if (burgerText) burgerText.classList.remove('d-none');
        }
    }

    if (burgerToggle) burgerToggle.addEventListener('click', function(e) { e.preventDefault(); toggleSidebar(); });
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
    
    document.querySelectorAll('.sidebar-item').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) { petugasSidebar.classList.remove('open'); sidebarOverlay.classList.remove('show'); if (mainContent) mainContent.classList.remove('sidebar-open'); burgerToggle.classList.remove('open'); if (burgerIcon) burgerIcon.classList.remove('d-none'); if (burgerText) burgerText.classList.add('d-none'); }
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) { petugasSidebar.classList.add('open'); sidebarOverlay.classList.remove('show'); if (mainContent) mainContent.classList.add('sidebar-open'); }
        else { petugasSidebar.classList.remove('open'); sidebarOverlay.classList.remove('show'); if (mainContent) mainContent.classList.remove('sidebar-open'); burgerToggle.classList.remove('open'); if (burgerIcon) burgerIcon.classList.remove('d-none'); if (burgerText) burgerText.classList.add('d-none'); }
    });
    window.dispatchEvent(new Event('resize'));

    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) btnLogout.addEventListener('click', function(e) { if (!confirm('Apakah Anda yakin ingin keluar?')) e.preventDefault(); });
});