<?php
include ("functions.php");
logMessage("Starting moveFiles.php", "INFO");
try {

    $sanitized = getSanitizedFiles();

    foreach ($sanitized as $file) {
        try {
            $fileID = $file["file_id"];
            $loanNumber = $file["loan_number"];
            $documentType = $file["document_type"];
            $version = $file["version"];
            $created = $file["created_at"];
    
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
                logMessage("Moved $fileID into Database", "INFO");
            }
        }catch(Exception $e){
            logMessage("Failed to move $fileID into Database", "ERROR");
        }
    }
    logMessage("Ending moveFiles.php", "INFO");
} catch (Exception $e) {
    logMessage("Critical Failure", "ERROR");
    logMessage("Ending moveFiles.php", "INFO");
}

?>