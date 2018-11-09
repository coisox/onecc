<?php
    require_once('connection.php');
	
	//Set new password
	//======================================================================================================
	$sql = "
		UPDATE user SET user_fullname = '".$_GET['desiredname']."' WHERE user_username = '".$_GET['username']."'
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	$update = $db->affected_rows;

	if($update>0) {
		echo json_encode(['status' => 'ok', 'message' => 'Display name updated successfully', 'close' => true]);
		$_SESSION['user_fullname'] = $_GET['desiredname'];
		die();
	}
	
	echo json_encode(['status' => 'ok', 'message' => 'Something went wrong but we couldn\'t tell', 'close' => false]);
?>