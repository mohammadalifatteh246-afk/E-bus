/**
 * Seat Map Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  const seatMap = document.getElementById('seatMap');
  const selectedSeatsDisplay = document.getElementById('selectedSeatsList');
  const totalFareDisplay = document.getElementById('totalFare');
  const seatCountDisplay = document.getElementById('seatCount');
  
  if (!seatMap) return;

  const baseFare = parseInt(seatMap.dataset.fare || '0');
  let selectedSeats = [];

  // Generate seats (dummy data for visual)
  const rows = 10;
  const cols = 4; // 2x2 layout
  
  let html = '';
  for (let r = 1; r <= rows; r++) {
    html += `<div class="seat-row flex justify-center gap-md mb-sm">`;
    for (let c = 1; c <= cols; c++) {
      const seatId = `${r}${String.fromCharCode(64 + c)}`; // 1A, 1B...
      // Randomly book some seats for demo
      const isBooked = Math.random() < 0.3;
      const statusClass = isBooked ? 'booked' : 'available';
      
      html += `
        <div class="seat ${statusClass}" data-seat="${seatId}">
          ${seatId}
        </div>
      `;
      
      if (c === 2) {
        html += `<div class="seat-aisle" style="width: 20px;"></div>`; // Aisle
      }
    }
    html += `</div>`;
  }
  seatMap.innerHTML = html;

  // Seat Click Logic
  seatMap.addEventListener('click', (e) => {
    const seatEl = e.target.closest('.seat.available, .seat.selected');
    if (!seatEl) return;

    const seatId = seatEl.dataset.seat;

    if (seatEl.classList.contains('available')) {
      // Select
      if (selectedSeats.length >= 6) {
        if (typeof ToastSystem !== 'undefined') {
          ToastSystem.show('Limit Reached', 'You can only select up to 6 seats.', 'warning');
        }
        return;
      }
      seatEl.classList.remove('available');
      seatEl.classList.add('selected');
      selectedSeats.push(seatId);
    } else {
      // Deselect
      seatEl.classList.remove('selected');
      seatEl.classList.add('available');
      selectedSeats = selectedSeats.filter(id => id !== seatId);
    }

    updateFareSummary();
  });

  function updateFareSummary() {
    if (selectedSeatsDisplay) {
      if (selectedSeats.length === 0) {
        selectedSeatsDisplay.innerHTML = '<span class="text-muted">None selected</span>';
      } else {
        selectedSeatsDisplay.innerHTML = selectedSeats.map(s => `<span class="badge badge-info">${s}</span>`).join(' ');
      }
    }

    if (seatCountDisplay) {
      seatCountDisplay.textContent = selectedSeats.length;
    }

    if (totalFareDisplay) {
      const subtotal = selectedSeats.length * baseFare;
      const fee = selectedSeats.length > 0 ? 20 : 0;
      totalFareDisplay.textContent = `₹${subtotal + fee}`;
      
      // Also update subtotal and fee in the breakdown if they exist
      const subtotalEl = document.getElementById('fareSubtotal');
      const feeEl = document.getElementById('fareFee');
      if (subtotalEl) subtotalEl.textContent = `₹${subtotal}`;
      if (feeEl) feeEl.textContent = `₹${fee}`;
    }
    
    // Enable/disable continue button
    const continueBtn = document.getElementById('continueToPassengerBtn');
    if (continueBtn) {
      continueBtn.disabled = selectedSeats.length === 0;
    }
  }
});
