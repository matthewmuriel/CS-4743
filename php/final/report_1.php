<?php
include("../functions.php");

$pdo = db_connect('doc_storage');

//Selects distinct loan numbers from the loan table (l)
//Then joins the Loan table to the Loan_Document table to match loans with their associated documents
//We then filter based on the Loan_Document table for the date range.
//While we have 1507 loans in the Loan table, only 1427 loans met the window criteria
$query = "SELECT DISTINCT l.loan_number 
FROM Loan l 
JOIN Loan_Document ld ON l.loan_id = ld.loan_id 
WHERE ld.submission_date >= '2024-11-01 00:00:00' AND ld.submission_date <= '2024-11-20 23:59:59';";
$stmt = $pdo->prepare($query);
$stmt->execute();

$files = $stmt->fetchAll(PDO::FETCH_COLUMN);
$LoanCount = count($files);

echo '<pre>';
echo 'Number of Loans: '. $LoanCount."\n";
echo print_r($files);
echo '</pre>';

?>