<?php
	header("Access-Control-Allow-Origin: *");
    require_once('connection.php');
	
	//Get username from DB
	//====================================================================================================
	$sql = "
		SELECT
			resource_name,
			mobile_phone
		FROM
			resource,
			mobile
		WHERE
			resource_nric_or_reg = '".$_GET['nric']."'
			AND mobile_resource_nric_or_reg = resource_nric_or_reg
		ORDER BY resource_id DESC
		LIMIT 3
	";
	
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	
	$name = 'Unregistered User';
	$phone = 'Unregistered User';
	if($row = $result->fetch_assoc()) {
		$name = $row['resource_name'];
		$phone = $row['mobile_phone'];
	}
	//====================================================================================================
	
	
	//Get Callcard for that user only
	//====================================================================================================
	$sql = "
		SELECT 
			*,
			IFNULL(NULLIF(callcard_notes, ''), '-') callcard_notes,
			IFNULL(NULLIF(callcard_caller_phone, ''), 'No phone') callcard_caller_phone,
			(SELECT statustype_order FROM statustype WHERE statustype_code = responder_statustype_code) statustype_order,
			CASE
				WHEN responder_statustype_code != 'completed' AND NULLIF(callcard_filingtype_code, '') IS NULL THEN 'activeCallcard'
				ELSE 'classifiedCallcard'
			END callcard_status
		FROM
			callcard,
			patienttype,
			eventcode,
			eventtype,
			locationtype,
			responder
		WHERE
			callcard_patienttype_code = patienttype_code
			AND callcard_eventcode_code = eventcode_code
			AND callcard_eventtype_code = eventtype_code
			AND callcard_locationtype_code = locationtype_code
			AND responder_callcard_id = callcard_id
			AND responder_statustype_code != 'new'
			AND responder_resource_nric_or_reg = '".$_GET['nric']."'
		ORDER BY callcard_status, callcard_id DESC
	";
		
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	
	$data = [];
	$record = 0;
	while($row = $result->fetch_assoc()) {
		$record++;
		array_push($data,
			[
				'record' => $record,
				'callcard_id' => $row['callcard_id'],
				'callcard_caller_name' => $row['callcard_caller_name'],
				'callcard_caller_phone' => $row['callcard_caller_phone'],
				'callcard_notes' => $row['callcard_notes'],
				'callcard_incident_address' => $row['callcard_incident_address'],
				'callcard_patient_name' => $row['callcard_patient_name'],
				'callcard_incident_coordinate' => $row['callcard_incident_coordinate'],
				'responder_id' => $row['responder_id'],
				'responder_statustype_code' => $row['responder_statustype_code'],
				'statustype_order' => $row['statustype_order'],
				'patienttype_desc' => $row['patienttype_desc'],
				'eventcode_desc' => $row['eventcode_desc'],
				'eventtype_desc' => $row['eventtype_desc'],
				'locationtype_desc' => $row['locationtype_desc'],
				'filingtype_desc' => $row['filingtype_desc'],
				'callcard_status' => $row['callcard_status']
			]
		);
	}
	//====================================================================================================
	
	echo json_encode(['status' => 'ok', 'name' => $name, 'phone' => $phone, 'data' => $data]);
?>