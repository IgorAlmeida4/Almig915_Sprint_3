<?php
global $pdo;
require 'db.php';

// Set the header to ensure JSON output
header('Content-Type: application/json');

// Get the node ID from the query string
$nodeId = $_GET['node_id'] ?? null;

// Handle missing or invalid node ID
if (!$nodeId || !is_numeric($nodeId)) {
    echo json_encode(["error" => "Valid node ID is required"]);
    exit;
}

try {
    // Prepare the SQL query to fetch measurement data for the given node ID
    $query = $pdo->prepare("
        SELECT soilMoisture, recordDateTime 
        FROM Measurement 
        WHERE fk_node_isRecorded = ? 
        ORDER BY recordDateTime
    ");
    $query->execute([$nodeId]);

    // Fetch the data
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode($data);
} catch (Exception $e) {
    // Handle any errors during database access
    echo json_encode(["error" => "An error occurred while fetching data: " . $e->getMessage()]);
}