        </div><!-- /.admin-main -->
    </div><!-- /.admin-content -->
</div><!-- /.admin-panel -->

<script>
// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('adminSidebar');
    const content = document.querySelector('.admin-content');
    const toggleBtn = document.querySelector('.toggle-sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
    }

    if (content && sidebar) {
        // Close the drawer when tapping the main content (but not the toggle/topbar)
        content.addEventListener('click', function(e) {
            if (e.target.closest('.toggle-sidebar')) return;
            if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
});

// Confirm delete actions
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Auto-generate slug from title
function generateSlug(text) {
    return text.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}
</script>
</body>
</html>
