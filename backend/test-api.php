<?php
// Simple API test script
header('Content-Type: text/plain');

echo "=== API Test Script ===\n\n";

$baseUrl = 'http://localhost:8000/api'; // Change this for production testing

// Test 1: Login with valid credentials
echo "Test 1: Login with valid credentials\n";
$loginData = json_encode([
    'email' => 'user@example.com',
    'password' => 'password123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $loginData
    ]
]);

$result = file_get_contents($baseUrl . '/login.php', false, $context);
if ($result) {
    $data = json_decode($result, true);
    if (isset($data['token'])) {
        echo "✅ Login successful\n";
        $token = $data['token'];
    } else {
        echo "❌ Login failed: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ Login request failed\n";
}

echo "\n";

// Test 2: Login with invalid credentials
echo "Test 2: Login with invalid credentials\n";
$loginData = json_encode([
    'email' => 'user@example.com',
    'password' => 'wrongpassword'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $loginData
    ]
]);

$result = file_get_contents($baseUrl . '/login.php', false, $context);
if ($result) {
    $data = json_decode($result, true);
    if (isset($data['message']) && $data['message'] === 'Invalid credentials') {
        echo "✅ Invalid credentials properly rejected\n";
    } else {
        echo "❌ Unexpected response: " . ($data['message'] ?? 'Unknown') . "\n";
    }
} else {
    echo "❌ Invalid login request failed\n";
}

echo "\n";

// Test 3: Verify token (if login was successful)
if (isset($token)) {
    echo "Test 3: Verify token\n";

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $token",
        ]
    ]);

    $result = file_get_contents($baseUrl . '/verify.php', false, $context);
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['user'])) {
            echo "✅ Token verification successful\n";
        } else {
            echo "❌ Token verification failed: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Token verification request failed\n";
    }

    echo "\n";

    // Test 4: Logout
    echo "Test 4: Logout\n";

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $token",
        ]
    ]);

    $result = file_get_contents($baseUrl . '/logout.php', false, $context);
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['message']) && $data['message'] === 'Logged out successfully') {
            echo "✅ Logout successful\n";
        } else {
            echo "❌ Logout failed: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Logout request failed\n";
    }
}

echo "\n";

// Test 5: Register new user
echo "Test 5: Register new user\n";
$registerData = json_encode([
    'email' => 'test@example.com',
    'password' => 'testpass123',
    'name' => 'Usuario de Prueba',
    'registrationCode' => 'VIEWER-2024-002'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $registerData
    ]
]);

$result = file_get_contents($baseUrl . '/register.php', false, $context);
if ($result) {
    $data = json_decode($result, true);
    if (isset($data['token'])) {
        echo "✅ User registration successful\n";
        $newToken = $data['token'];
    } else {
        echo "❌ User registration failed: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ User registration request failed\n";
}

echo "\n";

// Test 6: Login with newly registered user
if (isset($newToken)) {
    echo "Test 6: Login with newly registered user\n";

    $loginData = json_encode([
        'email' => 'test@example.com',
        'password' => 'testpass123'
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $loginData
        ]
    ]);

    $result = file_get_contents($baseUrl . '/login.php', false, $context);
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['token']) && isset($data['user']['role'])) {
            echo "✅ New user login successful, role: " . $data['user']['role'] . "\n";
        } else {
            echo "❌ New user login failed: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ New user login request failed\n";
    }
}

echo "\n";

// Test 7: Get events
echo "Test 7: Get events\n";
$result = file_get_contents($baseUrl . '/events.php', false, $context);
if ($result) {
    $data = json_decode($result, true);
    if (is_array($data)) {
        echo "✅ Events retrieved successfully (" . count($data) . " events)\n";
    } else {
        echo "❌ Events retrieval failed: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ Events request failed\n";
}

echo "\n";

// Test 8: Create event (if login was successful)
if (isset($token)) {
    echo "Test 8: Create event\n";

    $eventData = json_encode([
        'title' => 'Test Event',
        'description' => 'This is a test event',
        'startTime' => '14:00',
        'endTime' => '15:30',
        'date' => date('Y-m-d'),
        'location' => 'Test Room',
        'color' => 'bg-red-500',
        'organizer' => 'Test User',
        'attendees' => ['John Doe', 'Jane Smith']
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $token",
            'content' => $eventData
        ]
    ]);

    $result = file_get_contents($baseUrl . '/events.php', false, $context);
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['event_id'])) {
            echo "✅ Event created successfully (ID: " . $data['event_id'] . ")\n";
            $createdEventId = $data['event_id'];
        } else {
            echo "❌ Event creation failed: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Event creation request failed\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "Para probar en producción, cambia \$baseUrl a la URL de tu servidor.\n";
?>
