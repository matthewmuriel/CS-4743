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
        
        foreach($payLoad as $key=>$value)
        {

            $data="sid=$sid&uid=$username&fid=$value";
            $ch=curl_init('https://cs4743.professorvaladez.com/api/request_file');
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
            
            if(strstr($result, "Status"))
            {
                echo '<h2>There was an error with file:$value</h2>';
                echo '<pre>';
                print_r($result);
                echo '</pre>';
                continue;
            }
            else
            {
                $content=$result;

                if (strlen($content) == 0)
                {
                    echo "<h2>File $value received zero length</h2>";
                    continue;
                }

                $fp=fopen("/var/www/files/$value","wb");
                fwrite($fp,$content);
                fclose($fp);
                echo "<h3>File $value written to filesystem</h3>";
            }

        }

        $data="sid=$sid";
        $ch=curl_init('https://cs4743.professorvaladez.com/api/close_session');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array (
            'content-type: application/x-www-form-urlencoded',
            'content-length: '. strlen($data)));
        curl_exec($ch);
        echo "<h3>Session closed. Done</h3>";
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