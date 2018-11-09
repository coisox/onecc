<?php
    require_once('connection.php');

    $sql = "
		SELECT * FROM patienttype ORDER BY patienttype_order
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
				'patienttype_code' => $row['patienttype_code'],
				'patienttype_desc' => $row['patienttype_desc'],
				'patienttype_order' => $row['patienttype_order']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>