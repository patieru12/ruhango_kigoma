<?php
session_start();

if(count($_SESSION) <= 0){
	?>
	<span style="font-size: 15px; font-weight: bold; font-family: helvetica; color: red;">Please Login to use Messages</span>
	<?php
} else {
	require_once "../lib/db_function.php";
	//var_dump($_SESSION);
	// Here get the currently logged user to get all messages

	$userId = $_SESSION["user"]["UserID"];

	$sql = "SELECT * FROM msg_data AS a WHERE a.receiverId='{$userId}' AND readAt IS NULL";

	$data = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	echo count($data)." Message unread";
}
?>