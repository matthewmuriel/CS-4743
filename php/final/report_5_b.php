<?php
include("../functions.php");

$pdo = db_connect('doc_storage');

//Returns the loan_numbers that are complete
//utilizes the query from report 1 to get the valid loans
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
GROUP BY fl.loan_number, fl.loan_id
HAVING COUNT(DISTINCT d.document_id) = 12
ORDER BY fl.loan_number;";

$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetch();

if($results == null){
    $completeLoans = 0;
}else{
    $completeLoans = count($results);
}

echo '<pre>';
echo 'Completed Loans: '.$completeLoans."\n";
if($completeLoans > 0){
    echo print_r($results);
}else{
    echo "There are no complete Loans";
}
echo '</pre>';
?>