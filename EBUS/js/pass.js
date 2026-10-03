/**
 * Bus Pass Application Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  const passTypeInputs = document.querySelectorAll('input[name="passType"]');
  const distanceInput = document.getElementById('passDistance');
  const durationSelect = document.getElementById('passDuration');
  
  const baseFareDisplay = document.getElementById('passBaseFare');
  const feeDisplay = document.getElementById('passFee');
  const subtotalDisplay = document.getElementById('passSubtotal');
  const discountRow = document.getElementById('passDiscountRow');
  const discountDisplay = document.getElementById('passDiscount');
  const totalDisplay = document.getElementById('passTotal');
  
  const ratePerKm = 1.5; // Example rate
  const processingFee = 20;
  
  function calculatePassFare() {
    if (!distanceInput || !totalDisplay) return;
    
    const distance = parseFloat(distanceInput.value) || 0;
    const durationMonths = parseInt(durationSelect.value) || 1;
    
    // Determine pass type
    let passType = 'regular';
    passTypeInputs.forEach(input => {
      if (input.checked) passType = input.value;
    });

    // Base Calculation
    // formula: distance * rate * 30 days * months
    const baseFare = Math.round(distance * ratePerKm * 30 * durationMonths);
    const subtotal = baseFare + processingFee;
    
    let discount = 0;
    
    if (passType === 'student') {
      discount = Math.round(subtotal * 0.50);
      if (discountRow) discountRow.style.display = 'flex';
      if (discountDisplay) discountDisplay.textContent = `- ₹${discount}`;
    } else {
      if (discountRow) discountRow.style.display = 'none';
    }
    
    const total = subtotal - discount;

    // Update UI
    if (baseFareDisplay) baseFareDisplay.textContent = `₹${baseFare}`;
    if (feeDisplay) feeDisplay.textContent = `₹${processingFee}`;
    if (subtotalDisplay) subtotalDisplay.textContent = `₹${subtotal}`;
    if (totalDisplay) totalDisplay.textContent = `₹${total}`;
  }

  // Attach event listeners
  if (passTypeInputs.length > 0) {
    passTypeInputs.forEach(input => {
      input.addEventListener('change', calculatePassFare);
    });
  }
  
  if (distanceInput) {
    distanceInput.addEventListener('input', calculatePassFare);
  }
  
  if (durationSelect) {
    durationSelect.addEventListener('change', calculatePassFare);
  }

  // Initial calculation
  calculatePassFare();
  
  // File upload preview logic
  const photoUpload = document.getElementById('photoUpload');
  const photoPreview = document.getElementById('photoPreview');
  
  if (photoUpload && photoPreview) {
    photoUpload.addEventListener('change', function(e) {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          photoPreview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-sm);">`;
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
  }
});
