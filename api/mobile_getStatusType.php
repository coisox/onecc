<?php
	header("Access-Control-Allow-Origin: *");
    require_once('connection.php');

    $sql = "
		SELECT * FROM statustype WHERE statustype_on_mobile IS NOT NULL ORDER BY statustype_order
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
				'statustype_on_mobile' => $row['statustype_on_mobile']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>