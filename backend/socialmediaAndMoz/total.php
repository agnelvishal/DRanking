<?php

   
    // Database details
    $d = "127.0.0.1";
    $u = "root";
    $p = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";
    $db = mysqli_connect($d, $u, $p, "condense");
    if (mysqli_connect_errno($db)) {
        echo "AV:Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $domain = $argv[1];

    $item_select_moz = "SELECT min(mozPa),max(fbshares) FROM `".$domain."`";
    $result_select_moz = mysqli_query($db, $item_select_moz);
    $row = mysqli_fetch_assoc($result_select_moz);

    $minMoz = intval($row["min(mozPa)"]);
    $maxfbshares = intval($row["max(fbshares)"]);
    if ($maxfbshares > 1000) {
        $mulForMoz = $maxfbshares /200;
    }
    else{
        $mulForMoz = $maxfbshares /15;
    }
    $item_select = "SELECT mozPa,fblikes,fbshares,url,reddit,pinterest FROM `".$domain."` ORDER BY fbshares desc";
    $result_select = mysqli_query($db, $item_select);
    while ($row = mysqli_fetch_assoc($result_select)) {
        try {
          $total=0;
            $url=$row["url"];
            $likes= $row["fblikes"];
            $shares= $row["fbshares"];
            $pa= $row["mozPa"];
            $reddit= $row["reddit"];
            $pinterest= $row["pinterest"];
            $normMoz = 0;

            //Approx 50% for shares,10% for likes and 40% for pa.
            // Note that I feel log should ve avoided for likes and shares.
            if ($shares>0) {
                $total+=$shares;
            }
            if ($likes>0) {
                $total+=$likes;
            }
            if ($pa>0) {
                $normMoz = ($pa - $minMoz + 1) * $mulForMoz; 
                $total+= $normMoz;
            }
            if ($reddit>0) {
              $total+=$reddit;
          }
          if ($pinterest>0) {
            $total+=$pinterest;
        }
            $total=ceil($total);
            //echo $total;
      
            $insertquery="UPDATE `".$domain."` SET total='".$total."',normMoz='".$normMoz."' WHERE url='".$url."'";
            $insert=mysqli_query($db, $insertquery);
            if (!$insert) {
                echo "insert failed - ",mysqli_error($db);
            }
        } catch (Exception $e1) {
            echo 'Caught exception: ',  $e1->getMessage(), "\n";
        }
    }

    mysqli_free_result($result_select);
    mysqli_close($db);
    