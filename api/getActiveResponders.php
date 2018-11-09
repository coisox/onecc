<?php
    require_once('connection.php');
	
	$filterByCallcard = "";
	if(isset($_GET['filterByCallcard'])) $filterByCallcard = "AND responder_callcard_id = '".$_GET['filterByCallcard']."'";

	$sql = "
		SELECT
			resource_nric_or_reg,
			resource_name,
			resource_resourcetype_code,
			responder_callcard_id,
			COALESCE(
				(SELECT tracking_coordinate FROM tracking WHERE tracking_resource_nric_or_reg = resource_nric_or_reg ORDER BY tracking_time DESC LIMIT 1),
				NULLIF(resource_standby_location, ''),
				(SELECT venue_coordinate FROM venue WHERE venue_id = resource_venue_id)
			) current_location
		FROM
			responder,
			resource,
			callcard
		WHERE
			responder_resource_nric_or_reg = resource_nric_or_reg
			AND resource_id = (SELECT MAX(R2.resource_id) FROM resource R2 WHERE R2.resource_nric_or_reg = responder_resource_nric_or_reg)
			AND responder_statustype_code NOT IN ('new', 'completed')
			AND callcard_id = responder_callcard_id
			AND NULLIF(callcard_filingtype_code, '') IS NULL
			$filterByCallcard
	";
	
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}

	$data = [];
	while($row = $result->fetch_assoc()) {
		array_push($data,
			[
				'resource_nric_or_reg' => $row['resource_nric_or_reg'],
				'resource_name' => $row['resource_name'],
				'resource_resourcetype_code' => $row['resource_resourcetype_code'],
				'responder_callcard_id' => $row['responder_callcard_id'],
				'current_location' => $row['current_location']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>