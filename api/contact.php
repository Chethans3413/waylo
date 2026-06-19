<?php
// waylo.in — Contact message handler
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

$name    = htmlspecialchars(trim($data['name']    ?? ''));
$email   = htmlspecialchars(trim($data['email']   ?? ''));
$subject = htmlspecialchars(trim($data['subject'] ?? 'Contact Message'));
$message = htmlspecialchars(trim($data['message'] ?? ''));

$to      = 'freight@waylo.in';
$subjectLine = "Contact: $subject — from $name";
$body    = "
New contact message on waylo.in

Name:    $name
Email:   $email
Subject: $subject
Message: $message

Sent at: " . date('Y-m-d H:i:s') . "
";

$headers  = "From: noreply@waylo.in\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

mail($to, $subjectLine, $body, $headers);

echo json_encode([
    'success' => true,
    'message' => 'Message received. Thank you!',
]);
