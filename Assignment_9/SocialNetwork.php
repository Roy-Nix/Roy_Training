<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Social_Network";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$currentUser = null;
$post = null;
$currentid = null;

//Setting the current user
if(isset($_GET['name'])){
	$currentUser =$_GET['name'];	
}

//Initializing the user during first reload
if($currentUser == null){
	$sql="SELECT Name FROM tUser Where User_id = 1";
	$user = $conn->query($sql);
	while($row = $user->fetch_assoc()) {
		$currentUser = $row['Name'];
	}										
}


//Fetching current user's ID
$sqlIdQuery = "SELECT User_id FROM tUser where Name = '$currentUser'";
$id = $conn->query($sqlIdQuery);
while($idfetch = $id->fetch_assoc()) {
		$currentid = $idfetch['User_id'];
}


//Function to find list of friends of the current user
function findFriends($currentUser){
$sqlfind = "SELECT Name 
			FROM tUser
			WHERE User_id IN (SELECT tFriends.Friend_id
			                  FROM tFriends
			                  WHERE User_id = (SELECT User_id
			                                   FROM tUser
			                                   WHERE Name='$currentUser'))";
$friends = $GLOBALS['conn']->query($sqlfind);

while($names = $friends->fetch_assoc()) {
		$temp = $names['Name'];
		echo "<br>"."<a href='SocialNetwork.php?name=$temp'>".$names['Name']."</a>"."<br><br>";	
	}	 
}
/* Synchronous Post
//Handling the post operation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  	$post = test_input($_POST["postsomething"]);
  	$postingQuery = "INSERT INTO tWall VALUES($currentid, Now(), '$post')";
  	$runPostQuery = $conn->query($postingQuery);

}
*/

//Method to validate the input provide by the user
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Method to display all the posts
function displayPost($currentid){
	$userPostQuery = 	"SELECT Post,Posting_date
						FROM tWall
						WHERE User_id = $currentid
						ORDER BY Posting_date DESC";
	$runUserPostQ = $GLOBALS['conn']->query($userPostQuery);
	while ($posts = $runUserPostQ->fetch_assoc()) {
		echo "<div class='well well-sm'>".$posts['Post']."<span style='float:right;'>".$posts['Posting_date']."</span></div>";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Social Network</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script>
		function ajaxFunc(cuser){
			var apost = document.forms["myform"]["postsomething"].value;
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	            if (this.readyState == 4 && this.status == 200) {
	                document.getElementById("insertPost").innerHTML = this.responseText;
	            }
	        };
	        xmlhttp.open("GET","asyncPost.php?post="+apost+"&name="+cuser,true);
        	xmlhttp.send();
		}
	</script>
</head>
<body style="background-color: #ffe1c3;">
	<div class="container-fluid">
		<div class="jumbotron">
			<h1 style="text-align: center;" id="cuser"><?php echo "$currentUser"; ?>
												
			</h1> 	
		</div>
		<div class="row">
			<div class="col-sm-3" style="text-align: center; background-color: #ededee;">
				
					<?php findFriends($currentUser); ?>
	
				
			</div>
			<div class="col-sm-9">
					<form class="form-group"  method="post" name="myform" id="form1">
						Write Something about yourself!!<br><br><textarea rows="7" cols="120" class="form-control" name="postsomething"
						 required></textarea>
						<br>
						<input type="button" name="submit" value="Post" onclick = "ajaxFunc('<?php echo $currentUser ?>')" class="btn btn-primary">
					</form>
					<div id="insertPost">
						<?php displayPost($currentid); ?>
					</div>
			</div>
		</div>
	</div>
</body>
</html>