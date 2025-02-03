<?php
$username="bxj214";
$password="94JFRTwjfn37mk6t";
$data="username=$username&password=$password";
$ch=curl_init('https://cs4743.professorvaladez.com/api/create_session');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array (
    'content-type: application/x-www-form-urlencoded',
    'content-length: '. strlen($data)));
$time_start=microtime(true);
$result=curl_exec($ch);
$time_end=microtime(true);
$execution_time=($time_end-$time_start)/60;
curl_close($ch);

$cinfo=json_decode($result,true);

if ($cinfo[0] == "Status: OK")
{
    $sid=$cinfo[2];//index 2 holds the session ID
    $data="uid=$username&sid=$sid";

    $ch=curl_init('https://cs4743.professorvaladez.com/api/query_files');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array (
        'content-type: application/x-www-form-urlencoded',
        'content-length: '. strlen($data)));
    $time_start=microtime(true);
    $result=curl_exec($ch);
    $time_end=microtime(true);
    $execuTime=($time_end-$time_start)/60;
    curl_close($ch);

    $cinfo=json_decode($result,true);

    if ($cinfo[0] == "Status: OK")
    {
        echo "<pre>";
        print_r($cinfo);
        echo "</pre>";
    
        $tmp=explode(":",$cinfo[1]);
        $payLoad=json_decode($tmp[1]);
        echo "<pre>";
        print_r($payLoad);
        echo "</pre>";
    }
    else 
    {
        echo "<pre>";
        print_r($cinfo);
        echo "</pre>";   
    }

}else
{
    echo "<pre>";
    print_r($cinfo);
    echo "</pre>";
}
?>