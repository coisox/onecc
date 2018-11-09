<?php
	header("Access-Control-Allow-Origin: *");
    require_once('connection.php');
	
	if(isset($_GET['signout'])) {
		$sql = "
			DELETE FROM mobile WHERE mobile_resource_nric_or_reg = '".$_GET['nric']."'
		";
		
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
	
		echo json_encode(['status' => "ok"]);
		die();
	}
	else {
	
		//Massage phone number
		//=================================================================
		function massagePhoneNumber($phone) {
			$phone = str_replace(['+', '-', ' '], '', $phone);
			if($phone[0]=='6') $phone = substr($phone, 1);
			if($phone[1]=='1') $phone = substr($phone, 0, 3) . ' ' . substr($phone, 3);
			else $phone = substr($phone, 0, 2) . ' ' . substr($phone, 2);
			
			return $phone;
		}
		//=================================================================

		
		//Add new mobile
		//========================================================================================
		$sql = "
			INSERT INTO mobile (
				mobile_resource_nric_or_reg,
				mobile_phone,
				mobile_token
			) VALUES (
				'".$_GET['nric']."',
				'".massagePhoneNumber($_GET['phone'])."',
				'".$_GET['token']."'
			)
			ON DUPLICATE KEY UPDATE mobile_phone = '$phone', mobile_token = '".$_GET['token']."'
		";

		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
		//========================================================================================
		
		
		//Update mobile number on resource
		//========================================================================================
		$sql = "
			UPDATE resource SET resource_phone = '".massagePhoneNumber($_GET['phone'])."' WHERE resource_nric_or_reg = '".$_GET['nric']."'
		";

		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
		//========================================================================================
		
		echo json_encode(['status' => "ok"]);
		die();
	}
	
	echo json_encode(['status' => "Unknown problem"]);
?>