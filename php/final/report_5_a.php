<?php
include("../functions.php");

$pdo = db_connect('doc_storage');

//Retrieve loan numbers along with the names of missing documents
//Uses the query from report 1 to get the list of valid loan numbers
//Cross joins to match every loan from the fl list with every document from the Document table
//This ensures that for each laon, all possible document types are considered.
//We then use a left join to check which document types are missing for each laon.
$query = "SELECT l.loan_number, GROUP_CONCAT(d.document_name) AS missing_documents
FROM (
    SELECT DISTINCT l.loan_number, l.loan_id
    FROM Loan l
    JOIN Loan_Document ld 
    ON l.loan_id = ld.loan_id 
    WHERE ld.submission_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
) fl
CROSS JOIN Document d
LEFT JOIN Loan l 
ON l.loan_id = fl.loan_id
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