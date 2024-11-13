<?php
include ("functions.php");

try {
    // $sid = createSession();
    $payLoad = fetchPendingDocuments();
    
    // clearSID();

    echo "<pre>";
    print_r($payLoad);
    echo "</pre>";

} catch (Exception $e) {
    echo "". $e->getMessage() ."";
    logError(
        $e->getMessage(),
        "createSID.php",
        "requestAllDocuments",
        "ERROR"
    );
}
?>