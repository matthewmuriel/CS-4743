<?php
include("functions.php");

try{
    $loanId = 002346187;
    $document_type = "Personals";
    $highestloanNumber = getHighestDocumentVersion($loanId, $document_type);

    echo "<pre>";
    echo "$highestloanNumber";
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