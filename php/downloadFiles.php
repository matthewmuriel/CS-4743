<?php
include ("functions.php");
date_default_timezone_set('America/Chicago');
$terminateMinute = 50;

try{
    $time_start = date("Y-m-d H:i:s");
    $begin = microtime(true);

    logMessage("Starting downloadFiles.php", "INFO");
    $pendingFiles = getFileIds("Pending");
    $sid = createSession();

    if(count($pendingFiles) > 0){
        logMessage(count($pendingFiles) . " clear pending files.","INFO");
        $count = 0;
        foreach($pendingFiles as $fileName){
            $currentMinute = (int)date('i');
            if ($currentMinute >= $terminateMinute) {
                logMessage($count . " files downloaded.",'INFO');
                break;
            }

            try{
                $data = getFileData($fileName, $sid);
                file_put_contents("/var/private_files/files/temp/".$fileName, $data);
                $fileSize=filesize("/var/private_files/files/temp/".$fileName);
                updateFileStatus($fileName, "Downloaded");
                $count++;
            }catch(Exception $e){
                logMessage("Error downloading file: " . $fileName, "ERROR");
            }
        }

    }else{
        logMessage("No Pending Documents Found", "INFO");
    }

    clearSID();
    $time_end = date("Y-m-d H:i:s");
    $end = microtime(true);
    $execution_time = ($end - $begin) / 60;
    logCron("downloadFiles.php", $time_start, $time_end, $execution_time, "SUCCESS");
    logMessage("Ending downloadFiles.php", "INFO\n");
} catch (Exception $e) {
    clearSID();
    echo "". $e->getMessage() ."";
    logError(
        $e->getMessage(),
        "downloadFiles.php",
        "Create Session",
        "ERROR"
    );

    logMessage("Ending downloadFiles.php", "ERROR\n");
}

?>