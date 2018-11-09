<?php
    require_once('connection.php');

    $sql = "
		SELECT
			IFNULL(NULLIF(resource_standby_location, ''), venue_coordinate) resource_standby_location
		FROM
			resource,
			venue
		WHERE
			resource_venue_id = venue_id
			AND resource_resourcetype_code = 'AMB'
			AND NOW() BETWEEN resource_availability_from AND resource_availability_to
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
				'resource_standby_location' => $row['resource_standby_location']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>