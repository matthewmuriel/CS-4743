<?php
include("../functions.php");

$pdo = db_connect('doc_storage');

$query = "SELECT AVG(file_size) AS average_file_size 
FROM Loan_Document 
WHERE submission_date >= '2024-11-01 00:00:00' 
AND submission_date <= '2024-11-20 23:59:59';";
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetch();
$globalAvgSize = $results[0];

$query = "SELECT AVG(document_count) AS average_documents_per_loan
FROM (
    SELECT loan_id, COUNT(*) AS document_count
    FROM Loan_Document
    WHERE submission_date >= '2024-11-01 00:00:00' AND submission_date <= '2024-11-20 23:59:59'
    GROUP BY loan_id
) AS loan_document_counts;";
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetch();
$globalAvgFiles = $results[0];

$query = "SELECT l.loan_id, l.loan_number, COUNT(ld.document_id) AS total_documents,
AVG(ld.file_size) AS average_file_size
FROM Loan l
LEFT JOIN Loan_Document ld ON l.loan_id = ld.loan_id
WHERE ld.submission_date >= '2024-11-01 00:00:00' 
AND ld.submission_date <= '2024-11-20 23:59:59'
GROUP BY l.loan_id, l.loan_number
ORDER BY l.loan_id;";

$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll();

echo "<table border='1'>
        <tr>
            <th>Loan Number</th>
            <th>Number of Documents Received</th>
            <th>Average Size of Documents</th>
            <th>Above/Below Global Average File Size</th>
            <th>Above/Below Global Average File Amount</th>
        </tr>";
    
    foreach ($results as $row){
        if($row['average_file_size'] > $globalAvgSize){
            $fileSizes = "Above";
        }else{
            $fileSizes = "Below";
        }

        if($row['total_documents'] > $globalAvgFiles){
            $fileAmount = "Above";
        }else{
            $fileAmount = "Below";
        }

        echo "<tr>
                <td>{$row['loan_number']}</td>
                <td>{$row['total_documents']}</td>
                <td>{$row['average_file_size']}</td>
                <td>{$fileSizes}</td>
                <td>{$fileAmount}</td>
            </tr>";
    }

echo "</table>";
?>