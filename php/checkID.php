<?php
include("functions.php");

try{
    $file =getSanitizedFiles();

    $fileID = $file[0]["file_id"];
    $loanNumber = $file[0]["loan_number"];
    $documentType = $file[0]["document_type"];
    $version = $file[0]["version"];
    $created = $file[0]["created_at"];

    $targetDirectory = "/var/private_files/files/loans/$loanNumber/";
    $currentPath = "/var/private_files/files/temp/$fileID";

    if (!file_exists($targetDirectory)){
        mkdir($targetDirectory, 0777, true);
    }

    $finalFilePath = $targetDirectory . $fileID;

    if (!checkLoanID($loanNumber)){
        insertLoanID($loanNumber);
    }
    if (!checkDocumentName($documentType)){
        insertDocumentID($documentType);
    }

    $loanID = fetchLoanID($loanNumber);
    $documentID = fetchDocumentID($documentType);

    if (rename($currentPath, $finalFilePath)){
        insertDocumentData($loanID, $documentID, $fileID, $created, $finalFilePath, $version, "Cron");
        updateFileStatus($fileID, "Inserted");
        echo "Moved File";
    } else {
        echo "Failed to Move File";
    }
    

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