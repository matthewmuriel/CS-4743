<?php
include ("functions.php");

try {
    $sid = createSession();

    $payLoad = getAllLoanIds($sid);
    clearSID();

    //print out the loan ids from the payload
    echo "<pre>";
    echo print_r($payLoad);
    echo "</pre>";

} catch (Exception $e) {
    echo "" . $e->getMessage() . "";
    logError(
        $e->getMessage(),
        "createSID.php",
        "requestAllLoanID",
        "ERROR"
    );
}

?>