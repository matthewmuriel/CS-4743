<?php
include("../functions.php");

$pdo = db_connect('doc_storage');
$query = "SELECT COUNT(file_name) AS file_count FROM Loan_Document WHERE submission_date >= '2024-11-01 00:00:00' AND submission_date <= '2024-11-20 23:59:59';";
$stmt = $pdo->prepare($query);
$stmt->execute();

$totalFiles = $stmt->fetch();

$pdo = db_connect('doc_storage');
$query = "SELECT AVG(document_count) AS average_documents_per_loan
FROM (
    SELECT loan_id, COUNT(*) AS document_count
    FROM Loan_Document
    GROUP BY loan_id
) AS loan_document_counts;";
$stmt = $pdo->prepare($query);
$stmt->execute();

$avgFiles = $stmt->fetch();

echo '<pre>';
echo 'Total files: '.$totalFiles[0]."\n";
echo 'Average Files Submitted Per Loan: '.$avgFiles[0];
echo '</pre>';

?>