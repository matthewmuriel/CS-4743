<?php
include("../functions.php");

$pdo = db_connect('doc_storage');

//Calculates the total file size in bytes for all documents within the specified date range
$query = "SELECT SUM(file_size) AS total_file_size_in_bytes 
FROM Loan_Document 
WHERE submission_date >= '2024-11-01 00:00:00' AND submission_date <= '2024-11-20 23:59:59';";
$stmt = $pdo->prepare($query);
$stmt->execute();

$totalSize = $stmt->fetch();

//Calculates the average file siez for all documents within the date range.
$query = "SELECT AVG(file_size) AS average_file_size 
FROM Loan_Document 
WHERE submission_date >= '2024-11-01 00:00:00' AND submission_date <= '2024-11-20 23:59:59';";
$stmt = $pdo->prepare($query);
$stmt->execute();

$avgSize = $stmt->fetch();


echo '<pre>';
echo 'Size of all Documents in bytes: '.$totalSize[0]."\n";
echo 'Average size of documents in bytes: '. $avgSize[0] ;
echo '</pre>';

?>