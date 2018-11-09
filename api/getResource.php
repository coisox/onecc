<?php
    require_once('connection.php');

	$sql = "
		SELECT
			*,
			DATE_FORMAT(resource_availability_from, '%Y-%m-%d %h:%i%p') resource_availability_from,
			DATE_FORMAT(resource_availability_to, '%Y-%m-%d %h:%i%p') resource_availability_to,
			(SELECT mobile_phone FROM mobile WHERE mobile_resource_nric_or_reg = resource_nric_or_reg) phone_opt1,
			NULLIF(resource_phone, '') phone_opt2,
			(SELECT 1 FROM responder WHERE responder_resource_nric_or_reg = resource_nric_or_reg AND responder_statustype_code != 'completed' LIMIT 1) total_active
		FROM
			resource,
			resourcetype,
			venue
		WHERE
			resource_resourcetype_code = resourcetype_code
			AND venue_id = resource_venue_id
		ORDER BY resource_resourcetype_code, resource_name
	";
		
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
		$dtRow++;
		$resource_phone = 'No phone';
		if($row['phone_opt1']) $resource_phone = $row['phone_opt1'];
		else if($row['phone_opt2']) $resource_phone = $row['phone_opt2'];
		
		array_push($data,
			[
				'dtRow' => $dtRow,
				'resource_id' => $row['resource_id'],
				'resourcetype_desc' => $row['resourcetype_desc'],
				'resource_nric_or_reg' => $row['resource_nric_or_reg'],
				'resource_resourcetype_code' => $row['resource_resourcetype_code'],
				'resource_name' => $row['resource_name'],
				'resource_phone' => $resource_phone.($row['phone_opt1']?' (Activated)':''),
				'resource_standby_location' => $row['resource_standby_location'],
				'resource_venue_id' => $row['resource_venue_id'],
				'venue_desc' => $row['venue_desc'],
				'resource_availability_from' => $row['resource_availability_from'],
				'resource_availability_to' => $row['resource_availability_to'],
				'callcard_locationtype_code' => $row['callcard_locationtype_code'],
				'callcard_notes' => $row['callcard_notes'],
				'total_active' => $row['total_active']				
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>