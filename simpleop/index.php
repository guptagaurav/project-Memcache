<?php

require_once "dbconnect.php";
include "functions.php";

/* Including dbconnect.php to connect to the database. */

/* Including functions.php to carry out the functionality Like Adding an Item
Deleting an Item, Updating Item. */


/*
	Using Memcache as caching domain to reduce user's trip to Database to query the results 
	which were queried earlier.
*/


$memcache = new Memcache;
$memcache->connect('localhost', 11211) or die ("Could not connect");

																	/*  Checking the conditions if User has clicked on the button that adds */
if(isset($_POST['addm'])){											/*  new Item to the Database */
	$check = add_item($_POST['name'],$_POST['comments']);							
	if(!$check){
		echo "<script>"."window.alert('Already in Database')"."</script>";			/*  If the item is already present in the database. Alert command get fired */
	}																				/*  up specifying the fields are already present in the database */
	header('Location:index.php');		
}

if(isset($_POST['update'])){																		/*  Same for Updating an entry in database */
	$checkedit = update_item($_POST['editname'],$_POST['editcomments'],$_GET['id'],$memcache);	
	if(!$checkedit){
		echo "<script>"."window.alert('CANNOT PERFORM THIS ACTION')"."</script>";                   /*  If the item is already present in the database or Someone */
	}																								/*  deletes that item before updating it. The item wouldn't be updated. */
	header('Location:index.php');
}

if(isset($_POST['cancel'])){             /* Code to cancel the current operation */
	header('location:index.php');	
}

if(isset($_GET['delid'])){				 /* Code to delete an item and reset that memcache key that corresponded to that item */
	delete_item($_GET['delid'],$memcache);
	header('Location:index.php');
}

if(!isset($_GET['searchquery'])){        /* Code to search an item. It is not a dynamic Search since that would require repeated Ajax calls to Database*/
$query = " SELECT * from `items` ";
}
else
{
	$query = search_item($_GET['searchquery'],$memcache);
}


/*

HTML PART 

*/


echo<<<END
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<meta property="og:image" content="http://www.belconference.in/images/bel/preview.jpg" />
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/bootstrap.icon-large.css" rel="stylesheet" type="text/css" />
	<meta charset="utf-8">
	<title>Local-Project</title>
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
				$i = 1;

				$querynum = $query." ORDER BY `id` DESC LIMIT 1;";			/* Quering the database to get the ID of the last that is present */
				$resultnum = $db->query($querynum);
				
				$if(!$resultfetch= $resultnum->fetch_object()){
					$max = 0;												/* If search query resulted in no match Or The Database is empty. Then don't display*/

				}
				else $max = $resultfetch->id;
				
				$result = $db->query($query);												
				$trace = $result->fetch_row();
				$num = $trace[0];

				while($num<=$max){											/* Looping through the queries to fill memcache pool if It isn't set */
				
					$key1 = "key1".$num;
				
					if(!$memcache->get($key1) && !($memcache->get("del1".$num))){    /* When memcache for that Key is not set up OR not take the ID of the field */
					$querytrace = $query." WHERE `id` = $num";						 /* that has been deleted. */

					if(!$resultn = $db->query($querytrace)){						/* If the query got is invalid for that ID then jump to the loop starting */
						$num++;														/* with an incremented $num */
						continue;
					}

					if($resultn->num_rows==0){ 				/* Same if the resulted number rows returned is 0*/
						$num++; 
						continue;
					}
					else{									/* If it is a valid row then and Memcache is not set for that Key the set it */
					$trace = $resultn->fetch_row(); 	
					$memcache->set($key1,$trace, false , 500);
					$row = $memcache->get($key1);
				
					}
				
					}
					elseif($memcache->get("del1".$num)){	/* If an Item is deleted then delete its entry from Memcache pool as well.*/
						echo "eneter";
						$del1 = "del1".$num;
						$memcache->delete($del1);
						$memcache->delete($key1);
						$num++;
						continue;
					}
					else{
						$row = $memcache->get($key1);		/* If the memcache is Set for then query from it instead of going to Database */
					}

			

				if(isset($_GET['id']) && $row[0] == $_GET['id']){	/* If edit button is clicked set and ID as a GET Request */
				
			
echo<<<END

				<tr>
				<td width="5%">$i</td>
				<form action="index.php?id=$row[0]" method="POST">
				<td style="width: 111px"><input style="width: 80%" type="text" name="editname" value="$row[1]"></td>
				<td><input style="width: 100%" type="message" name="editcomments" value="$row[2]" ></td>
				<td><input type="submit" name="update" value="Update"></td>
				<td><input type="submit" name="cancel" value="Cancel"></td>

				</form>
				</tr>
END;
				}else{   /* Else go on carrying with Displaying the rest of the rows. */
				
				
echo<<<END
				<tr>
				<td width="5%" id="num">$i</td>
				<td>$row[1]</td>
				<td>$row[2]</td>
				<td><button type="submit" name="edit" onClick="window.location.href = 'index.php?id=$row[0]'" value="$num"> Edit</button></td>
				<td><button type="submit" name="delete" onClick="window.location.href = 'index.php?delid=$row[0]'"  value="$num"> Delete</button></td>
				</tr>
				
END;
}
				$num++;
				$i++;
			}

			$result->free();	/* It is advisable to free any value. This will free up some system resources */


			if(isset($_POST['add'])){      /* If a New Entry is to be created and Displayed on webpage. */
echo<<<END
				<tr>
				<td width="5%">$i</td>
				<form action="index.php" method="POST">
				<td style="width: 111px"><input id="name" style="width: 80%" type="text" name="name" ></td>
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

/*
	Some scripts to check to check whether the User is submitting a entry with a empty field.
*/


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