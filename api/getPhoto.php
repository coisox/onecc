<?php
    require_once('connection.php');
	
	$sql = "
		SELECT photo_url FROM photo WHERE photo_responder_id IN (SELECT responder_id FROM responder WHERE responder_callcard_id = '".$_GET['callcard_id']."')
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
				'photo_url' => $row['photo_url']
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>