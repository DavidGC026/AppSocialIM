<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['message' => 'No token provided']);
    exit;
}

$token = $matches[1];

try {
    $db = Database::getInstance();

    // Decode token to get expiration time
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        $expiresAt = date('Y-m-d H:i:s', $payload['exp'] ?? (time() + 86400));

        // Insert token into invalidated tokens table
        $db->insert(
            "INSERT INTO invalidated_tokens (token, expires_at) VALUES (?, ?) ON DUPLICATE KEY UPDATE invalidated_at = CURRENT_TIMESTAMP",
            [$token, $expiresAt]
        );
    }

    echo json_encode(['message' => 'Logged out successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
