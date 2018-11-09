<?php
    require_once('connection.php');
	
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
	
	
	$sql = "";
	$resource_query_type = "";
	
	if($_GET['del']=='1') {
		$resource_query_type = "delete";
		$sql = "
			DELETE FROM resource WHERE resource_id = '".$_GET['resource_id']."'
		";
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
	}
	else if($_GET['resource_id']=='') {
		$resource_query_type = "insert";
		$sql = "
			INSERT INTO resource (
				resource_nric_or_reg,
				resource_resourcetype_code,
				resource_name,
				resource_phone,
				resource_standby_location,
				resource_venue_id,
				resource_availability_from,
				resource_availability_to
			) VALUES (
				'".$_GET['resource_nric_or_reg']."',
				'".$_GET['resource_resourcetype_code']."',
				'".strtoupper(mysqli_real_escape_string($db, $_GET['resource_name']))."',
				'".massagePhoneNumber($_GET['resource_phone'])."',
				'".$_GET['resource_standby_location']."',
				'".$_GET['resource_venue_id']."',
				STR_TO_DATE('".$_GET['resource_availability_from']."', '%Y-%m-%d %h:%i%p'),
				STR_TO_DATE('".$_GET['resource_availability_to']."', '%Y-%m-%d %h:%i%p')
			)
		";
		if(!$result = $db->query($sql)) {
			if(strpos(($db->error), 'Duplicate') !== false) {
				//echo json_encode(['status' => "Duplicate Entry Found"]);
			}
			else {
				$timestamp = date("YmdHis");
				file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
				file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
				echo json_encode(['status' => "SQL Error $timestamp"]);
				die();
			}
		}
	}
	else {
		$resource_query_type = "update";
		$sql = "
			UPDATE resource SET
				resource_resourcetype_code = '".$_GET['resource_resourcetype_code']."',
				resource_name = '".strtoupper(mysqli_real_escape_string($db, $_GET['resource_name']))."',
				resource_phone = '".massagePhoneNumber($_GET['resource_phone'])."',
				resource_standby_location = '".$_GET['resource_standby_location']."',
				resource_venue_id = '".$_GET['resource_venue_id']."',
				resource_availability_from = STR_TO_DATE('".$_GET['resource_availability_from']."', '%Y-%m-%d %h:%i%p'),
				resource_availability_to = STR_TO_DATE('".$_GET['resource_availability_to']."', '%Y-%m-%d %h:%i%p')
			WHERE resource_id = '".$_GET['resource_id']."'
		";
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			
			if(strpos(($db->error), 'UNIQUE')!==false) echo json_encode(['status' => explode(" for key ", ($db->error))[0]]);
			else echo json_encode(['status' => "SQL Error $timestamp"]);
			
			die();
		}
	}
	
	echo json_encode(['status' => 'ok', 'resource_query_type' => $resource_query_type]);
?>