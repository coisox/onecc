<?php
    require_once('connection.php');
	
	$user = isset($_GET['user'])?$_GET['user']:'crm_api';
	$pass = isset($_GET['pass'])?$_GET['pass']:'crm_pass';
	$app_user = isset($_GET['app_user'])?$_GET['app_user']:'';
	$app_pass = isset($_GET['app_pass'])?$_GET['app_pass']:'Welcome123';			//P@ssw0rd, p@ssw0rd123456, Welcome123
	$source = isset($_GET['source'])?$_GET['source']:'meta_ru';
	$action = isset($_GET['action'])?$_GET['action']:'get_last_position';
	
	$sql = "
		SELECT
			resource_nric_or_reg
		FROM
			responder,
			resource
		WHERE
			responder_resource_nric_or_reg = resource_nric_or_reg
			AND resource_resourcetype_code = 'AMB'
			AND responder_statustype_code NOT IN ('new', 'completed')
	";
	
	$sql2 = "
		SELECT resource_nric_or_reg
		FROM resource
		WHERE resource_resourcetype_code = 'AMB'
	";
	
	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}

	$ch = curl_init();
	$total = 0;
		
	while($row = $result->fetch_assoc()) {
		$total++;
		echo 'Processing for '.$row['resource_nric_or_reg'].'<br>';
		
		$data = '{"login":{"user":"'.$user.'","pass":"'.$pass.'","app_user":"'.$app_user.'","app_pass":"'.$app_pass.'","source":"'.$source.'","action":"'.$action.'"},"data":[{"vehicle_plate":"'.$row['resource_nric_or_reg'].'"}]}';
		
		echo 'Post data: ' . $data;
		echo '<br>';
	
		curl_setopt($ch, CURLOPT_URL, 'https://www.cse.com.my/crm/main');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // required as of PHP 5.6.0
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		if(!isset($_GET['debug'])) curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$starttime = microtime(true);
		
		if(isset($_GET['debug'])) echo 'Result from API:<br>';
		$ch_result = json_decode(curl_exec($ch));
		
		$endtime = microtime(true);
		$timediff = $endtime - $starttime;
		
		if(!isset($_GET['debug'])) echo 'Detected lat: '.$ch_result -> lat;
		echo '<br>Elapse time: ' . $timediff . ' seconds<br><br>';

		if(curl_errno($ch)){
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'Curl Error: '.curl_error($ch), FILE_APPEND);
			echo 'Curl error for '.$row['resource_nric_or_reg'].': '.curl_error($ch).'<br>';
		}
		
		if($ch_result -> lat) {
			echo 'Inserting into DB...<br>';
			$sql2 = "
				INSERT INTO tracking (
					tracking_resource_nric_or_reg,
					tracking_coordinate,
					tracking_time
				) VALUES (
					'".$row['resource_nric_or_reg']."',
					'".($ch_result -> lat . ',' . $ch_result -> long)."',
					NOW()
				)
				ON DUPLICATE KEY UPDATE tracking_coordinate = '".($ch_result -> lat . ',' . $ch_result -> long)."', tracking_time = NOW()
			";

			if(!$result2 = $db->query($sql2)) {
				$timestamp = date("YmdHis");
				file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
				file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql2) . "\n", FILE_APPEND);
				echo json_encode(['status' => "SQL Error $timestamp"]);
				die();
			}
			
			echo 'Success update DB for '.$row['resource_nric_or_reg'].'<br><br>';
		}
	}
	curl_close($ch);
	
	echo "Done for $total records";
?>