<?php
// Database details
$db_hostname = "127.0.0.1";
$db_username = "root";
$db_password = "8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db";


$db = mysqli_connect($db_hostname, $db_username, $db_password,"condense");


if (mysqli_connect_errno())
{
	printf("AV-Connect failed: %s\n", mysqli_connect_error());
	exit();
}


?>
