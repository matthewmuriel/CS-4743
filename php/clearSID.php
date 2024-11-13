<?php
include("functions.php");

// $username="bxj214";
// $password="94JFRTwjfn37mk6t";
// $data="username=$username&password=$password";
// $ch=curl_init('https://cs4743.professorvaladez.com/api/clear_session');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array (
//     'content-type: application/x-www-form-urlencoded',
//     'content-length: '. strlen($data)));
// $time_start=microtime(true);
// $result=curl_exec($ch);
// $time_end=microtime(true);
// $execution_time=($time_end-$time_start)/60;
// curl_close($ch);

// $cinfo=json_decode($result,true);
// echo "<pre>";
// print_r($cinfo);
// echo "</pre>";

$cinfo = clearSID();

if ($cinfo['status'] == 'error') {
    echo "<pre>";
    echo "Failed to clear session: $cinfo[1]";
    print_r($cinfo);
    echo "</pre>";
    logError(
        $cinfo['message'],
        "clearSID.php",
        "Clear Session",
        "ERROR"
    );
    return;
}else{
    echo "<pre>";
    print_r($cinfo);
    echo "</pre>";
}

?>