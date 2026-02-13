<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    exit(0);
}

// Get authorization header
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['message' => 'No token provided']);
    exit;
}

$token = $matches[1];

// Verify token and get user
try {
    $db = Database::getInstance();

    // Check if token is invalidated (skip if table doesn't exist)
    try {
        $invalidated = $db->selectOne(
            "SELECT id FROM invalidated_tokens WHERE token = ? AND expires_at > NOW()",
            [$token]
        );

        if ($invalidated) {
            http_response_code(401);
            echo json_encode(['message' => 'Token has been invalidated']);
            exit;
        }
    } catch (Exception $e) {
        // Table might not exist, continue without token invalidation check
    }

    // Decode and verify token
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid token']);
        exit;
    }

    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
    if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
        http_response_code(401);
        echo json_encode(['message' => 'Token expired']);
        exit;
    }

    $userId = $payload['user_id'];

    // Get user role
    $user = $db->selectOne("SELECT role FROM users WHERE id = ?", [$userId]);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'User not found']);
        exit;
    }

    $userRole = $user['role'];

    switch ($method) {
        case 'GET':
            // Get all users (only basic info for attendees selection)
            $users = $db->select(
                "SELECT id, name, email FROM users ORDER BY name"
            );

            echo json_encode($users);
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>