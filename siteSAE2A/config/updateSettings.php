<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true) ?? [];
$val = $data['email_notifications'] ?? null;
$enabled = ($val === 1 || $val === '1' || $val === true || $val === 'true');

$settingsFile = __DIR__ . '/settings.json';
$settings = [];
if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
}

// Invert the saved value to match the toggle semantics (fixes inverted behavior reported by the UI)
$settings['email_notifications'] = !(bool)$enabled;

$tmp = tempnam(sys_get_temp_dir(), 's');
if (file_put_contents($tmp, json_encode($settings, JSON_PRETTY_PRINT)) !== false && rename($tmp, $settingsFile)) {
    echo json_encode(['success' => true, 'email_notifications' => $settings['email_notifications']]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not write settings']);
}
