<?php
    require_once('connection.php');

    $sql = "
		SELECT * FROM statustype ORDER BY statustype_order
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
				'statustype_code' => $row['statustype_code'],
				'statustype_desc' => $row['statustype_desc'],
				'statustype_order' => $row['statustype_order'],
				'statustype_icon' => $row['statustype_icon'],
				'statustype_color' => $row['statustype_color']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>