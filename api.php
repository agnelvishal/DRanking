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

try {
    $item_select_all = "select * from `techcrunch.com` limit 100";

    $result_select_all = mysqli_query($db, $item_select_all);

    try {
        $rows = array();
        while ($r = mysqli_fetch_assoc($result_select_all)) {
            $rows[] = $r;
        }
        print json_encode($rows);
    } catch (Exception $e1) {
        echo 'Caught exception: ',  $e1->getMessage(), "\n";
    }
} catch (Exception $e1) {
    echo 'Caught exception: ',  $e1->getMessage(), "\n";
}


mysqli_close($db);
