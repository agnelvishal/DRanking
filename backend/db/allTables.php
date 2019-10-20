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

 try{
    $item_select_tables = "show tables";
 
    $result_select_tables = mysqli_query($db, $item_select_tables);


    while ($table_rows = mysqli_fetch_assoc($result_select_tables)) {
        $domain = $table_rows["Tables_in_condense"];
        $item_select = "alter table `".$domain."` add column keywords varchar(512)";
        $result_select = mysqli_query($db, $item_select);
       // $item_select = "UPDATE `".$domain."` SET isArticleData='1'";
        //$result_select = mysqli_query($db, $item_select);

if(!$result_select){
    echo mysqli_error($db);
}
        // $item_select = "SELECT mozPa,fblikes,fbshares,url,reddit,pinterest FROM `".$domain."` ORDER BY fbshares desc";
        // $result_select = mysqli_query($db, $item_select);

        // while ($row = mysqli_fetch_assoc($result_select)) {
        //     try {
        //         $insertquery="UPDATE `".$domain."` SET total='".$total."',normMoz='".$normMoz."' WHERE url='".$url."'";
        //         $insert=mysqli_query($db, $insertquery);
        //         if (!$insert) {
        //             echo "insert failed - ",mysqli_error($db);
        //         }
        //     } catch (Exception $e1) {
        //         echo 'Caught exception: ',  $e1->getMessage(), "\n";
        //     }
        // }

       // mysqli_free_result($result_select);
    }
 }
 catch (Exception $e1) {
    echo 'Caught exception: ',  $e1->getMessage(), "\n";
 }


    mysqli_close($db);
