<?php
// ============================================
// Secure Route Calculator - PHP Proxy
// ============================================
// Called by frontend JS via AJAX.
// Keeps the OpenRouteService API key server-side.
// 
// EXPECTS (POST JSON):
//   from_lng, from_lat, to_lng, to_lat
//
// RETURNS (JSON):
//   distance_km, duration_min, geometry (GeoJSON)
// ============================================

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit();
}

// Load API config
include(__DIR__ . '/../includes/api_config.php');

// Get input
$input = json_decode(file_get_contents('php://input'), true);

$from_lng = floatval($input['from_lng'] ?? 0);
$from_lat = floatval($input['from_lat'] ?? 0);
$to_lng   = floatval($input['to_lng'] ?? 0);
$to_lat   = floatval($input['to_lat'] ?? 0);

// Validate coordinates
if ($from_lng == 0 || $from_lat == 0 || $to_lng == 0 || $to_lat == 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid coordinates provided.']);
    exit();
}

// Build OpenRouteService API URL
$ors_url = "https://api.openrouteservice.org/v2/directions/driving-car"
         . "?start=$from_lng,$from_lat"
         . "&end=$to_lng,$to_lat";

// Make API call with cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $ors_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_HTTPHEADER     => [
        'Authorization: ' . ORS_API_KEY,
        'Accept: application/json, application/geo+json'
    ]
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle cURL errors
if ($curl_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to routing service.']);
    exit();
}

// Handle API errors
if ($http_code !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'Routing service returned error.', 'status' => $http_code]);
    exit();
}

// Parse response
$data = json_decode($response, true);

if (!$data || !isset($data['features'][0])) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid response from routing service.']);
    exit();
}

$feature    = $data['features'][0];
$properties = $feature['properties']['summary'] ?? [];
$geometry   = $feature['geometry'] ?? [];

// Extract distance (meters → km) and duration (seconds → minutes)
$distance_m   = $properties['distance'] ?? 0;
$duration_s   = $properties['duration'] ?? 0;
$distance_km  = round($distance_m / 1000, 2);
$duration_min = round($duration_s / 60, 0);

// Calculate fare using the helper function
$fare_regular = calculateFare($distance_km, 'Regular');
$fare_student = calculateFare($distance_km, 'Student');

// Return clean JSON
echo json_encode([
    'success'       => true,
    'distance_km'   => $distance_km,
    'duration_min'  => $duration_min,
    'fare_regular'  => $fare_regular,
    'fare_student'  => $fare_student,
    'rate_per_km'   => RATE_PER_KM,
    'processing_fee'=> PROCESSING_FEE,
    'geometry'      => $geometry
]);
?>
