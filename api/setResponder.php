<?php
    require_once('connection.php');

	$sql = "";

	if($_GET['direction']=='left') {
		$sql = "
			INSERT INTO responder (
				responder_callcard_id,
				responder_resource_nric_or_reg,
				responder_statustype_code
			) VALUES (
				'".$_GET['callcard_id']."',
				'".$_GET['resource_nric_or_reg']."',
				'new'
			)
		";
	}
	else if($_GET['direction']=='right') {
		$sql = "
			DELETE FROM responder WHERE responder_id = '".$_GET['responder_id']."'
		";
	}
		
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}

	echo json_encode(['status' => 'ok']);
?>