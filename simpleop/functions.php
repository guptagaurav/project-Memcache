<?php

function add_item($name, $comments){
	require 'dbconnect.php';

	$name = $db->escape_string($name);
	$comments = $db->escape_string($comments);

	$querycheck = "SELECT * FROM `items` WHERE `name`='$name' and `comments`='$comments'";
	$resultcheck = $db->query($querycheck);
	$counter = $resultcheck->num_rows;
	if($counter>0){
		return false;
	}

	$queryadd= "INSERT INTO `items` (`name`,`comments`) VALUES ('$name','$comments') ";

	if(!$resultadd = $db->query($queryadd)){
	die('There was an error running the query [' . $db->error . ']');
	}
	// $resultadd->free();
	return true;
}


function update_item($name_update, $comments_update,$id,$memcahce){
	require 'dbconnect.php';

	$name_update = $db->escape_string($name_update);
	$comments_update = $db->escape_string($comments_update);

	$queryfirstcheck = "SELECT `id` FROM `items` WHERE `id`=$id";
	if(!$resultfirstcheck=$db->query($queryfirstcheck)){
		return false;
	}

	$querycheck = "SELECT * FROM `items` WHERE `name`='$name_update' and `comments`='$comments_update'";
	$resultcheck = $db->query($querycheck);
	$counter = $resultcheck->num_rows;
	if($counter>0){
		return false;
	}

	$queryupdate = "UPDATE `items` SET `name` = '$name_update' , `comments`='$comments_update' WHERE `id` = $id ";

	if(!$resultupdate = $db->query($queryupdate)){
	die('There was an error running the query [' . $db->error . ']');
	}
	$key1 = "key1".$id;
	$memcahce->delete($key1);
	// else $resultupdate->free();
	return true;


}

function delete_item($del_id,$memcahce){
	require 'dbconnect.php';

	$querydel = "DELETE FROM `items` where `id` = $del_id" ;
	if(!$resultdel= $db->query($querydel)){
	die('There was an error running the query [' . $db->error . ']');
	}

	$query1 = "SELECT `id` from `items`";
	
	if(!$result1 = $db->query($query1)){
	die('There was an error running the query [' . $db->error . ']');
	}
	$counter = $result1->num_rows;
	$t=0;
	$arr = array();
	$net = $del_id+1;
	echo $net;
	echo $counter; 
	for($i=$net; $i<=$counter;$i++){
		// $arr = array("$i");
		array_push($arr, $i-1);
		// $t++;
	}
	$del1 = "del1".$del_id;
	$memcahce->set($del1,1,false,500);
	
	echo "". implode(",",$arr)."'<br>'";

	// $query2 = 'UPDATE `items` WHERE `id`=$net SET `id`= '.implode(",",$arr);
	// if(!$result2 = $db->query($query2)){
	// 	die('There was an error running the query [' . $db->error . ']');
	// }

}



function search_item($string,$memcache){
	require 'dbconnect.php';
	$string = $db->escape_string($string);

	$query = "SELECT * FROM `items` WHERE `name` LIKE '%$string%' OR `comments` LIKE '%$string%'";

	if(!$result = $db->query($query)){
		die('There was an error running the query [' . $db->error . ']');
	}

	$memcache->set("search",1,false,500);

	return $query;

}

?>