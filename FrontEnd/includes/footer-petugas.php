<?php
// Footer Petugas
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const burgerToggle = document.getElementById('burgerToggle');
const sidebar = document.getElementById('petugasSidebar');
const subnavIcon = document.getElementById('subnavIcon');
const burgerIcon = document.getElementById('burgerIcon');
const burgerText = document.getElementById('burgerText');

function openSidebar() {
    sidebar.classList.add('open');
    subnavIcon.classList.add('hidden');
    burgerIcon.classList.replace('bi-list', 'bi-x-lg');
    burgerText.textContent = 'Tutup';
}

function closeSidebar() {
    sidebar.classList.remove('open');
    subnavIcon.classList.remove('hidden');
    burgerIcon.classList.replace('bi-x-lg', 'bi-list');
    burgerText.textContent = 'Menu';
}

function toggleSidebar() {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
}

burgerToggle.addEventListener('click', toggleSidebar);

// Swipe burger ke kiri = buka
let touchStartX = 0;
burgerToggle.addEventListener('touchstart', e => touchStartX = e.touches[0].clientX);
burgerToggle.addEventListener('touchmove', e => {
    const diff = touchStartX - e.touches[0].clientX;
    if (diff > 30 && !sidebar.classList.contains('open')) openSidebar();
    if (diff < -30 && sidebar.classList.contains('open')) closeSidebar();
});

// Swipe sidebar ke kanan = tutup
sidebar.addEventListener('touchstart', e => touchStartX = e.touches[0].clientX);
sidebar.addEventListener('touchmove', e => {
    const diff = e.touches[0].clientX - touchStartX;
    if (diff > 50 && sidebar.classList.contains('open')) closeSidebar();
});

// Auto close link click
document.querySelectorAll('.sidebar-item').forEach(link => {
    link.addEventListener('click', closeSidebar);
});

// Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
});
</script>
<script src="<?= assets_url() ?>/js/petugas-main.js"></script>
</body>
</html>