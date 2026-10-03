<?php
// Safe session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/../includes/api_config.php');

$error = '';
$user_email = $_SESSION['user'];

// fetch
$stmt = $conn->prepare("SELECT * FROM passes WHERE email=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$old_pass = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$old_pass) {
    die("❌ No existing pass found. Please apply for a new pass first.");
}

// Fetch manual routes
$routes_result = $conn->query("SELECT id, route_name, from_location, to_location, distance_km FROM routes ORDER BY route_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name'] ?? '');
    $dob           = trim($_POST['dob'] ?? '');
    $age           = (int)trim($_POST['age'] ?? 0); // ✅ user-entered age
    $gender        = trim($_POST['gender'] ?? '');
    $from_location = trim($_POST['from_location'] ?? '');
    $to_location   = trim($_POST['to_location'] ?? '');
    $distance_km   = floatval($_POST['distance_km'] ?? 0);
    $route         = trim($_POST['route'] ?? '');
    $pass_type     = trim($_POST['pass_type'] ?? '');
    $duration      = trim($_POST['renew_duration'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $address       = trim($_POST['address'] ?? '');

    // नया validity calculate
    $old_to   = $old_pass['pass_to'];
    $new_from = date("Y-m-d", strtotime($old_to . " +1 day"));

    if ($duration == "1 Month") {
        $new_to = date("Y-m-d", strtotime($new_from . " +1 month"));
    } elseif ($duration == "3 Months") {
        $new_to = date("Y-m-d", strtotime($new_from . " +3 months"));
    } elseif ($duration == "6 Months") {
        $new_to = date("Y-m-d", strtotime($new_from . " +6 months"));
    } else {
        $new_to = date("Y-m-d", strtotime($new_from . " +1 year"));
    }

    // Dynamic fee calculation based on distance
    if ($distance_km <= 0) {
        $error = "Please select valid locations and calculate the route first.";
    }

    $fee = calculateFare($distance_km, $pass_type, $duration);

    // Photo (default पुराना वाला)
    $photo = $old_pass['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoName = time() . "_" . preg_replace('/\s+/', '_', basename($_FILES['photo']['name']));
        $photoDir = __DIR__ . "/uploads/photos";

        if (!is_dir($photoDir)) {
            mkdir($photoDir, 0777, true);
        }

        $targetFile = $photoDir . "/" . $photoName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = "uploads/photos/" . $photoName;
        } else {
            $error = "Photo upload failed. Please try again.";
        }
    }

    if (empty($error)) {
        // Payment के लिए session में store
        $_SESSION['pending_pass'] = [
            'user_id'       => $old_pass['user_id'],
            'name'          => $name,
            'email'         => $email,
            'dob'           => $dob,
            'age'           => $age,           // ✅ Added age
            'gender'        => $gender,
            'from_location' => $from_location,
            'to_location'   => $to_location,
            'distance_km'   => $distance_km,
            'route'         => $route,
            'pass_type'     => $pass_type,
            'pass_duration' => $duration,
            'pass_from'     => $new_from,
            'pass_to'       => $new_to,
            'address'       => $address,
            'photo'         => $photo,
            'fee'           => $fee
        ];

        header("Location: payment.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Renew Bus Pass - E-Bus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/bootstrap.bundle.min.js"></script>

  <!-- Leaflet.js CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <style>
    body {
      background: url("/ebus/images/21.png") no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .renew-container {
      background: #ffffffdd;
      padding: 40px 50px;
      border-radius: 20px;
      max-width: 900px;
      margin: 60px auto;
      box-shadow: 0 6px 25px rgba(0,0,0,0.3);
      position: relative;
    }
    .form-header { text-align: center; margin-bottom: 35px; }
    .form-header img { height: 70px; margin-bottom: 15px; }
    .form-header h2 { font-weight: bold; color: #222; }
    .form-header p { color: #666; font-size: 15px; }
    .form-control, .form-select { height: 50px; font-size: 16px; border-radius: 10px; border: 1px solid #ddd; }
    textarea.form-control { height: auto; }
    .btn-custom { font-size: 18px; padding: 12px; border-radius: 12px; width: 100%; }
    .back-btn { position: absolute; top: 15px; left: 15px; }

    /* Map container */
    #map {
      height: 350px;
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
    .autocomplete-item:hover {
      background-color: #e9f5ff;
    }

    /* Fare preview card */
    .fare-preview {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 25px;
      border-radius: 15px;
      margin-top: 15px;
      display: none;
    }
    .fare-preview h5 { margin-bottom: 15px; font-weight: 600; }
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
    .route-info {
      background: #e8f5e9;
      padding: 12px 18px;
      border-radius: 10px;
      margin-top: 10px;
      display: none;
    }
    .route-info span { font-weight: 600; color: #2e7d32; }
  </style>
</head>
<body>

<div class="renew-container">
  <a href="user_dashboard.php" class="btn btn-secondary back-btn">⬅ Back</a>

  <div class="form-header">
    <img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="Bus Logo">
    <h2>♻ Renew Your Bus Pass</h2>
    <p>Please review and update details for your renewal.</p>
  </div>

  <?php if(!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" id="renewForm">
    <div class="row g-4">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($old_pass['name']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="dob" class="form-control"
               value="<?= htmlspecialchars($old_pass['dob']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Age</label>
        <input type="number" name="age" class="form-control" 
               value="<?= htmlspecialchars($old_pass['age'] ?? '') ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select" required>
          <option value="">-- Select Gender --</option>
          <option value="Male"   <?= $old_pass['gender']=="Male"?"selected":"" ?>>Male</option>
          <option value="Female" <?= $old_pass['gender']=="Female"?"selected":"" ?>>Female</option>
          <option value="Other"  <?= $old_pass['gender']=="Other"?"selected":"" ?>>Other</option>
        </select>
      </div>

      <!-- FROM Location with Autocomplete -->
      <div class="col-md-6" style="position:relative;">
        <label class="form-label">📍 From (Location)</label>
        <input type="text" id="from_location" name="from_location" class="form-control" 
               value="<?= htmlspecialchars($old_pass['from_location']) ?>"
               placeholder="Type city name..." autocomplete="off" required>
        <div id="from_suggestions" class="autocomplete-list"></div>
        <small class="text-muted">Type & select from suggestions to set map route</small>
      </div>

      <!-- TO Location with Autocomplete -->
      <div class="col-md-6" style="position:relative;">
        <label class="form-label">📍 To (Location)</label>
        <input type="text" id="to_location" name="to_location" class="form-control" 
               value="<?= htmlspecialchars($old_pass['to_location']) ?>"
               placeholder="Type city name..." autocomplete="off" required>
        <div id="to_suggestions" class="autocomplete-list"></div>
        <small class="text-muted">Type & select from suggestions to set map route</small>
      </div>

      <!-- Hidden fields for coordinates and distance -->
      <input type="hidden" id="from_lat" name="from_lat">
      <input type="hidden" id="from_lng" name="from_lng">
      <input type="hidden" id="to_lat" name="to_lat">
      <input type="hidden" id="to_lng" name="to_lng">
      <input type="hidden" id="distance_km" name="distance_km">

      <!-- Map (Hidden per user request) -->
      <div class="col-md-12" style="display: none;">
        <label class="form-label">🗺️ Route Map</label>
        <div id="map"></div>
      </div>

      <!-- Route Info -->
      <div class="col-md-12">
        <div id="routeInfo" class="route-info">
          🚌 Distance: <span id="routeDistance">--</span> km &nbsp;|&nbsp; 
          ⏱ Estimated Time: <span id="routeDuration">--</span> min
        </div>
      </div>

      <div class="col-md-6">
        <label class="form-label">Route</label>
        <select name="route" id="manual_route" class="form-select" required onchange="handleManualRoute()">
          <option value="">-- Select Route --</option>
          <?php if($routes_result) while ($r = $routes_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($r['route_name']) ?>" 
                    data-distance="<?= htmlspecialchars($r['distance_km']) ?>"
                    data-from="<?= htmlspecialchars($r['from_location']) ?>"
                    data-to="<?= htmlspecialchars($r['to_location']) ?>"
                    <?= $old_pass['route']==$r['route_name']?"selected":"" ?>>
              <?= htmlspecialchars($r['route_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Pass Type</label>
        <select name="pass_type" id="pass_type" class="form-select" required onchange="updateFarePreview()">
          <option value="">-- Select Pass Type --</option>
          <option value="Student"  <?= $old_pass['pass_type']=="Student"?"selected":"" ?>>Student</option>
          <option value="Regular"  <?= $old_pass['pass_type']=="Regular"?"selected":"" ?>>Passenger</option>
          <option value="Handicap" <?= $old_pass['pass_type']=="Handicap"?"selected":"" ?>>Handicap Person</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Renew Duration</label>
        <select name="renew_duration" id="renew_duration" class="form-select" required onchange="updateFarePreview()">
          <option value="">-- Select Duration --</option>
          <option value="1 Month">1 Month</option>
          <option value="3 Months">3 Months</option>
          <option value="6 Months">6 Months</option>
          <option value="1 Year">1 Year</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= htmlspecialchars($old_pass['email']) ?>" required>
      </div>

      <div class="col-md-12">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($old_pass['address']) ?></textarea>
      </div>

      <div class="col-md-12">
        <label class="form-label">Upload New Photo (optional)</label>
        <input type="file" name="photo" class="form-control" accept="image/*">
      </div>

      <!-- Fare Preview -->
      <div class="col-md-12">
        <div id="farePreview" class="fare-preview">
          <h5>💰 Fare Estimate</h5>
          <div class="fare-row">
            <span>Daily Fare (Distance + ₹<?= PROCESSING_FEE ?>):</span>
            <span id="fareBase">₹--</span>
          </div>
          <div class="fare-row" id="durationRow" style="display:none;">
            <span>Duration Multiplier:</span>
            <span id="fareDuration">x1 days</span>
          </div>
          <div class="fare-row" id="discountRow" style="display:none;">
            <span>Student Discount (50%):</span>
            <span id="fareDiscount">-₹--</span>
          </div>
          <div class="fare-row fare-total">
            <span>Total Fee:</span>
            <span id="fareTotal">₹--</span>
          </div>
        </div>
      </div>

      <div class="col-md-12 mt-4">
        <button type="submit" class="btn btn-primary btn-custom" id="submitBtn">Proceed to Payment</button>
      </div>
    </div>
  </form>
</div>

<!-- Leaflet.js JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ============================================
// MAP INITIALIZATION
// ============================================
const map = L.map('map').setView([22.3, 71.8], 7); // Gujarat center

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors',
  maxZoom: 18
}).addTo(map);

let fromMarker = null;
let toMarker = null;
let routeLine = null;
let currentDistanceKm = 0;

// Custom icons
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
    
    if (query.length < 3) {
      sugBox.innerHTML = '';
      return;
    }

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
            input.value = item.display_name.split(',')[0]; // Short name
            sugBox.innerHTML = '';
            
            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lon);
            
            if (type === 'from') {
              document.getElementById('from_lat').value = lat;
              document.getElementById('from_lng').value = lng;
              if (fromMarker) map.removeLayer(fromMarker);
              fromMarker = L.marker([lat, lng], {icon: greenIcon}).addTo(map).bindPopup('📍 Source: ' + input.value).openPopup();
            } else {
              document.getElementById('to_lat').value = lat;
              document.getElementById('to_lng').value = lng;
              if (toMarker) map.removeLayer(toMarker);
              toMarker = L.marker([lat, lng], {icon: redIcon}).addTo(map).bindPopup('📍 Destination: ' + input.value).openPopup();
            }
            
            // If both markers are set, calculate route
            if (fromMarker && toMarker) {
              calculateRoute();
            }
          });
          sugBox.appendChild(div);
        });
      })
      .catch(err => console.error('Nominatim error:', err));
    }, 300);
  });

  // Also trigger suggestions if user focuses input with existing value
  input.addEventListener('focus', function() {
    if (this.value.trim().length >= 3 && sugBox.children.length === 0) {
      this.dispatchEvent(new Event('input'));
    }
  });

  // Reset coordinates if user starts typing manually
  input.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' && e.key !== 'Tab') {
      if (type === 'from') {
        document.getElementById('from_lat').value = '';
        document.getElementById('from_lng').value = '';
      } else {
        document.getElementById('to_lat').value = '';
        document.getElementById('to_lng').value = '';
      }
      document.getElementById('distance_km').value = '';
      currentDistanceKm = 0;
    }
  });

  // Hide suggestions on click outside
  document.addEventListener('click', function(e) {
    if (e.target !== input) sugBox.innerHTML = '';
  });
}

// Initialize autocomplete for both fields
setupAutocomplete('from_location', 'from_suggestions', 'from');
setupAutocomplete('to_location', 'to_suggestions', 'to');

// ============================================
// ROUTE CALCULATION (via PHP proxy)
// ============================================
function calculateRoute() {
  const fromLat = document.getElementById('from_lat').value;
  const fromLng = document.getElementById('from_lng').value;
  const toLat   = document.getElementById('to_lat').value;
  const toLng   = document.getElementById('to_lng').value;

  if (!fromLat || !fromLng || !toLat || !toLng) return;

  // Show loading state
  document.getElementById('routeInfo').style.display = 'block';
  document.getElementById('routeDistance').textContent = 'Calculating...';
  document.getElementById('routeDuration').textContent = '...';

  fetch('calculate_route.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      from_lat: parseFloat(fromLat),
      from_lng: parseFloat(fromLng),
      to_lat: parseFloat(toLat),
      to_lng: parseFloat(toLng)
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.error) {
      alert('Route Error: ' + data.error);
      return;
    }

    // Save distance
    currentDistanceKm = data.distance_km;
    document.getElementById('distance_km').value = data.distance_km;

    // Show route info
    document.getElementById('routeDistance').textContent = data.distance_km;
    document.getElementById('routeDuration').textContent = data.duration_min;

    // Draw route on map
    if (routeLine) map.removeLayer(routeLine);
    
    if (data.geometry && data.geometry.coordinates) {
      const coords = data.geometry.coordinates.map(c => [c[1], c[0]]); // GeoJSON is [lng, lat], Leaflet needs [lat, lng]
      routeLine = L.polyline(coords, {
        color: '#2196F3',
        weight: 5,
        opacity: 0.8
      }).addTo(map);
      
      // Fit map to show full route
      const group = L.featureGroup([fromMarker, toMarker, routeLine]);
      map.fitBounds(group.getBounds().pad(0.1));
    }

    // Update fare preview
    updateFarePreview();
  })
  .catch(err => {
    console.error('Route calculation error:', err);
    alert('Failed to calculate route. Please try again.');
  });
}

// ============================================
// FARE PREVIEW UPDATE
// ============================================
function updateFarePreview() {
  if (currentDistanceKm <= 0) return;

  const passType = document.getElementById('pass_type').value;
  const passDuration = document.getElementById('renew_duration').value;
  const ratePerKm = <?= RATE_PER_KM ?>;
  const processingFee = <?= PROCESSING_FEE ?>;
  const dailyBaseFare = (currentDistanceKm * ratePerKm) + processingFee;
  
  let multiplier = 1;
  if (passDuration === '1 Month') multiplier = 30;
  else if (passDuration === '3 Months') multiplier = 90;
  else if (passDuration === '6 Months') multiplier = 180;
  else if (passDuration === '1 Year') multiplier = 365;

  const subtotal = dailyBaseFare * multiplier;
  
  let discount = 0;
  let total = subtotal;

  if (passType === 'Student') {
    discount = subtotal * 0.5;
    total = subtotal - discount;
    document.getElementById('discountRow').style.display = 'flex';
    document.getElementById('fareDiscount').textContent = '-₹' + discount.toFixed(2);
  } else {
    document.getElementById('discountRow').style.display = 'none';
  }

  if (multiplier > 1) {
    document.getElementById('durationRow').style.display = 'flex';
    document.getElementById('fareDuration').textContent = 'x' + multiplier + ' days';
  } else {
    document.getElementById('durationRow').style.display = 'none';
  }

  document.getElementById('fareBase').textContent = '₹' + dailyBaseFare.toFixed(2);
  document.getElementById('fareTotal').textContent = '₹' + total.toFixed(2);
  document.getElementById('farePreview').style.display = 'block';
}

// ============================================
// MANUAL ROUTE HANDLING
// ============================================
function handleManualRoute() {
  const routeSelect = document.getElementById('manual_route');
  if (routeSelect.selectedIndex <= 0) return;
  
  const selectedOption = routeSelect.options[routeSelect.selectedIndex];
  const km = parseFloat(selectedOption.getAttribute('data-distance')) || 0;
  const fromCity = selectedOption.getAttribute('data-from');
  const toCity = selectedOption.getAttribute('data-to');
  
  if (km > 0) {
    // Fill the inputs
    document.getElementById('from_location').value = fromCity;
    document.getElementById('to_location').value = toCity;
    
    // Update distance
    currentDistanceKm = km;
    document.getElementById('distance_km').value = km;
    document.getElementById('routeDistance').textContent = km;
    document.getElementById('routeDuration').textContent = 'N/A';
    
    // Calculate Fare
    updateFarePreview();
  }
}

// ============================================
// FORM VALIDATION
// ============================================
document.getElementById('renewForm').addEventListener('submit', function(e) {
  const dist = document.getElementById('distance_km').value;
  if (!dist || parseFloat(dist) <= 0) {
    e.preventDefault();
    alert('⚠️ Please select both From and To locations from suggestions and wait for the route to be calculated.');
  }
});
</script>

</body>
</html>