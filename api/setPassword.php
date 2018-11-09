<?php
    require_once('connection.php');
	
	//Set new password
	//======================================================================================================
	$sql = "
		UPDATE user SET user_password = '".md5($_GET['new_password'])."' WHERE user_password = '".md5($_GET['old_password'])."' AND user_username = '".$_GET['username']."'
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
		echo json_encode(['status' => 'ok', 'message' => 'Password updated successfully', 'close' => true]);
		die();
	}
	
	echo json_encode(['status' => 'ok', 'message' => 'Invalid old password', 'close' => false]);
?>