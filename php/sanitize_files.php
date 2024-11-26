<?php
include ("functions.php");
logMessage("Starting sanitize_files.php", "INFO");

$sourcePath = "/var/private_files/files/temp/";
$fileID = "748216930-Financial-20241021_17_52_02.pdf";
$filePath = $sourcePath.$fileID;

$downloaded = getFileIds("Downloaded");

logMessage("Found ".count($downloaded)." Downloaded Files", "INFO");

foreach ($downloaded as $fileID){
    $filePath = $sourcePath.$fileID;

    if (sanitizeFile($filePath)){
        updateFileStatus($fileID, "Sanitized");
    }else{
        updateFileStatus($fileID, "Quarantined");
    }

}



logMessage("Ending sanitize_files.php", "INFOs");
?>