<?php
$db_hostname = "localhost";
$db_database = "simpleop";
$db_password = "";
$db_username = "root";

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database);
if($db->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
	}

?>