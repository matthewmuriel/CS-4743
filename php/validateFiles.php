<?php
    include("functions.php");
    $sid = createSession();

    $pdo = db_connect('doc_storage');
    $query = "SELECT 
    ld.loan_id,
    l.loan_number,
    COUNT(ld.document_id) AS document_count
FROM 
    Loan_Document ld
JOIN 
    Loan l ON ld.loan_id = l.loan_id
GROUP BY 
    ld.loan_id, l.loan_number
ORDER BY 
    document_count DESC;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $results = $stmt->fetchAll();

    foreach($results as $row){
        $loanNumber = $row['loan_number'];
        $count = $row['document_count'];
        $files = validateFiles($sid, $loanNumber);

        $apiCount = count($files);

        if ($count != $apiCount){
            echo $loanNumber;
        }

    }

?>