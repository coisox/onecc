<?php
    require_once('connection.php');

	//Push notification to mobile
	//======================================================================================================
	$token = [];
	$sql = "
		SELECT
			mobile_token
		FROM
			mobile,
			responder,
			callcard
		WHERE
			responder_resource_nric_or_reg = mobile_resource_nric_or_reg
			AND responder_callcard_id = callcard_id
			AND responder_statustype_code != 'new'
			AND callcard_id = '".$_GET['callcard_id']."'
	";
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	while($row = $result->fetch_assoc()) array_push($token, $row['mobile_token']);
	
	if(count($token)>0) {
		$msg = array(
			'title' => $_GET['title'],
			'body' => $_GET['body'],
			'sound' => 'default',
			'color' => '#333354',
			'click_action' => 'FCM_PLUGIN_ACTIVITY'
		);
		
		$fields = array(
			'registration_ids' => $token,
			'notification' => $msg,
			'data' => array('title' => $_GET['title'])
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

	echo json_encode(['status' => 'ok']);
?>