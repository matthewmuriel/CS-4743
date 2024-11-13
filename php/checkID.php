<?php
include("functions.php");

try{
    $loanIds = fetchLoanIDsFromDatabase();
    echo"<pre>";
    
    foreach($loanIds as $loanId){
        echo "".$loanId['loan_id']."/n";
    }

    echo "</pre>";
} catch (Exception $e) {
    clearSID();
    echo "". $e->getMessage() ."";
    logError(
        $e->getMessage(),
        "checkID.php",
        "Check ID",
        "ERROR"
    );
}

?>