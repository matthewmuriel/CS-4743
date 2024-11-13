<?php
include("functions.php");

try {
    $cinfo = createSession();

    echo "<pre>";
    print_r($cinfo);
    echo "</pre>";
} catch (Exception $e) {
    echo "". $e->getMessage() ."";
    logError(
        $e->getMessage(),
        "createSID.php",
        "Create Session",
        "ERROR"
    );
}
?>