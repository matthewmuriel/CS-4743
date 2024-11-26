<?php
include("../functions.php");

$pdo = db_connect('doc_storage');
$query = "SELECT d.document_name, ld.document_id, COUNT(*) AS total_count
FROM Loan_Document ld
JOIN Document d ON ld.document_id = d.document_id
WHERE ld.submission_date >= '2024-11-01 00:00:00' 
  AND ld.submission_date <= '2024-11-20 23:59:59'
GROUP BY ld.document_id, d.document_name
ORDER BY total_count DESC;
";
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll();

echo "<table border='1'>
            <tr>
                <th>Document Name</th>
                <th>Total Count</th>
            </tr>";
    foreach ($results as $row) {
        echo "<tr>
                <td>{$row['document_name']}</td>
                <td>{$row['total_count']}</td>
              </tr>";
    }
echo "</table>";
?>