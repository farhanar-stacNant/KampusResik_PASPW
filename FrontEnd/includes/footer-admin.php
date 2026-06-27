<?php
// Footer Admin
?>

</div><!-- /admin-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mobile sidebar toggle
const sidebarToggle = document.getElementById('sidebarToggle');
const adminSidebar = document.getElementById('adminSidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        adminSidebar.classList.toggle('mobile-open');
        sidebarOverlay.classList.toggle('show');
    });
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
        adminSidebar.classList.remove('mobile-open');
        sidebarOverlay.classList.remove('show');
    });
}
</script>
<script src="<?= assets_url() ?>/js/admin-main.js"></script>
</body>
</html>