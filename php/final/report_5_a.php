<?php
include("../functions.php");

$pdo = db_connect('doc_storage');
$query = "SELECT l.loan_number, GROUP_CONCAT(d.document_name) AS missing_documents
FROM Loan l
CROSS JOIN Document d
LEFT JOIN Loan_Document ld 
ON l.loan_id = ld.loan_id 
AND d.document_id = ld.document_id 
AND ld.submission_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
WHERE ld.document_id IS NULL
GROUP BY l.loan_number
HAVING COUNT(d.document_id) > 0 
ORDER BY l.loan_number;";

$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll();

$rowCount = count($results);

echo '<pre>';
echo 'Number of Incomplete Loans: '.$rowCount."\n";
echo '</pre>';

echo "<table border='1'>
        <tr>
            <th>Loan Number</th>
            <th>Missing Documents</th>
        </tr>";

        foreach ($results as $row){
            echo "<tr>
                    <td>{$row['loan_number']}</td>     
                    <td>{$row['missing_documents']}</td>
            </tr>";
        }

echo "</table>";

?>