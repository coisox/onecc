<?php
    require_once('connection.php');

	$filter = "";
	if (isset($_GET['filter'])) {
		if($_GET['filter']=='filterActive') $filter = "AND NULLIF(callcard_filingtype_code, '') IS NULL";
		else if($_GET['filter']=='filterHistory') $filter = "AND NULLIF(callcard_filingtype_code, '') IS NOT NULL";
		else if($_GET['filter']=='filterUnassigned') $filter = "AND (SELECT COUNT(1) FROM responder WHERE responder_callcard_id = callcard_id AND responder_statustype_code NOT IN ('new')) = 0 AND NULLIF(callcard_filingtype_code, '') IS NULL";
	}
	
	$sql = "
		SELECT 
			*,
			IFNULL(NULLIF(callcard_caller_phone, ''), 'No phone') callcard_caller_phone,
			(SELECT filingtype_code FROM filingtype WHERE filingtype_code = callcard_filingtype_code) filingtype_code,
			(SELECT filingtype_desc FROM filingtype WHERE filingtype_code = callcard_filingtype_code) filingtype_desc,
			(SELECT COUNT(1) FROM responder WHERE responder_callcard_id = callcard_id AND responder_statustype_code != 'completed') active_responder,
			CASE
				WHEN NULLIF(callcard_filingtype_code, '') IS NULL THEN 'activeCallcard'
				ELSE 'classifiedCallcard'
			END callcard_status
		FROM
			callcard,
			patienttype,
			eventcode,
			eventtype,
			locationtype
		WHERE
			callcard_patienttype_code = patienttype_code
			AND callcard_eventcode_code = eventcode_code
			AND callcard_eventtype_code = eventtype_code
			AND callcard_locationtype_code = locationtype_code
			$filter
		ORDER BY callcard_id DESC
	";

	if(isset($_GET['activecallcard'])) {
		$sql = "
			SELECT DISTINCT
				callcard_id,
				callcard_incident_address,
				callcard_patient_name,
				CASE
					WHEN NULLIF(callcard_filingtype_code, '') IS NULL THEN 'activeCallcard'
					ELSE 'classifiedCallcard'
				END callcard_status,
				(SELECT MAX(statuslog_time) FROM statuslog WHERE statuslog_callcard_id = callcard_id) time_update
			FROM callcard
			WHERE NULLIF(callcard_filingtype_code, '') IS NULL
			ORDER BY time_update DESC, callcard_id DESC
		";
	}
		
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}

	$data = [];
	$dtRow = -1;
	while($row = $result->fetch_assoc()) {
		
		//Get list of responder
		//=========================================================================================
		$sql = "
			SELECT
				responder_id,
				resource_name,
				(SELECT mobile_phone FROM mobile WHERE mobile_resource_nric_or_reg = resource_nric_or_reg) phone_opt1,
				NULLIF(resource_phone, '') phone_opt2,
				statustype_code,
				statustype_desc,
				statustype_order,
				statustype_icon,
				statustype_color
			FROM
				responder,
				resource,
				statustype
			WHERE
				responder_resource_nric_or_reg = resource_nric_or_reg
				AND resource_id = (SELECT MAX(R2.resource_id) FROM resource R2 WHERE R2.resource_nric_or_reg = responder_resource_nric_or_reg)
				AND responder_statustype_code = statustype_code
				AND responder_callcard_id = ".$row['callcard_id']."
			ORDER BY resource_resourcetype_code, resource_name
		";
		
		if(!$result2 = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}

		$arr_responders = [];
		$arr_responder_id = [-1];
		while($row2 = $result2->fetch_assoc()) {
			$resource_phone = 'No phone';
			if($row2['phone_opt1']) $resource_phone = $row2['phone_opt1'];
			else if($row2['phone_opt2']) $resource_phone = $row2['phone_opt2'];
			
			array_push($arr_responders,
				[
					'responder_id' => $row2['responder_id'],
					'resource_name' => $row2['resource_name'],
					'resource_phone' => $resource_phone.($row2['phone_opt1']?' (Activated)':''),
					'statustype_code' => $row2['statustype_code'],
					'statustype_desc' => $row2['statustype_desc'],
					'statustype_order' => $row2['statustype_order'],
					'statustype_icon' => $row2['statustype_icon'],
					'statustype_color' => $row2['statustype_color']
				]
			);
			array_push($arr_responder_id, $row2['responder_id']);
		}

		//Get statuslog
		//=========================================================================================
		$sql = "
			SELECT * FROM statuslog
			WHERE statuslog_callcard_id = '".$row['callcard_id']."'
			ORDER BY statuslog_time
		";
		
		if(!$result2 = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}

		$arr_statuslog = [];
		while($row2 = $result2->fetch_assoc()) {
			array_push($arr_statuslog,
				[
					'statuslog_time' => $row2['statuslog_time'],
					'statuslog_desc' => $row2['statuslog_desc']
				]
			);
		}
		
		//Get photo
		//=========================================================================================
		$sql = "
			SELECT
				photo_url,
				resource_name
			FROM
				photo,
				responder,
				resource
			WHERE
				photo_responder_id IN (".implode(", ", $arr_responder_id).")
				AND photo_responder_id = responder_id
				AND responder_resource_nric_or_reg = resource_nric_or_reg
				AND resource_id = (SELECT MAX(R2.resource_id) FROM resource R2 WHERE R2.resource_nric_or_reg = responder_resource_nric_or_reg)
			ORDER BY photo_id
		";
		
		if(!$result2 = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}

		$arr_photo = [];
		while($row2 = $result2->fetch_assoc()) {
			array_push($arr_photo,
				[
					'photo_url' => $row2['photo_url'],
					'resource_name' => $row2['resource_name']
				]
			);
		}
		
		//Finalize
		//=========================================================================================
		$dtRow++;
		array_push($data,
			[
				'dtRow' => $dtRow,
				'callcard_id' => $row['callcard_id'],
				'callcard_caller_name' => $row['callcard_caller_name'],
				'callcard_caller_phone' => $row['callcard_caller_phone'],
				'callcard_patienttype_code' => $row['callcard_patienttype_code'],
				'callcard_notes' => $row['callcard_notes'],
				'callcard_incident_address' => $row['callcard_incident_address'],
				'callcard_patient_name' => $row['callcard_patient_name'],
				'callcard_eventcode_code' => $row['callcard_eventcode_code'],
				'callcard_eventtype_code' => $row['callcard_eventtype_code'],
				'callcard_locationtype_code' => $row['callcard_locationtype_code'],
				'callcard_incident_coordinate' => $row['callcard_incident_coordinate'],
				'callcard_filingtype_code' => $row['callcard_filingtype_code'],
				'arr_responders' => $arr_responders,
				'arr_statuslog' => $arr_statuslog,
				'arr_photo' => $arr_photo,
				'patienttype_desc' => $row['patienttype_desc'],
				'eventcode_code' => $row['eventcode_code'],
				'eventtype_desc' => $row['eventtype_desc'],
				'locationtype_desc' => $row['locationtype_desc'],
				'filingtype_desc' => $row['filingtype_desc'],
				'active_responder' => $row['active_responder'], //tak pakai tp reserve in case nak control Filing Type bersyarat semua responder dah completed
				'callcard_status' => $row['callcard_status']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data, 'sql' => $xxx]);
?>