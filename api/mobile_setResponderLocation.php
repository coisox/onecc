<?php
	header("Access-Control-Allow-Origin: *");
    require_once('connection.php');
		
	$location = $_POST['location']['coords']['latitude'] .','. $_POST['location']['coords']['longitude'];
	
	$sql = "
		INSERT INTO tracking (
			tracking_resource_nric_or_reg,
			tracking_coordinate,
			tracking_time
		) VALUES (
			'".$_POST['tracking_resource_nric_or_reg']."',
			'$location',
			NOW()
		)
		ON DUPLICATE KEY UPDATE tracking_coordinate = '$location', tracking_time = NOW()
	";

	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}
	
	echo json_encode(['status' => "ok"]);
?>