<?php

$domain = $argv[1];
if (isset($argv[2])) {
    $limit=$argv[2];
} else {
    $limit=500;
}

$db_hostname = "127.0.0.1";
$db_username = "root";
$db_password = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";

$db = mysqli_connect($db_hostname, $db_username, $db_password, "condense");
$item_select = "select `url`  from `".$domain."` order by total desc, fbshares desc LIMIT ".$limit;
$result_select = mysqli_query($db, $item_select);

$AaccessID=["mozscape-9424ce25e7","mozscape-6667868ce7"];
$AsecretKey=["8f08fa5bbfe3d771dce7c40f20f54b5c","550c93e8059b3458f0d73e10d3e35431"];

$ip = '127.0.0.1';
$port = '9051';
$auth = 'password';
$command = 'signal NEWNYM';
$fp = fsockopen($ip, $port, $error_number, $err_string, 10);
if (!$fp) {
    echo "ERROR: $error_number : $err_string";
}
fwrite($fp, "AUTHENTICATE \"".$auth."\"\n");
fwrite($fp, $command."\n");


$i=0;
$count=count($AaccessID);
$startT=time();
$url=[];
$batch=0;
while ($row = mysqli_fetch_assoc($result_select)) {
    if ($batch<10) {
        $url[$batch]= $row["url"];
        $batch++;
    } else {
        $accessID = $AaccessID[$i];
        $secretKey = $AsecretKey[$i];
        $i++;
        $batch=0;
        $pa=0; //If pa is not available,then dont insert in db. Hence used as flag.
        // Set your expires times for several minutes into the future.
        // An expires time excessively far in the future will not be honored by the Mozscape API.
        $expires = time() + 300;
        // Put each parameter on a new line.
        $stringToSign = $accessID."\n".$expires;

        $binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);

        $urlSafeSignature = urlencode(base64_encode($binarySignature));

        //cols has value for page authority
        $cols = "34359738368";

        $requestUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/?Cols=".$cols."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
        $encodedUrls = json_encode($url);

        $curl = curl_init($requestUrl);

            curl_setopt($curl, CURLOPT_URL, $requestUrl);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedUrls);
            curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:9050");
            curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
            curl_setopt($curl, CURLOPT_COOKIESESSION, 1);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

            curl_setopt_array($curl, $options);
        }
        $content = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($content, true);
        //print_r($json);

        try {
            foreach ($json as $key => $value) {
                if (!is_null($json)) {
                    if (array_key_exists('upa', $value)) {
                        echo "success";
                        echo $value['upa'];
                    } else {
                        print_r($json);
                        echo $accessID;
                        echo $secretKey;
                    }
                } else {
                    echo "json is null";
                }
                //echo $value['upa'];
                //echo $url[$key];

                $insertquery="UPDATE  `".$domain."`  SET mozPa='".$value['upa']."' WHERE url='".$url[$key]."'";
                //echo ("UPDATE  `".$domain."`  SET mozPa='".$pa."' WHERE url='".$url."'");
                $insert=mysqli_query($db, $insertquery);

                if (!$insert) {
                    echo "insert failed for pa - ",mysqli_error($db);
                }
            }

            if ($i>=$count) {
                echo $i."  ".$count;
                $i=0;
                $sleepT=11-(time()-$startT);
                echo "sleeping.........".$sleepT;
                if ($sleepT>0) {
                    sleep($sleepT);
                }
                $startT=time();
            }
        } catch (Exception $e1) {
            echo 'Caught exception: ',  $e1->getMessage(), "\n";
        }
    }

echo "cycle complete ";
