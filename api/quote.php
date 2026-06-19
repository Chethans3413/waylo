<?php
// waylo.in — Quote request handler
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://waylo.in');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Sanitize inputs
$name      = htmlspecialchars(trim($data['name']      ?? ''));
$email     = htmlspecialchars(trim($data['email']     ?? ''));
$phone     = htmlspecialchars(trim($data['phone']     ?? ''));
$pickup    = htmlspecialchars(trim($data['pickup']    ?? ''));
$delivery  = htmlspecialchars(trim($data['delivery']  ?? ''));
$cargoType = htmlspecialchars(trim($data['cargoType'] ?? ''));
$weight    = htmlspecialchars(trim($data['weight']    ?? ''));
$date      = htmlspecialchars(trim($data['date']      ?? ''));
$message   = htmlspecialchars(trim($data['message']   ?? ''));

// ── Send email notification ──────────────────────────────────────────────────
// IMPORTANT: Replace the TO address below with your actual business email
$to      = 'freight@waylo.in';
$subject = "New Quote Request from $name — Waylo";
$body    = "
New quote request received on waylo.in

Name:      $name
Email:     $email
Phone:     $phone
Pickup:    $pickup
Delivery:  $delivery
Cargo:     $cargoType
Weight:    $weight lbs
Date:      $date
Notes:     $message

Sent at: " . date('Y-m-d H:i:s') . "
";

$headers  = "From: noreply@waylo.in\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Log to file as backup (optional — comment out if not needed)
$log = date('[Y-m-d H:i:s]') . " Quote: $name | $pickup → $delivery | $email\n";
file_put_contents(__DIR__ . '/../../logs/quotes.log', $log, FILE_APPEND | LOCK_EX);

// Send email
mail($to, $subject, $body, $headers);

echo json_encode([
    'success' => true,
    'id'      => time(),
    'message' => "Quote request received. We'll contact you within 2 hours.",
]);
