<?php
$db_hostname = "articklycom.ipagemysql.com";
$db_database = "simpleop";
$db_password = "jerrytom";
$db_username = "jerrytom";

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database);
if($db->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
	}

?>