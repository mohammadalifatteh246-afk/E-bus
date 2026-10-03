/**
 * E-BUS Global Application Script
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize generic components
  initTooltips();
  
  // Future global initializations can go here
});

/**
 * Basic tooltip functionality (placeholder for now, can be expanded)
 */
function initTooltips() {
  const elements = document.querySelectorAll('[data-tooltip]');
  elements.forEach(el => {
    el.addEventListener('mouseenter', (e) => {
      // Tooltip logic if needed
      el.title = el.getAttribute('data-tooltip');
    });
  });
}
