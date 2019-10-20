<?php
// 40 in in 7 seconnds with 20 threads. 100 in in 12 seconnds with 20 threads. 300 in in 32 seconnds with 40 threads. 300 in in 70 seconnds with 20 threads
//$rustart = getrusage();
//$time_start = microtime(true);

class AsyncOperation extends Thread
{
  public $urlT;
  public $domain;
  public function __construct($urlA, $domain)
  {
    $this->urlT=$urlA;
    $this->domain=$domain;
  }

  public function run()
  {
    try {
      $url = $this->urlT;
      do {
        //echo $url."  ";
        //$url = "http://www.thehindu.com/features/magazine/keeping-the-thriller-alive/article7332623.ece";
        $api = "http://graph.facebook.com/?fields=id,share,og_object%7Blikes.summary(true).limit(0)%7D&id=";

        $request = $api . $url;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $request);
        curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:9050");
        curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_COOKIESESSION, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

        $response = curl_exec($curl);
        curl_close($curl);
        // get json as array
        $json = json_decode($response, true);

        $shares = 0;
        $likes = 0;
        $fblog = -1;
        if (!is_null($json)) {
          if (array_key_exists('share', $json)) {
            if (array_key_exists('share_count', $json['share'])) {
              $shares = $json['share']['share_count'];
            }
          }

          if (array_key_exists('og_object', $json)) {
            if (array_key_exists('likes', $json['og_object'])) {
              if (array_key_exists('summary', $json['og_object']['likes'])) {
                if (array_key_exists('total_count', $json['og_object']['likes']['summary'])) {
                  $likes = $json['og_object']['likes']['summary']['total_count'];
                }
              }
            }
          }
          if (array_key_exists('error', $json)) {
            if ($json['error']['code']==4||$json['error']['code']==32) {
              //echo " limit reached ";
              $fblog=-2;
              //proxy. ip is changed here.

              //proxy details
              $ip = '127.0.0.1';
              $port = '9051';
              $auth = 'password';
              $command = 'signal NEWNYM';
              $fp = fsockopen($ip, $port, $error_number, $err_string, 10);
              if (!$fp) {
                echo "ERROR: $error_number : $err_string";
              }
              fwrite($fp, "AUTHENTICATE \"".$auth."\"\n");
              //$received = fread($fp,512);
              //echo $received;
              fwrite($fp, $command."\n");
              //$received = fread($fp,512);
              //echo $received;
              sleep(5);
              continue;
            } else {
              echo "Some fb api error";
              print_r($json);
              $fblog=-3;
            }
          }
        } else {
          echo "json is null";
          $fblog=-4;
        }

        $db_hostname = "127.0.0.1:3307";
        $db_username = "root";
        $db_password = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";

        $db = mysqli_connect($db_hostname, $db_username, $db_password, "condense");

        if (mysqli_connect_errno()) {
          printf("AV-Connect failed: %s\n", mysqli_connect_error());
          exit();
        }

       
          $insertquery="UPDATE `".$this->domain."` SET fblikes='".$likes."',fbshares='".$shares."',fbCount=fbCount+1 WHERE url='".$url."'";
          $insert=mysqli_query($db, $insertquery);
          //echo "success";
        
        
        if (!$insert) {
          echo "insert failed for likes and shares - ",mysqli_error($db);
        }
      } while ($fblog==-2);
    } catch (Exception $e1) {
      echo 'Caught exception: ',  $e1->getMessage(), "\n";
    } finally {
      mysqli_close($db);
    }
  }
}
$domain = $argv[1];

$db_hostname = "127.0.0.1:3307";
$db_username = "root";
$db_password = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";

$db = mysqli_connect($db_hostname, $db_username, $db_password, "condense");

if (mysqli_connect_errno()) {
  printf("AV-Connect failed: %s\n", mysqli_connect_error());
  exit();
}
$item_select = "select `url`  from `".$domain."` where (fbcount = 0) OR (fbCount <10 AND (date between DATE_SUB(CURDATE(),INTERVAL 10 DAY) AND CURDATE()))";
$result_select = mysqli_query($db, $item_select);



$pool = new Pool(20);

$stack = array();

//Initiate Multiple Thread
while ($row = mysqli_fetch_assoc($result_select)) {
  $stack[] = new AsyncOperation($row["url"], $domain);
}
// Start The Threads
foreach ($stack as $t) {
  $pool->submit($t);
}
while ($pool->collect());


$pool->shutdown();

mysqli_free_result($result_select);
mysqli_close($db);

//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);


/*
function rutime($ru, $rus, $index) {
return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
-  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") .
" ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
" ms in system calls\n";
//mysqli_free_result($result);
//mysqli_close($db);
*/
