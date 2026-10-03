// CSS for Autocomplete Dropdown
const style = document.createElement('style');
style.innerHTML = `
  .autocomplete-wrapper { position: relative; width: 100%; }
  .autocomplete-suggestions {
    position: absolute; top: 100%; left: 0; right: 0;
    background: #fff; border: 1px solid var(--border, #ccc); border-top: none;
    z-index: 1000; max-height: 250px; overflow-y: auto;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 4px 4px; display: none;
  }
  .autocomplete-suggestion {
    padding: 10px; cursor: pointer; border-bottom: 1px solid var(--border, #eee);
    font-size: 14px; line-height: 1.4; color: #333;
  }
  .autocomplete-suggestion:last-child { border-bottom: none; }
  .autocomplete-suggestion:hover { background-color: var(--light, #f8f9fa); color: var(--primary, #0056b3); }
`;
document.head.appendChild(style);

function initGeocoder() {
  const fromInput = document.querySelector('input[name="from"]');
  const toInput = document.querySelector('input[name="to"]');
  const distInput = document.querySelector('input[name="distance_km"]');

  if (!fromInput || !toInput || !distInput) return;

  // Wrap inputs to hold absolute positioned suggestions
  const wrapInput = (input, id) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'autocomplete-wrapper';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    const suggs = document.createElement('div');
    suggs.className = 'autocomplete-suggestions';
    suggs.id = id;
    wrapper.appendChild(suggs);
    return suggs;
  };

  const fromSuggs = wrapInput(fromInput, 'fromSuggestions');
  const toSuggs = wrapInput(toInput, 'toSuggestions');

  let debounceTimerFrom;
  let debounceTimerTo;

  const calculateDistance = async () => {
    const lat1 = fromInput.dataset.lat;
    const lon1 = fromInput.dataset.lon;
    const lat2 = toInput.dataset.lat;
    const lon2 = toInput.dataset.lon;

    if (lat1 && lon1 && lat2 && lon2) {
      distInput.placeholder = "Calculating...";
      distInput.value = '';
      try {
        const res = await fetch(`https://router.project-osrm.org/route/v1/driving/${lon1},${lat1};${lon2},${lat2}?overview=false`);
        const data = await res.json();
        
        if (data.routes && data.routes.length > 0) {
          const distanceKm = data.routes[0].distance / 1000;
          distInput.value = distanceKm.toFixed(1);
          distInput.style.backgroundColor = '#e8f5e9';
          setTimeout(() => distInput.style.backgroundColor = '', 1500);
        } else {
            distInput.placeholder = "Route not found.";
        }
      } catch(e) {
        console.error('OSRM error:', e);
        distInput.placeholder = "Failed to calculate.";
      }
    }
  };

  const setupAutocomplete = (inputEle, suggsBox, timerRefObj) => {
    inputEle.addEventListener('input', (e) => {
      const query = e.target.value;
      
      // Clear data on typing so it doesn't hold old coordinates
      inputEle.dataset.lat = '';
      inputEle.dataset.lon = '';

      if (query.length < 3) {
        suggsBox.style.display = 'none';
        return;
      }

      clearTimeout(timerRefObj.timer);
      timerRefObj.timer = setTimeout(async () => {
        try {
          const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=in&limit=5`);
          const data = await res.json();
          
          suggsBox.innerHTML = '';
          if (data && data.length > 0) {
            suggsBox.style.display = 'block';
            data.forEach(item => {
              const div = document.createElement('div');
              div.className = 'autocomplete-suggestion';
              div.innerText = item.display_name;
              div.addEventListener('click', () => {
                inputEle.value = item.display_name.split(',')[0].trim();
                inputEle.dataset.lat = item.lat;
                inputEle.dataset.lon = item.lon;
                suggsBox.style.display = 'none';
                calculateDistance();
              });
              suggsBox.appendChild(div);
            });
          } else {
            suggsBox.style.display = 'none';
          }
        } catch(err) {
          console.error('Geocoder error:', err);
        }
      }, 500);
    });
  };

  const timerFrom = { timer: null };
  const timerTo = { timer: null };

  setupAutocomplete(fromInput, fromSuggs, timerFrom);
  setupAutocomplete(toInput, toSuggs, timerTo);

  // Hide dropdowns when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.autocomplete-wrapper')) {
      fromSuggs.style.display = 'none';
      toSuggs.style.display = 'none';
    }
  });
}

document.addEventListener('DOMContentLoaded', initGeocoder);
