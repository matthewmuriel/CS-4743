<?php
include ("functions.php");
date_default_timezone_set('America/Chicago');
try{
    $time_start = date("Y-m-d H:i:s");
    $begin = microtime(true);

    logMessage("Starting auditDocuments.php", "INFO");
    $sid = createSession();
    $apiDocuments = getAllDocuments($sid);
    $dbDocuments =  fetchAllDocuments();
    $totalDocuments = count($apiDocuments);
    echo "Total Documents: ". $totalDocuments ."";

    $missingDocuments = array_diff($apiDocuments, $dbDocuments);

    if(count($missingDocuments) > 0){
        logMessage(count($missingDocuments)." missing documents found", "INFO");

        foreach($missingDocuments as $document){
            try{
                $fileParts = breakDownFileName($document);
                insertImportData($document, $fileParts);
            }catch(Exception $insertException){
                logError(
                    $insertException->getMessage(),
                    "auditDocuments.php",
                    "Insert Document",
                    "ERROR"
                );
            }
        }

    }else{
        logMessage("No missing documents found", "INFO");
    }

    clearSID();
    $time_end = date("Y-m-d H:i:s");
    $end = microtime(true);
    $execution_time = ($end - $begin) / 60;
    logCron("queryDocuments.php", $time_start, $time_end, $execution_time, "SUCCESS");
    logMessage("Ending auditDocuments.php", "INFO\n");
}catch (Exception $e){
    echo "".$e->getMessage()."";
    logError(
        $e->getMessage(),
        "createSID.php",
        "Create Session",
        "ERROR"
    );
    logMessage("Ending auditDocuments.php", "ERROR\n");
}

?>