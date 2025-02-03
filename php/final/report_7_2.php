<?php
include("../functions.php");
$pdo = db_connect('system_logs');

$query = "SELECT *
FROM performance_logs
WHERE execution_time > 0.0833;
";

$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll();

echo "<pre>";
echo "Total Long Execution Times: ".count($results);
echo "</pre>";

echo "<table border='1'>
            <tr>
                <th>Date/Time</th>
                <th>Execution Time</th>
            </tr>";
    foreach ($results as $row) {
        echo "<tr>
                <td>{$row['timestamp']}</td>
                <td>{$row['execution_time']}</td>
              </tr>";
    }
echo "</table>";

?>