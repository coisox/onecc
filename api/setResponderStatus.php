<?php
    require_once('connection.php');

	//Step 1: Get responder name
	//======================================================================================================
	$resource_name = "";
	$sql = "
		SELECT resource_name
		FROM
			responder,
			resource
		WHERE
			responder_resource_nric_or_reg = resource_nric_or_reg
			AND resource_id = (SELECT MAX(R2.resource_id) FROM resource R2 WHERE R2.resource_nric_or_reg = responder_resource_nric_or_reg)
			AND responder_id = '".$_GET['responder_id']."'
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	if($row = $result->fetch_assoc()) $resource_name = $row['resource_name'];
	
	
	//Step 2: Generate statuslog string
	//======================================================================================================
	$statustype_log_template = "";
	$sql = "
		SELECT statustype_log_template FROM statustype WHERE statustype_code = '".$_GET['responder_statustype_code']."'
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	if($row = $result->fetch_assoc()) $statustype_log_template = $row['statustype_log_template'];
	$statustype_log_template = str_replace('RESPONDER', strtoupper($resource_name), $statustype_log_template);
	$statustype_log_template = str_replace('USERNAME', strtoupper($_SESSION['user_fullname']), $statustype_log_template);
	
	
	//Step 3: Add statuslog
	//======================================================================================================
	if($_GET['isRollback']=="true") {
		$sql = "
			INSERT INTO statuslog (
				statuslog_callcard_id,
				statuslog_time,
				statuslog_desc
			) VALUES (
				'".$_GET['callcard_id']."',
				NOW(),
				'".strtoupper($resource_name)." status has been rolled back by ".strtoupper($_SESSION['user_fullname'])."'
			)
		";
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
	}
	
	$sql = "
		INSERT INTO statuslog (
			statuslog_callcard_id,
			statuslog_time,
			statuslog_desc
		) VALUES (
			'".$_GET['callcard_id']."',
			NOW(),
			'".$statustype_log_template."'
		)
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	
	
	//Step 4: Update ke table responder
	//======================================================================================================
	$sql = "
		UPDATE responder SET responder_statustype_code = '".$_GET['responder_statustype_code']."' WHERE responder_id = '".$_GET['responder_id']."'
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	
	
	//Step 5: Push notification to mobile
	//======================================================================================================
	$token = "";
	$sql = "
		SELECT
			mobile_token
		FROM
			mobile,
			responder
		WHERE
			responder_resource_nric_or_reg = mobile_resource_nric_or_reg
			AND responder_id = '".$_GET['responder_id']."'
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	if($row = $result->fetch_assoc()) $token = $row['mobile_token'];
	
	if($token=="") {
		echo json_encode(['status' => 'Status successfully changed but responder hasn\'t being notified due to no active device registered']);
		die();
	}
	else {
		$FCM_Title = 'Status changed '.$_GET['callcard_id'];
		if($_GET['responder_statustype_code']=='new') $FCM_Title = 'Drop callcard '.$_GET['callcard_id'];
		else if($_GET['responder_statustype_code']=='dispatched') $FCM_Title = 'New callcard '.$_GET['callcard_id'];

		$msg = array(
			'title' => $FCM_Title,
			'body' => 'Tap to open',
			'sound' => 'default',
			'color' => '#333354',
			'click_action' => 'FCM_PLUGIN_ACTIVITY'
		);
		
		$fields = array(
			'to' => $token,
			'notification' => $msg,
			'data' => array('title' => $FCM_Title)
		);

		$headers = array(
			'Authorization: key=' . "AAAAMJJ8Eds:APA91bELdy5Q2Lzcfnz7_Ww4h54RkHOv8QvZvfWFW5nAMHu70fASPN6-KApHwbbc9NHdsjwW9wcm0iYAXTZxwyMpqpynAQAM-jCpXpqwxPNzsXaKGlzelkQX6Qdza8nF22tR_5LbcMmg",
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		$result = curl_exec($ch);
		curl_close($ch);
	}
	//======================================================================================================
	

	echo json_encode(['status' => 'ok', 'isRollback' => $_GET['isRollback']]);
?>