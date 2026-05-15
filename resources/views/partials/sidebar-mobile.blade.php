<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Mobile Toggle Button -->
<button onclick="toggleSidebar()" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-slate-900 text-white rounded-lg shadow-lg">
    @include('components.icons.menu', ['class' => 'w-6 h-6'])
</button>

<style>
/* Collapsed sidebar state - width shrinks to icon-only */
.sidebar-collapsed {
    width: 4rem !important; /* w-16 */
}

/* When collapsed, hide text labels */
.sidebar-collapsed .sidebar-text {
    opacity: 0;
    width: 0;
    overflow: hidden;
    display: none;
}

/* When collapsed, center items */
.sidebar-collapsed .sidebar-collapsed\:justify-center {
    justify-content: center;
}

.sidebar-collapsed .sidebar-collapsed\:px-2 {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.sidebar-collapsed .sidebar-collapsed\:p-2 {
    padding: 0.5rem;
}

/* Hide scrollbar when collapsed */
.sidebar-collapsed .sidebar-collapsed\:overflow-hidden {
    overflow: hidden;
}

/* Main content adjusts */
.main-collapsed {
    margin-left: 4rem !important; /* lg:ml-16 */
}

/* Show tooltips only when collapsed */
.sidebar-collapsed .sidebar-collapsed\:block {
    display: block;
}
</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

function toggleDesktopSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const iconExpanded = document.getElementById('icon-expanded');
    const iconCollapsed = document.getElementById('icon-collapsed');
    const isCollapsed = sidebar.getAttribute('data-collapsed') === 'true';
    
    if (isCollapsed) {
        // Expand sidebar
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.add('w-64');
        sidebar.classList.remove('w-16');
        sidebar.setAttribute('data-collapsed', 'false');
        
        mainContent.classList.remove('main-collapsed');
        mainContent.classList.add('lg:ml-64');
        
        // Toggle icons
        if (iconExpanded && iconCollapsed) {
            iconExpanded.classList.remove('hidden');
            iconCollapsed.classList.add('hidden');
        }
    } else {
        // Collapse sidebar to icon-only
        sidebar.classList.add('sidebar-collapsed');
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-16');
        sidebar.setAttribute('data-collapsed', 'true');
        
        mainContent.classList.add('main-collapsed');
        mainContent.classList.remove('lg:ml-64');
        
        // Toggle icons
        if (iconExpanded && iconCollapsed) {
            iconExpanded.classList.add('hidden');
            iconCollapsed.classList.remove('hidden');
        }
    }
}

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const iconExpanded = document.getElementById('icon-expanded');
    const iconCollapsed = document.getElementById('icon-collapsed');
    
    if (sidebar && sidebar.getAttribute('data-collapsed') === 'true') {
        sidebar.classList.add('sidebar-collapsed', 'w-16');
        sidebar.classList.remove('w-64');
        document.getElementById('main-content').classList.add('main-collapsed');
        
        // Set icon state
        if (iconExpanded && iconCollapsed) {
            iconExpanded.classList.add('hidden');
            iconCollapsed.classList.remove('hidden');
        }
    }
});
</script>
