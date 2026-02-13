<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';
require_once '../config/env.php';

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
    // Check if token is invalidated
    $db = Database::getInstance();
    $invalidated = $db->selectOne(
        "SELECT id FROM invalidated_tokens WHERE token = ? AND expires_at > NOW()",
        [$token]
    );

    if ($invalidated) {
        http_response_code(401);
        echo json_encode(['message' => 'Token has been invalidated']);
        exit;
    }

    // Simple token verification (simplified, use proper JWT library in production)
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid token']);
        exit;
    }

    $header = $parts[0];
    $payload = $parts[1];
    $signature = $parts[2];

    // Verify signature
    $jwtSecret = env('JWT_SECRET', 'your-secret-key-change-this-in-production');
    $expectedSignature = hash_hmac('sha256', $header . "." . $payload, $jwtSecret);
    $expectedSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

    if (!hash_equals($signature, $expectedSignatureEncoded)) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid token signature']);
        exit;
    }

    // Decode payload
    $payloadDecoded = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

    if (!$payloadDecoded || !isset($payloadDecoded['exp']) || $payloadDecoded['exp'] < time()) {
        http_response_code(401);
        echo json_encode(['message' => 'Token expired']);
        exit;
    }

    // Get user data from database
    $user = $db->selectOne(
        "SELECT id, email, name, role FROM users WHERE id = ?",
        [$payloadDecoded['user_id']]
    );

    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'User not found']);
        exit;
    }

    echo json_encode(['user' => [
        'id' => (string)$user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role']
    ]]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
