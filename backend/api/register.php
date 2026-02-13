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

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['email']) || !isset($input['password']) || !isset($input['name']) || !isset($input['registrationCode'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Email, password, name, and registration code are required']);
    exit;
}

$email = trim($input['email']);
$password = $input['password'];
$name = trim($input['name']);
$registrationCode = trim($input['registrationCode']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['message' => 'Password must be at least 6 characters long']);
    exit;
}

if (strlen($name) < 2) {
    http_response_code(400);
    echo json_encode(['message' => 'Name must be at least 2 characters long']);
    exit;
}

try {
    $db = Database::getInstance();

    // Check if email already exists
    $existingUser = $db->selectOne(
        "SELECT id FROM users WHERE email = ?",
        [$email]
    );

    if ($existingUser) {
        http_response_code(409);
        echo json_encode(['message' => 'Email already registered']);
        exit;
    }

    // Validate registration code
    $codeData = $db->selectOne(
        "SELECT id, role, used, expires_at FROM registration_codes WHERE code = ?",
        [$registrationCode]
    );

    if (!$codeData) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid registration code']);
        exit;
    }

    if ($codeData['used']) {
        http_response_code(400);
        echo json_encode(['message' => 'Registration code has already been used']);
        exit;
    }

    if ($codeData['expires_at'] && strtotime($codeData['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode(['message' => 'Registration code has expired']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create user
    $userId = $db->insert(
        "INSERT INTO users (email, password, name, role) VALUES (?, ?, ?, ?)",
        [$email, $hashedPassword, $name, $codeData['role']]
    );

    // Mark code as used
    $db->update(
        "UPDATE registration_codes SET used = TRUE WHERE id = ?",
        [$codeData['id']]
    );

    // Generate JWT token
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $userId,
        'email' => $email,
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ]);

    $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    // Simple signature (use proper signing in production)
    $jwtSecret = env('JWT_SECRET', 'your-secret-key-change-this-in-production');
    $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $jwtSecret);
    $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $token = $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;

    echo json_encode([
        'message' => 'User registered successfully',
        'token' => $token,
        'user' => [
            'id' => (string)$userId,
            'email' => $email,
            'name' => $name,
            'role' => $codeData['role']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
