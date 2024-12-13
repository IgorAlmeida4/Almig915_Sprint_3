<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Interface - Igor Almeida</title>
    <link rel="stylesheet" href="nodes.css">
    <script src="https://cdn.jsdelivr.net/npm/luxon@2.0.2/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>aaa

<body>
<h1>Plant Node Dashboard</h1>

<?php
require 'db.php';
global $pdo;

try {
    $query = $pdo->query("
            SELECT 
                n.pk_node,
                n.name,
                m.soilMoisture,
                m.lampBrightness,
                MAX(m.recordDateTime) AS last_updated
            FROM 
                Node n
            LEFT JOIN 
                Measurement m
            ON 
                n.pk_node = m.fk_node_isRecorded
            GROUP BY 
                n.pk_node;
        ");
    $nodes = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching nodes and measurements: " . $e->getMessage());
}
?>

<div id="node-view-container">
    <?php if (empty($nodes)): ?>
        <p>No nodes available.</p>
    <?php else: ?>
        <?php foreach ($nodes as $node): ?>
            <div class="node-card">
                <h2>Node: <?= htmlspecialchars($node['name'] ?? 'Unnamed') ?></h2>
                <p>Soil Moisture: <?= htmlspecialchars($node['soilMoisture'] ?? 'N/A') ?>%</p>
                <p>Lamp Brightness: <?= htmlspecialchars($node['lampBrightness'] ?? 'N/A') ?>%</p>
                <p>Last Updated: <?= htmlspecialchars($node['last_updated'] ?? 'No Data') ?></p>

                <label for="brightness_<?= $node['pk_node'] ?>">Set Grow Light Brightness (0-100):</label>
                <input type="range" id="brightness_<?= $node['pk_node'] ?>" min="0" max="100"
                       value="<?= htmlspecialchars($node['lampBrightness'] ?? 0) ?>" step="1">
                <span
                        id="brightness_value_<?= $node['pk_node'] ?>"><?= htmlspecialchars($node['lampBrightness'] ?? 0) ?>%</span>

                <label for="water_<?= $node['pk_node'] ?>">Amount of Water (0-100 ml):</label>
                <input type="number" id="water_<?= $node['pk_node'] ?>" min="0" max="100" value="100" step="1">

                <button onclick="setControlValues(<?= $node['pk_node'] ?>)">Set Values</button>
                <canvas id="chart_<?= $node['pk_node'] ?>" width="400" height="200"></canvas>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function setControlValues(nodeId) {
        console.log("Setting values for Node ID:", nodeId);
        // Add the logic for updating node values
    }

    document.querySelectorAll('canvas').forEach(canvas => {
        const nodeId = canvas.id.split('_')[1];

        fetch(`measurementData.php?node_id=${nodeId}`)
            .then(response => response.json())
            .then(data => {
                if (data && Array.isArray(data) && data.length > 0) {
                    const ctx = canvas.getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(d => luxon.DateTime.fromISO(d.recordDateTime).toFormat('HH:mm:ss')),
                            datasets: [{
                                label: 'Soil Moisture',
                                data: data.map(d => d.soilMoisture),
                                borderColor: '#00aaff',
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'category',
                                    labels: data.map(d => luxon.DateTime.fromISO(d.recordDateTime).toFormat('HH:mm')),
                                },
                                y: {
                                    beginAtZero: true,
                                    min: 0,
                                    max: 100
                                }
                            }
                        }
                    });
                } else {
                    console.error("Invalid or empty data for Node ID:", nodeId);
                }
            })
            .catch(error => console.error('Error fetching data for Node ID:', nodeId, error));
    });
</script>
</body>

</html>