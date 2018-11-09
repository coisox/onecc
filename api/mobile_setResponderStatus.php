<?php
	header("Access-Control-Allow-Origin: *");
    require_once('connection.php');

	//Step 1: Get responder name
	//======================================================================================================
	$resource_name = $_GET['name'];
	
	
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
				'".strtoupper($resource_name)." status has been rolled back by ".strtoupper($resource_name)."'
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
	

	echo json_encode(['status' => 'ok', 'isRollback' => $_GET['isRollback']]);
?>