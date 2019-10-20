<?php
// 3 async with 10 threads and 10 runs in 33 seconds.  5 async with 10 threads and 10 runs in 53 seconds.


$rustart = getrusage();


//include "/home/agnelvishal/Desktop/localhost/news/github/backend/localDbDetails.php";

$time_start = microtime(true);


class AsyncOperation extends Thread {

  public $i;
  public function __construct(int $i) {
    $this->i=$i;
    $curl=array();
    $url=array();
    $mh = curl_multi_init();

    $a=$this->i*3;
    $b=$a+3;
  }

  public function run() {
    try{
      $db_hostname = "127.0.0.1";
      $db_username = "root";
      $db_password = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";

      $db = mysqli_connect($db_hostname, $db_username, $db_password,"condense");
      //$db=$this->db;


      if (mysqli_connect_errno())
      {
        printf("AV-Connect failed: %s\n", mysqli_connect_error());
        exit();
      }


      $item_select = "SELECT `url` FROM `skcript` ORDER BY fbCount,date LIMIT $a , $b";
      $result_select = mysqli_query($db, $item_select);
      $v=0;
      while($row = mysqli_fetch_assoc($result_select))
      {

        //$row= $this->row;
        $url[$v]= $row["url"];
        //echo $url.$b;
        //$url = "http://www.thehindu.com/features/magazine/keeping-the-thriller-alive/article7332623.ece";
        $api = "http://graph.facebook.com/?fields=id,share,og_object%7Blikes.summary(true).limit(0)%7D&id=";

        $request = $api . $url[$v];
        $curl[$v] = curl_init();

        curl_setopt($curl[$v], CURLOPT_URL, $request);
        curl_setopt($curl[$v], CURLOPT_PROXY, "127.0.0.1:9050");
        curl_setopt($curl[$v], CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($curl[$v], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl[$v], CURLOPT_VERBOSE, 0);
        curl_setopt($curl[$v],CURLOPT_COOKIESESSION,1);
        curl_setopt($curl[$v],CURLOPT_FRESH_CONNECT,1);

        curl_multi_add_handle($mh, $curl[$v]);
        $v++;
      }
      echo $b;
      $running = null;
      do {
        curl_multi_exec($mh, $running);
      } while($running > 0);
      echo $b."    ";

      $v=0;
      $fblog=-1;
      while(($v<$b-$a)&&$fblog!=-2)
      {
        $response = curl_multi_getcontent($curl[$v]);
        //var_dump($response);
        curl_multi_remove_handle($mh, $curl[$v]);

        $json = json_decode($response, true);

        $shares = -1;
        $likes = -1;
        $fblog=-1;
        if (!is_null($json))
        {
          if (array_key_exists('share_count',$json['share']))
          {
            $shares = $json['share']['share_count'];
          }


          if (array_key_exists('total_count', $json['og_object']['likes']['summary']))
          {
            $likes = $json['og_object']['likes']['summary']['total_count'];
          }

          if(array_key_exists('error', $json))
          {
            if($json['error']['code']==4||$json['error']['code']==32)
            {
              echo " limit reached ";
              $fblog=-2;
              //proxy. ip is changed here.

              //proxy details
              $ip = '127.0.0.1';
              $port = '9051';
              $auth = 'password';
              $command = 'signal NEWNYM';
              $fp = fsockopen($ip,$port,$error_number,$err_string,10);
              if(!$fp) { echo "ERROR: $error_number : $err_string"; }
              fwrite($fp,"AUTHENTICATE \"".$auth."\"\n");
              //  $received = fread($fp,512);
              // echo $received;
              fwrite($fp,$command."\n");
              //$received = fread($fp,512);
              //echo $received;
              //sleep(3500);
              //	sleep(5);
              $v++;
              continue;
            }
            else
            {
              echo "Some fb api error";
              print_r($json);
              $fblog-3;
            }
          }
        }
        else
        {
          echo "json is null";
          $fblog=-4;
        }

        if($likes>-1||$shares>-1)
        {
          $insertquery="UPDATE skcript SET likes='".$likes."',shares='".$shares."',fbCount=fbCount+1 WHERE url='".$url[$v]."'";
          $insert=mysqli_query($db, $insertquery);
          //echo "UPDATE skcript SET likes='".$likes."',shares='".$shares."',fbCount=fbCount+1 WHERE url='".$url[$v]."'";
          echo "success";
        }
        else
        {
          $insertquery="UPDATE skcript SET fbCount=fbCount+1 WHERE url='".$url[$v]."'";
          $insert=mysqli_query($db, $insertquery);
          echo "failure";
        }


        if (!$insert)
        {
          echo "insert failed for likes and shares - ",mysqli_error($db);
        }


        $v++;
      }

    }
    catch (Exception $e1)
    {
      echo 'Caught exception: ',  $e1->getMessage(), "\n";
    }
  }
}


$pool = new Pool(5);

$stack = array();

//Initiate Multiple Thread
for($i=0;$i<10;$i=$i+1)
{
  $stack[] = new AsyncOperation($i);
}
// Start The Threads
foreach ( $stack as $t ) {
  $pool->submit($t);
}
echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
while ($pool->collect());
echo "ccccccccccccccccccccccccccccccccccc";

$pool->shutdown();
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);

// Code ...

// Script end
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
?>
