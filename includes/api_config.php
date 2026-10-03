<?php
// ============================================
// API Configuration - E-Bus Pass System
// ============================================

// OpenRouteService API Key (FREE tier — 2000 requests/day)
// Used by calculate_route.php to get route distance
define('ORS_API_KEY', 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImRhNGNlYTI4OTRjYTRlN2I4ZjVjYTE0YzdiYjBiYmMyIiwiaCI6Im11cm11cjY0In0=');

// ============================================
// Fare Calculation Settings
// ============================================

// Rate per kilometer in Rupees
define('RATE_PER_KM', 4);

// Flat processing fee (ticket, booking, pass registration)
define('PROCESSING_FEE', 2);

// Student discount percentage (50% = 0.50)
define('STUDENT_DISCOUNT', 0.50);

// ============================================
// Helper function to calculate fare
// ============================================
function getDurationDays($duration) {
    switch ($duration) {
        case '1 Month': return 30;
        case '3 Months': return 90;
        case '6 Months': return 180;
        case '1 Year': return 365;
        default: return 1; // Default to 1 day for tickets or unspecified
    }
}

function calculateFare($distance_km, $pass_type, $duration = '1 Day') {
    $daily_base_fare = ($distance_km * RATE_PER_KM) + PROCESSING_FEE;
    $days = getDurationDays($duration);
    
    $total_base_fare = $daily_base_fare * $days;
    
    // Apply 50% discount for Student pass type only
    if ($pass_type === 'Student') {
        $fare = round($total_base_fare * (1 - STUDENT_DISCOUNT), 2);
    } else {
        $fare = round($total_base_fare, 2);
    }
    
    return $fare;
}
?>
