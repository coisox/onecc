<?php
    require_once('connection.php');

	$duration = $_GET['duration'];
	
	$sql = "";
	
	if($_GET['callcard_id']=='') {
		$sql = "
			INSERT INTO callcard (
				callcard_caller_name,
				callcard_caller_phone,
				callcard_patienttype_code,
				callcard_notes,
				callcard_patient_name,
				callcard_eventcode_code,
				callcard_eventtype_code,
				callcard_locationtype_code,
				callcard_incident_address,
				callcard_incident_coordinate,
				callcard_nearest_venue,
				callcard_filingtype_code,
				callcard_time_create,
				callcard_time_submit
			) VALUES (
				'".strtoupper(mysqli_real_escape_string($db, $_GET['callcard_caller_name']))."',
				'".mysqli_real_escape_string($db, $_GET['callcard_caller_phone'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_patienttype_code'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_notes'])."',
				'".strtoupper(mysqli_real_escape_string($db, $_GET['callcard_patient_name']))."',
				'".mysqli_real_escape_string($db, $_GET['callcard_eventcode_code'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_eventtype_code'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_locationtype_code'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_incident_address'])."',
				'".mysqli_real_escape_string($db, $_GET['formattedCoordinate'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_nearest_venue'])."',
				'".mysqli_real_escape_string($db, $_GET['callcard_filingtype_code'])."',
				(NOW() - INTERVAL $duration SECOND),
				NOW()
			)
		";
	}
	else {
		 $sql = "
			UPDATE callcard SET
				callcard_caller_name = '".strtoupper(mysqli_real_escape_string($db, $_GET['callcard_caller_name']))."',
				callcard_caller_phone = '".mysqli_real_escape_string($db, $_GET['callcard_caller_phone'])."',
				callcard_patienttype_code = '".mysqli_real_escape_string($db, $_GET['callcard_patienttype_code'])."',
				callcard_notes = '".mysqli_real_escape_string($db, $_GET['callcard_notes'])."',
				callcard_patient_name = '".strtoupper(mysqli_real_escape_string($db, $_GET['callcard_patient_name']))."',
				callcard_eventcode_code = '".mysqli_real_escape_string($db, $_GET['callcard_eventcode_code'])."',
				callcard_eventtype_code = '".mysqli_real_escape_string($db, $_GET['callcard_eventtype_code'])."',
				callcard_locationtype_code = '".mysqli_real_escape_string($db, $_GET['callcard_locationtype_code'])."',
				callcard_incident_address = '".mysqli_real_escape_string($db, $_GET['callcard_incident_address'])."',
				callcard_incident_coordinate = '".mysqli_real_escape_string($db, $_GET['callcard_incident_coordinate'])."',
				callcard_filingtype_code = '".mysqli_real_escape_string($db, $_GET['callcard_filingtype_code'])."'
			WHERE callcard_id = '".$_GET['callcard_id']."'
		";
	}

	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	else {
		$callcard_id = ($_GET['callcard_id']==''?str_pad(($db->insert_id), 4, "0", STR_PAD_LEFT):$_GET['callcard_id']);
		
		$sql = "
			INSERT INTO statuslog (
				statuslog_callcard_id,
				statuslog_time,
				statuslog_desc
			) VALUES (
				'".$callcard_id."',
				NOW(),
				'Callcard ".($_GET['callcard_id']==''?"created":"updated")." by ".strtoupper($_SESSION['user_fullname'])."'
			)
		";

		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}

		echo json_encode(['status' => 'ok', 'callcard_id' => $callcard_id]);
	}
?>