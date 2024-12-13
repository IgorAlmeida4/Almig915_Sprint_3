<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$nodeId = $data['node_id'] ?? null;
$action = $data['action'] ?? null;

if ($nodeId && $action) {
    try {
        // Define SQL queries based on action
        $queries = [
            'water' => "UPDATE nodes SET water_pump = 100 WHERE id = ?",
            'light_on' => "UPDATE nodes SET light_level = 100 WHERE id = ?",
            'light_off' => "UPDATE nodes SET light_level = 0 WHERE id = ?"
        ];

        // Check if action is valid
        if (array_key_exists($action, $queries)) {
            $stmt = $pdo->prepare($queries[$action]);
            $stmt->execute([$nodeId]);

            // Prepare the appropriate response message
            $messages = [
                'water' => '100ml water pumped!',
                'light_on' => 'Grow light set to 100%!',
                'light_off' => 'Grow light turned off!'
            ];

            echo json_encode(['message' => $messages[$action]]);
        } else {
            echo json_encode(['message' => 'Invalid action.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['message' => 'Missing parameters.']);
}
?>