<?php
include ("functions.php");
date_default_timezone_set('America/Chicago');
try {
    logMessage("Starting auditIds.php", "INFO");
    $sid = createSession();
    $apiLoanIDs = getAllLoanIds($sid);
    $dbLoanIDs = fetchLoanIDsFromDatabase();

    

    $missingLoanIDs = array_diff($apiLoanIDs, $dbLoanIDs);
    if (count($missingLoanIDs) > 0) {
        logMessage(count($missingLoanIDs) . " missing loan IDs found", "INFO");
        
        foreach ($missingLoanIDs as $loanID) {
            try {
                insertLoanID($loanID);
            } catch(Exception $insertException){
                logError(
                    $insertException->getMessage(),
                    "auditIds.php",
                    "Insert Loan ID",
                    "ERROR"
                );
            }
        }

    }else{
        logMessage("No missing loan IDs found", "INFO");
    }

    clearSID();
    logMessage("Ending auditIds.php", "INFO\n");
} catch (Exception $e) {
    echo "". $e->getMessage() ."";
    logError(
        $e->getMessage(),
        "auditIds.php",
        "Create Session",
        "ERROR"
    );
    logMessage("Ending auditIds.php", "ERROR");
}
?>