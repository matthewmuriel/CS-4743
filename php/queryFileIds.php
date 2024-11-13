<?php
include("functions.php");
date_default_timezone_set('America/Chicago');
try {
    $time_start = date("Y-m-d H:i:s");
    $begin = microtime(true);


    logMessage("Starting queryFileIds.php", "INFO");
    $sid = createSession();
    $fileNames = queryFiles($sid);

    if ($fileNames == null) {
        logMessage("No files found", "INFO");
    }else{
        foreach($fileNames as $fileName) {
            try {
                $fileParts = breakDownFileName($fileName);
                insertImportData($fileName,$fileParts);
            }catch(Exception $insertException){
                logError(
                    $insertException->getMessage(),
                    "queryFileIds.php",
                    "Insert File",
                    "ERROR");
            }
    }
}

    clearSID();
    $time_end = date("Y-m-d H:i:s");
    $end = microtime(true);
    $execution_time = ($end - $begin) / 60;
    logCron("queryFileIds.php", $time_start, $time_end, $execution_time, "SUCCESS");
    logMessage("Ending queryFileIds.php", "INFO\n");
} catch (Exception $e) {
    $time_end = date("Y-m-d H:i:s");
    $end = microtime(true);
    $execution_time = $end - $time_start;

    logCron("queryFileIds.php", $time_start, $time_end, $execution_time, "ERROR");
    logMessage("Ending queryFileIds.php", "ERROR\n");
    echo "". $e->getMessage() ."";
    logError(
        $e->getMessage(),
        "createSID.php",
        "Create Session",
        "ERROR"
    );
}

?>