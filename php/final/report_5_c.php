<?php
include("../functions.php");

//does the same thing that report_5_b does, but instead looks for loans that don't have any documents
$pdo = db_connect('doc_storage');
$query = "SELECT fl.loan_number
FROM (
    SELECT DISTINCT l.loan_number, l.loan_id
    FROM Loan l
    JOIN Loan_Document ld 
    ON l.loan_id = ld.loan_id 
    WHERE ld.submission_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
) fl
JOIN Loan_Document ld 
ON fl.loan_id = ld.loan_id
JOIN Document d 
ON ld.document_id = d.document_id
WHERE ld.submission_date BETWEEN '2024-11-01 00:00:00' AND '2024-11-20 23:59:59'
GROUP BY fl.loan_id, fl.loan_number
HAVING COUNT(DISTINCT d.document_id) = (SELECT COUNT(*) FROM Document)
ORDER BY fl.loan_number;";
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetch();

if($results == null){
    $missingLoanCount = 0;
}else{
    $missingLoanCount = count($results);
}

echo '<pre>';
echo 'Loans with No Documents: '.$missingLoanCount."\n";
if($missingLoanCount > 0){
    echo print_r($results);
}else{
    echo "All Loans have at least 1 Document";
}
echo '</pre>';
?>