/**
 * Sidebar and Navigation Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const navItems = document.querySelectorAll('.nav-item');

  // Toggle Sidebar on mobile
  if (sidebarToggle && sidebar && sidebarOverlay) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.add('open');
      sidebarOverlay.classList.add('open');
      document.body.style.overflow = 'hidden'; // Prevent scrolling
    });

    sidebarOverlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('open');
      document.body.style.overflow = '';
    });
  }

  // Active navigation highlighting based on current URL
  const currentPath = window.location.pathname;
  navItems.forEach(item => {
    const link = item.getAttribute('href');
    if (link && currentPath.includes(link) && link !== '#') {
      // Remove active from all
      navItems.forEach(nav => nav.classList.remove('active'));
      // Add active to current
      item.classList.add('active');
    }
    
    // Add click event for smooth transitions / manual active setting if needed
    item.addEventListener('click', function(e) {
      if(this.getAttribute('href') === '#') {
        e.preventDefault();
        navItems.forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
      }
    });
  });
});
