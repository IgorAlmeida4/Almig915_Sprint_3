<?php
require 'db.php';

$file = 'C:\xampp\htdocs\Almig915_PIF\plantimeter_data.sql';

try {
    $query = file_get_contents($file);
    $pdo->exec($query);
    echo "Data imported successfully.";
} catch (PDOException $e) {
    echo "Error importing data: " . $e->getMessage();
}
?>