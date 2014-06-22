<?php

require_once "dbconnect.php";
include "functions.php";

if(isset($_POST['addm'])){
	$check = add_item($_POST['name'],$_POST['comments']);
	if(!$check){
		echo "<script>"."window.alert('Already in Database')"."</script>";
	}
}

if(isset($_POST['update'])){
	update_item($_POST['editname'],$_POST['editcomments'],$_GET['id']);
	header('Location:index.php');
}

if(isset($_POST['cancel'])){
	header('location:index.php');	
}

if(isset($_GET['delid'])){
	delete_item($_GET['delid']);
	header('Location:index.php');
}

if(!isset($_GET['searchquery'])){
$query = " SELECT * from `items` ";

if(!$result = $db->query($query)){
	die('There was an error running the query [' . $db->error . ']');
}
}
else
{
	$result = search_item($_GET['searchquery']);
}





echo<<<END
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<meta property="og:image" content="http://www.belconference.in/images/bel/preview.jpg" />
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/flexslider.css" rel="stylesheet" type="text/css" />
	<link href="css/prettyPhoto.css" rel="stylesheet" type="text/css" />
	<link href="css/animate.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/bootstrap.icon-large.css" rel="stylesheet" type="text/css" />
	<meta charset="utf-8">
	<title>Wingify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link href='http://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
</head>
<body>
<button onClick="window.location.href='index.php'" class="btn" style="margin-left:10%;margin-top:2%">HOME</button>
<form action="index.php" method="GET" style="margin-left:10%;margin-top:2%;margin-bottom:2%">
<input type="text" style="width:60%" name="searchquery" class="input-medium search-query" placeholder="Enter The search query"> 
<button type="submit" class="btn">Search</button>
</form>

<table class="table table-hover" border="0" style="margin-left:2%">
				<thead> 
					<tr>
						<th width="5%">Sno</th> 
						<th>Name</th> 
						<th>Comments</th> 	
					</tr>
				</thead> 
				<tbody>
END;
				$num = 1;
				while($row = $result->fetch_row()){


				if(isset($_GET['id']) && $row[0] == $_GET['id']){
				

echo<<<END

				<tr>
				<td width="5%">$num</td>
				<form action="index.php?id=$row[0]" method="POST">
				<td style="width: 111px"><input style="width: 50%" type="text" name="editname" value="$row[1]"></td>
				<td><input style="width: 100%" type="message" name="editcomments" value="$row[2]" ></td>
				<td><input type="submit" name="update" value="Update"></td>
				<td><input type="submit" name="cancel" value="Cancel"></td>

				</form>
				</tr>
END;
				$row = $result->fetch_row();
				}
				
				
echo<<<END
				<tr>
				<td width="5%" id="num">$num</td>
				<td>$row[1]</td>
				<td>$row[2]</td>
				<td><button type="submit" name="edit" onClick="window.location.href = 'index.php?id=$row[0]'" value="$num"> Edit</button></td>
				<td><button type="submit" name="delete" onClick="window.location.href = 'index.php?delid=$row[0]'"  value="$num"> Delete</button></td>
				</tr>
				
END;
				$num++;
			}

			$result->free();
			if(isset($_POST['add'])){
echo<<<END
				<tr>
				<td width="5%">$num</td>
				<form action="index.php" method="POST">
				<td style="width: 111px"><input id="name" style="width: 50%" type="text" name="name" ></td>
				<td><input id="comments" style="width: 100%" type="message" name="comments" ></td>
				<td><input type="submit" name="addm" value="Submit" onClick="return test()"></td>
				<td><input type="submit" name="cancel" value="Cancel"></td>
				</form>
				</tr>

END;
			unset($_POST['add']);
			$num++;	
			}

echo<<<END

			<form action="index.php" method="POST">
			<td><input type="submit" name="add" value="ADD"></td>
			</tbody>
</table>

<script>
function test(){
	if(document.getElementById("name").value.length==0){
	alert('Add a proper item name');
	return false;
}

if(document.getElementById("comments").value.length==0){
	alert('Comment Field Cannot be left blank');
	return false;
}
}

</script>
</body>
</html>

END;

?>