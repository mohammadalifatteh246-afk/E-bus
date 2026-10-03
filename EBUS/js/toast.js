/**
 * Toast Notification System
 */

const ToastSystem = {
  container: null,

  init() {
    // Create container if it doesn't exist
    if (!document.getElementById('toast-container')) {
      this.container = document.createElement('div');
      this.container.id = 'toast-container';
      this.container.className = 'toast-container';
      document.body.appendChild(this.container);
    } else {
      this.container = document.getElementById('toast-container');
    }
  },

  /**
   * Show a toast message
   * @param {string} title - Title of the toast
   * @param {string} message - Content message
   * @param {string} type - 'success', 'error', 'warning', 'info'
   * @param {number} duration - Time in ms before auto close (default 4000)
   */
  show(title, message, type = 'success', duration = 4000) {
    if (!this.container) this.init();

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Build toast HTML
    toast.innerHTML = `
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close" aria-label="Close">&times;</button>
    `;

    this.container.appendChild(toast);

    // Trigger reflow to apply CSS transition
    toast.offsetHeight;
    
    // Show toast
    toast.classList.add('show');

    // Close button click
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
      this.close(toast);
    });

    // Auto close
    if (duration > 0) {
      setTimeout(() => {
        this.close(toast);
      }, duration);
    }
  },

  close(toast) {
    toast.classList.remove('show');
    
    // Wait for transition to complete before removing from DOM
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300); // matches --transition-normal (250ms + buffer)
  }
};

// Auto-initialize on load
document.addEventListener('DOMContentLoaded', () => {
  ToastSystem.init();
});
