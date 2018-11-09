<?php
    require_once('connection.php');

    $sql = "
		SELECT * FROM eventtype ORDER BY eventtype_order
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
				'eventtype_code' => $row['eventtype_code'],
				'eventtype_desc' => $row['eventtype_desc'],
				'eventtype_order' => $row['eventtype_order']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>