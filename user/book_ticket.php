<?php
// Start session
if (session_status() == PHP_SESSION_NONE) session_start();
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/api_config.php');

// Access control
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user info from DB
$email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Check role
if ($user['role'] === 'admin') {
    die("Access Denied. Admins cannot book tickets.");
}

// Now you can safely use
$user_id    = $user['id'];
$user_email = $user['email'];
$user_name  = $user['name'];

// Fetch bus types with their extra charges
$bus_types_result = $conn->query("SELECT id, bus_type_name, extra_charge FROM bus_types ORDER BY bus_type_name ASC");

// Variables
$success = $error = '';
$ticket = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from        = trim($_POST['from'] ?? '');
    $to          = trim($_POST['to'] ?? '');
    $seat_number = trim($_POST['seat_number'] ?? '');
    $bus_type_id = $_POST['bus_type_id'] ?? '';
    $distance_km = floatval($_POST['distance_km'] ?? 0);

    // Fetch extra charge for selected bus type to ensure security
    $extra_charge = 0;
    if ($bus_type_id) {
        $bc_stmt = $conn->prepare("SELECT extra_charge FROM bus_types WHERE id = ?");
        $bc_stmt->bind_param("i", $bus_type_id);
        $bc_stmt->execute();
        $bc_stmt->bind_result($extra_charge);
        $bc_stmt->fetch();
        $bc_stmt->close();
    }

    // Dynamic ticket price based on distance and bus type
    $ticket_price = ($distance_km * RATE_PER_KM) + PROCESSING_FEE + $extra_charge;

    if (!$from || !$to || !$seat_number || !$bus_type_id) {
        $error = "Please fill all fields.";
    } elseif ($distance_km <= 0) {
        $error = "Please select valid locations and calculate the route first.";
    } else {
        $stmt2 = $conn->prepare("
            INSERT INTO bookings 
            (user_id, from_location, to_location, distance_km, seat_number, bus_type_id, booking_date, status, amount)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Pending', ?)
        ");
        $stmt2->bind_param("issdsid", $user_id, $from, $to, $distance_km, $seat_number, $bus_type_id, $ticket_price);

        if ($stmt2->execute()) {
            $booking_id = $stmt2->insert_id;

            // Redirect to ticket page with booking_id
            header("Location: ticket.php?booking_id=" . $booking_id);
            exit();
        } else {
            $error = " Failed to book ticket. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book & Pay Ticket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<script src="../assets/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet.js CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
body {
    background: url("/ebus/images/17.png") no-repeat center center fixed;
    background-size: cover;
    font-family: "Segoe UI", Arial, sans-serif;
}
.card {
    max-width: 700px;
    margin: 60px auto;
    padding: 2rem;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    background: rgba(255,255,255,0.92);
    position: relative;
    animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #0d6efd;
    font-weight: bold;
}
.back-btn {
    position: absolute;
    top: 15px;
    left: 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    padding: 5px 14px;
}
.form-label { font-weight: 500; }
.form-control, .form-select { border-radius: 12px; padding: 10px; }
.btn-primary { border-radius: 30px; font-weight: 500; padding: 10px; }

/* Map container */
#map {
    height: 300px;
    border-radius: 15px;
    border: 2px solid #ddd;
    margin-top: 10px;
    z-index: 1;
}

/* Autocomplete dropdown */
.autocomplete-list {
    position: absolute;
    z-index: 1000;
    background: #fff;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 10px 10px;
    max-height: 200px;
    overflow-y: auto;
    width: calc(100% - 24px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.autocomplete-item {
    padding: 10px 15px;
    cursor: pointer;
    font-size: 14px;
    border-bottom: 1px solid #f0f0f0;
}
.autocomplete-item:hover { background-color: #e9f5ff; }

/* Route info */
.route-info {
    background: #e8f5e9;
    padding: 12px 18px;
    border-radius: 10px;
    margin-top: 10px;
    display: none;
}
.route-info span { font-weight: 600; color: #2e7d32; }

/* Fare preview */
.fare-preview {
    background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
    color: white;
    padding: 18px 22px;
    border-radius: 15px;
    margin-top: 10px;
    display: none;
}
.fare-preview h5 { margin-bottom: 12px; font-weight: 600; }
.fare-preview .fare-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 15px;
}
.fare-preview .fare-total {
    border-top: 1px solid rgba(255,255,255,0.4);
    padding-top: 10px;
    margin-top: 10px;
    font-size: 18px;
    font-weight: bold;
}
</style>
</head>
<body>
<div class="container">
    <div class="card">
        
        <!-- 🔙 Back Button -->
        <a href="user_dashboard.php" class="btn btn-outline-secondary back-btn">⬅ Back</a>

        <h2>Book & Pay Ticket</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Booking form -->
        <form method="POST" id="ticketForm">
            <div class="mb-3" style="position:relative;">
                <label for="from" class="form-label">📍 From</label>
                <input type="text" name="from" id="from" class="form-control" placeholder="Type departure city..." autocomplete="off" required>
                <div id="from_suggestions" class="autocomplete-list"></div>
            </div>
            <div class="mb-3" style="position:relative;">
                <label for="to" class="form-label">📍 To</label>
                <input type="text" name="to" id="to" class="form-control" placeholder="Type destination city..." autocomplete="off" required>
                <div id="to_suggestions" class="autocomplete-list"></div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" id="from_lat" name="from_lat">
            <input type="hidden" id="from_lng" name="from_lng">
            <input type="hidden" id="to_lat" name="to_lat">
            <input type="hidden" id="to_lng" name="to_lng">
            <input type="hidden" id="distance_km" name="distance_km">

            <!-- Map (Hidden per user request) -->
            <div class="mb-3" style="display: none;">
                <label class="form-label">🗺️ Route Map</label>
                <div id="map"></div>
            </div>

            <!-- Route Info -->
            <div id="routeInfo" class="route-info mb-3">
                🚌 Distance: <span id="routeDistance">--</span> km &nbsp;|&nbsp; 
                ⏱ Time: <span id="routeDuration">--</span> min
            </div>

            <div class="mb-3">
                <label for="seat_number" class="form-label">Seat Number</label>
                <input type="text" name="seat_number" id="seat_number" class="form-control" placeholder="Seat number" required>
            </div>
            <div class="mb-3">
                <label for="bus_type_id" class="form-label">Bus Type</label>
                <select name="bus_type_id" id="bus_type_id" class="form-select" required onchange="updateFarePreview()">
                    <option value="" data-charge="0">-- Select Bus Type --</option>
                    <?php while ($bt = $bus_types_result->fetch_assoc()): ?>
                        <option value="<?= $bt['id'] ?>" data-charge="<?= $bt['extra_charge'] ?>"><?= htmlspecialchars($bt['bus_type_name']) ?> (+₹<?= $bt['extra_charge'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Fare Preview -->
            <div id="farePreview" class="fare-preview mb-3">
                <h5>🎫 Ticket Fare Estimate</h5>
                <div class="fare-row">
                    <span>Distance:</span>
                    <span id="fareDistance">-- km</span>
                </div>
                <div class="fare-row">
                    <span>Rate (₹<?= RATE_PER_KM ?>/km):</span>
                    <span id="fareBase">₹--</span>
                </div>
                <div class="fare-row">
                    <span>Processing Fee:</span>
                    <span>₹<?= PROCESSING_FEE ?></span>
                </div>
                <div class="fare-row" id="busChargeRow" style="display:none;">
                    <span>Bus Type Charge:</span>
                    <span id="fareBusType">₹--</span>
                </div>
                <div class="fare-row fare-total">
                    <span>Total:</span>
                    <span id="fareTotal">₹--</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">🚍 Book & Pay</button>
        </form>
    </div>
</div>

<!-- Leaflet.js JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ============================================
// MAP INITIALIZATION
// ============================================
const map = L.map('map').setView([22.3, 71.8], 7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors',
  maxZoom: 18
}).addTo(map);

let fromMarker = null;
let toMarker = null;
let routeLine = null;
let currentDistanceKm = 0;

const greenIcon = L.icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
  iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
});
const redIcon = L.icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
  iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
});

// ============================================
// NOMINATIM AUTOCOMPLETE
// ============================================
let debounceTimer = null;

function setupAutocomplete(inputId, suggestionsId, type) {
  const input = document.getElementById(inputId);
  const sugBox = document.getElementById(suggestionsId);

  input.addEventListener('input', function() {
    const query = this.value.trim();
    clearTimeout(debounceTimer);
    
    if (query.length < 3) { sugBox.innerHTML = ''; return; }

    debounceTimer = setTimeout(() => {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=in&limit=5&addressdetails=1`, {
        headers: { 'User-Agent': 'EBusPassSystem/1.0' }
      })
      .then(res => res.json())
      .then(data => {
        sugBox.innerHTML = '';
        data.forEach(item => {
          const div = document.createElement('div');
          div.className = 'autocomplete-item';
          div.textContent = item.display_name;
          div.addEventListener('click', () => {
            input.value = item.display_name.split(',')[0];
            sugBox.innerHTML = '';
            
            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lon);
            
            if (type === 'from') {
              document.getElementById('from_lat').value = lat;
              document.getElementById('from_lng').value = lng;
              if (fromMarker) map.removeLayer(fromMarker);
              fromMarker = L.marker([lat, lng], {icon: greenIcon}).addTo(map).bindPopup('📍 From: ' + input.value).openPopup();
            } else {
              document.getElementById('to_lat').value = lat;
              document.getElementById('to_lng').value = lng;
              if (toMarker) map.removeLayer(toMarker);
              toMarker = L.marker([lat, lng], {icon: redIcon}).addTo(map).bindPopup('📍 To: ' + input.value).openPopup();
            }
            
            if (fromMarker && toMarker) calculateRoute();
          });
          sugBox.appendChild(div);
        });
      })
      .catch(err => console.error('Nominatim error:', err));
    }, 300);
  });

  document.addEventListener('click', function(e) {
    if (e.target !== input) sugBox.innerHTML = '';
  });
}

setupAutocomplete('from', 'from_suggestions', 'from');
setupAutocomplete('to', 'to_suggestions', 'to');

// ============================================
// ROUTE CALCULATION
// ============================================
function calculateRoute() {
  const fromLat = document.getElementById('from_lat').value;
  const fromLng = document.getElementById('from_lng').value;
  const toLat   = document.getElementById('to_lat').value;
  const toLng   = document.getElementById('to_lng').value;

  if (!fromLat || !fromLng || !toLat || !toLng) return;

  document.getElementById('routeInfo').style.display = 'block';
  document.getElementById('routeDistance').textContent = 'Calculating...';

  fetch('calculate_route.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      from_lat: parseFloat(fromLat), from_lng: parseFloat(fromLng),
      to_lat: parseFloat(toLat), to_lng: parseFloat(toLng)
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.error) { alert('Route Error: ' + data.error); return; }

    currentDistanceKm = data.distance_km;
    document.getElementById('distance_km').value = data.distance_km;
    document.getElementById('routeDistance').textContent = data.distance_km;
    document.getElementById('routeDuration').textContent = data.duration_min;

    if (routeLine) map.removeLayer(routeLine);
    
    if (data.geometry && data.geometry.coordinates) {
      const coords = data.geometry.coordinates.map(c => [c[1], c[0]]);
      routeLine = L.polyline(coords, { color: '#2196F3', weight: 5, opacity: 0.8 }).addTo(map);
      const group = L.featureGroup([fromMarker, toMarker, routeLine]);
      map.fitBounds(group.getBounds().pad(0.1));
    }

    updateFarePreview();
  })
  .catch(err => {
    console.error('Route error:', err);
    alert('Failed to calculate route.');
  });
}

// ============================================
// FARE PREVIEW (No student discount for tickets)
// ============================================
function updateFarePreview() {
  if (currentDistanceKm <= 0) return;
  const ratePerKm = <?= RATE_PER_KM ?>;
  const processingFee = <?= PROCESSING_FEE ?>;
  const baseFare = currentDistanceKm * ratePerKm;
  
  // Get bus type extra charge
  const busSelect = document.getElementById('bus_type_id');
  let busCharge = 0;
  if (busSelect.selectedIndex > 0) {
      const selectedOption = busSelect.options[busSelect.selectedIndex];
      busCharge = parseFloat(selectedOption.getAttribute('data-charge')) || 0;
  }
  
  if (busCharge > 0) {
      document.getElementById('busChargeRow').style.display = 'flex';
      document.getElementById('fareBusType').textContent = '₹' + busCharge.toFixed(2);
  } else {
      document.getElementById('busChargeRow').style.display = 'none';
  }

  const total = baseFare + processingFee + busCharge;

  document.getElementById('fareDistance').textContent = currentDistanceKm + ' km';
  document.getElementById('fareBase').textContent = '₹' + baseFare.toFixed(2);
  document.getElementById('fareTotal').textContent = '₹' + total.toFixed(2);
  document.getElementById('farePreview').style.display = 'block';
}

// Form validation
document.getElementById('ticketForm').addEventListener('submit', function(e) {
  const dist = document.getElementById('distance_km').value;
  if (!dist || parseFloat(dist) <= 0) {
    e.preventDefault();
    alert('⚠️ Please select both From and To locations and wait for route calculation.');
  }
});
</script>

</body>
</html>