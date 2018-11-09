<?php
	header("Access-Control-Allow-Origin: *");
    require_once('connection.php');
	
	$success = file_put_contents('photo/'.$_POST['responder_id'].'_'.$_POST['filename'], base64_decode($_POST['dataUrl']));

	if($success) {
		$sql = "
			INSERT INTO photo (photo_responder_id, photo_url) VALUES ('".$_POST['responder_id']."', 'api/photo/".$_POST['responder_id'].'_'.$_POST['filename']."')
		";
		
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
	}
	else {
		echo json_encode(['status' => "Fail to save photo on server"]);
		die();
	}
	
	echo json_encode(['status' => 'ok']);
?>