<?php
    require_once('connection.php');

    $sql = "
		SELECT
			user_fullname,
			user_username,
			user_roles
		FROM
			user
		WHERE
			user_username = '".$_GET['username']."'
			AND user_password = '".md5($_GET['password'])."'
	";
	
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error"]);
		die();
	}

	$data = [];
	if($row = $result->fetch_assoc()) {
		$data =  [
			'user_fullname' => $row['user_fullname'],
			'user_username' => $row['user_username'],
			'user_roles' => $row['user_roles'],
			'status' => 'ok'
		];
		
		$_SESSION['user_fullname'] = $row['user_fullname'];
	}
	else {
		$data =  [
			'status' => 'Invalid login'
		];
	}

	echo json_encode($data);
?>