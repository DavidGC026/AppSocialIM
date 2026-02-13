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
            // Get all events or single event
            if (isset($_GET['id'])) {
                // Get single event
                $eventId = (int)$_GET['id'];
                $event = $db->selectOne(
                    "SELECT e.*, u.name as creator_name FROM events e JOIN users u ON e.created_by = u.id WHERE e.id = ?",
                    [$eventId]
                );

                if (!$event) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Event not found']);
                    exit;
                }

                // Get attendees
                $attendees = $db->select(
                    "SELECT COALESCE(u.name, ea.attendee_name) as name FROM event_attendees ea LEFT JOIN users u ON ea.user_id = u.id WHERE ea.event_id = ?",
                    [$eventId]
                );

                $event['attendees'] = array_column($attendees, 'name');

                // Format field names to match frontend expectations
                $event['startTime'] = $event['start_time'];
                $event['endTime'] = $event['end_time'];
                unset($event['start_time'], $event['end_time']);

                echo json_encode($event);
            } else {
                // Get all events
                $events = $db->select(
                    "SELECT e.*, u.name as creator_name FROM events e JOIN users u ON e.created_by = u.id ORDER BY e.date, e.start_time"
                );

                // Get attendees for each event and format field names
                foreach ($events as &$event) {
                    $attendees = $db->select(
                        "SELECT COALESCE(u.name, ea.attendee_name) as name FROM event_attendees ea LEFT JOIN users u ON ea.user_id = u.id WHERE ea.event_id = ?",
                        [$event['id']]
                    );
                    $event['attendees'] = array_column($attendees, 'name');

                    // Format field names to match frontend expectations
                    $event['startTime'] = $event['start_time'];
                    $event['endTime'] = $event['end_time'];
                    unset($event['start_time'], $event['end_time']);
                }

                echo json_encode($events);
            }
            break;

        case 'POST':
            // Create new event (admin only)
            if ($userRole !== 'admin') {
                http_response_code(403);
                echo json_encode(['message' => 'Only administrators can create events']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['title']) || !isset($input['startTime']) || !isset($input['endTime']) || !isset($input['date'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Title, start time, end time, and date are required']);
                exit;
            }

            $title = trim($input['title']);
            $description = trim($input['description'] ?? '');
            $startTime = $input['startTime'];
            $endTime = $input['endTime'];
            $date = $input['date'];
            $location = trim($input['location'] ?? '');
            $color = $input['color'] ?? 'bg-blue-500';
            $organizer = trim($input['organizer'] ?? 'Usuario');
            $attendees = $input['attendeeIds'] ?? [];

            // Validate time format
            if (!preg_match('/^\d{2}:\d{2}$/', $startTime) || !preg_match('/^\d{2}:\d{2}$/', $endTime)) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid time format']);
                exit;
            }

            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid date format']);
                exit;
            }

            // Create event
            $eventId = $db->insert(
                "INSERT INTO events (title, description, start_time, end_time, date, location, color, organizer, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$title, $description, $startTime, $endTime, $date, $location, $color, $organizer, $userId]
            );

            // Add attendees
            foreach ($attendees as $attendeeId) {
                if (!empty($attendeeId) && is_numeric($attendeeId)) {
                    $db->insert(
                        "INSERT INTO event_attendees (event_id, user_id) VALUES (?, ?)",
                        [$eventId, (int)$attendeeId]
                    );
                }
            }

            echo json_encode([
                'message' => 'Event created successfully',
                'event_id' => $eventId
            ]);
            break;

        case 'PUT':
            // Update event (admin only)
            if ($userRole !== 'admin') {
                http_response_code(403);
                echo json_encode(['message' => 'Only administrators can update events']);
                exit;
            }

            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Event ID is required']);
                exit;
            }

            $eventId = (int)$_GET['id'];
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input data']);
                exit;
            }

            // Check if event exists and belongs to user
            $event = $db->selectOne("SELECT id FROM events WHERE id = ? AND created_by = ?", [$eventId, $userId]);
            if (!$event) {
                http_response_code(404);
                echo json_encode(['message' => 'Event not found or access denied']);
                exit;
            }

            $updateFields = [];
            $updateValues = [];

            if (isset($input['title'])) {
                $updateFields[] = "title = ?";
                $updateValues[] = trim($input['title']);
            }
            if (isset($input['description'])) {
                $updateFields[] = "description = ?";
                $updateValues[] = trim($input['description']);
            }
            if (isset($input['startTime'])) {
                $updateFields[] = "start_time = ?";
                $updateValues[] = $input['startTime'];
            }
            if (isset($input['endTime'])) {
                $updateFields[] = "end_time = ?";
                $updateValues[] = $input['endTime'];
            }
            if (isset($input['date'])) {
                $updateFields[] = "date = ?";
                $updateValues[] = $input['date'];
            }
            if (isset($input['location'])) {
                $updateFields[] = "location = ?";
                $updateValues[] = trim($input['location']);
            }
            if (isset($input['color'])) {
                $updateFields[] = "color = ?";
                $updateValues[] = $input['color'];
            }
            if (isset($input['organizer'])) {
                $updateFields[] = "organizer = ?";
                $updateValues[] = trim($input['organizer']);
            }

            if (empty($updateFields)) {
                http_response_code(400);
                echo json_encode(['message' => 'No fields to update']);
                exit;
            }

            $updateValues[] = $eventId;
            $db->update(
                "UPDATE events SET " . implode(', ', $updateFields) . " WHERE id = ?",
                $updateValues
            );

            // Update attendees if provided
            if (isset($input['attendeeIds'])) {
                // Delete existing attendees
                $db->delete("DELETE FROM event_attendees WHERE event_id = ?", [$eventId]);

                // Add new attendees
                foreach ($input['attendeeIds'] as $attendeeId) {
                    if (!empty($attendeeId) && is_numeric($attendeeId)) {
                        $db->insert(
                            "INSERT INTO event_attendees (event_id, user_id) VALUES (?, ?)",
                            [$eventId, (int)$attendeeId]
                        );
                    }
                }
            }

            echo json_encode(['message' => 'Event updated successfully']);
            break;

        case 'DELETE':
            // Delete event (admin only)
            if ($userRole !== 'admin') {
                http_response_code(403);
                echo json_encode(['message' => 'Only administrators can delete events']);
                exit;
            }

            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Event ID is required']);
                exit;
            }

            $eventId = (int)$_GET['id'];

            // Check if event exists and belongs to user
            $event = $db->selectOne("SELECT id FROM events WHERE id = ? AND created_by = ?", [$eventId, $userId]);
            if (!$event) {
                http_response_code(404);
                echo json_encode(['message' => 'Event not found or access denied']);
                exit;
            }

            // Delete attendees first (foreign key constraint)
            $db->delete("DELETE FROM event_attendees WHERE event_id = ?", [$eventId]);

            // Delete event
            $db->delete("DELETE FROM events WHERE id = ?", [$eventId]);

            echo json_encode(['message' => 'Event deleted successfully']);
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
