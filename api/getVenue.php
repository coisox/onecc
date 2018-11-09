<?php
    require_once('connection.php');

    $sql = "
		SELECT * FROM venue ORDER BY venue_desc
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
				'venue_id' => $row['venue_id'],
				'venue_desc' => $row['venue_desc'],
				'venue_address' => $row['venue_desc'].', '.$row['venue_address'],
				'venue_coordinate' => $row['venue_coordinate'],
				'venue_icon' => $row['venue_icon'],
				'venuetype_code' => $row['venuetype_code']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>