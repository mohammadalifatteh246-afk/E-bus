/**
 * Modal System
 */

const ModalSystem = {
  init() {
    // Attach click listeners to all elements that open a modal
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
    modalTriggers.forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = trigger.getAttribute('data-target');
        this.open(targetId);
      });
    });

    // Attach click listeners to all elements that close a modal
    const modalClosers = document.querySelectorAll('[data-dismiss="modal"]');
    modalClosers.forEach(closer => {
      closer.addEventListener('click', (e) => {
        e.preventDefault();
        // Find closest modal overlay
        const modalOverlay = closer.closest('.modal-overlay');
        if (modalOverlay) {
          this.close(modalOverlay.id);
        }
      });
    });

    // Close on click outside modal content
    const modalOverlays = document.querySelectorAll('.modal-overlay');
    modalOverlays.forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          this.close(overlay.id);
        }
      });
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (activeModal) {
          this.close(activeModal.id);
        }
      }
    });
  },

  open(modalId) {
    if (!modalId) return;
    
    // Remove # if passed
    const id = modalId.startsWith('#') ? modalId.substring(1) : modalId;
    const modal = document.getElementById(id);
    
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  },

  close(modalId) {
    if (!modalId) return;
    
    const id = modalId.startsWith('#') ? modalId.substring(1) : modalId;
    const modal = document.getElementById(id);
    
    if (modal) {
      modal.classList.remove('active');
      
      // Check if other modals are open before restoring scroll
      if (!document.querySelector('.modal-overlay.active')) {
        document.body.style.overflow = '';
      }
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  ModalSystem.init();
});
