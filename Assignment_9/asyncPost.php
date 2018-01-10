<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Social_Network";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$post = $_GET['post'];
$cname = $_GET['name'];
$sqlIdQuery = "SELECT User_id FROM tUser where Name = '$cname'";
$id = $conn->query($sqlIdQuery);
while($idfetch = $id->fetch_assoc()) {
		$currentid = $idfetch['User_id'];
}
$postingQuery = "INSERT INTO tWall VALUES($currentid, Now(), '$post')";
 $runPostQuery = $conn->query($postingQuery);



$sql = "SELECT Posting_date, Post FROM tWall WHERE User_id = $currentid ORDER BY Posting_date desc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($posts = $result->fetch_assoc()) {
		echo "<div class='well well-sm'>".$posts['Post']."<span style='float:right;'>".$posts['Posting_date']."</span></div>";
	}
}
?>