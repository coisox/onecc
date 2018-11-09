<?php
    require_once('connection.php');

	$sql = "
		SELECT
			resource_nric_or_reg,
			resourcetype_code,
			resourcetype_desc,
			resource_name,
			(SELECT mobile_phone FROM mobile WHERE mobile_resource_nric_or_reg = resource_nric_or_reg) phone_opt1,
			NULLIF(resource_phone, '') phone_opt2
		FROM
			resource,
			resourcetype
		WHERE
			resource_resourcetype_code = resourcetype_code
			AND resource_nric_or_reg NOT IN (SELECT responder_resource_nric_or_reg FROM responder WHERE responder_statustype_code != 'completed')
			AND NOW() BETWEEN resource_availability_from AND resource_availability_to
			AND resource_venue_id = (SELECT callcard_nearest_venue FROM callcard WHERE callcard_id = ".$_GET['callcard_id'].")
		ORDER BY resource_resourcetype_code, resource_name
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
		
		$resource_phone = 'No phone';
		if($row['phone_opt1']) $resource_phone = $row['phone_opt1'];
		else if($row['phone_opt2']) $resource_phone = $row['phone_opt2'];
		
		array_push($data,
			[
				'resourcetype_desc' => $row['resourcetype_desc'],
				'resource' => $row['resource_name'].'<br>'.$resource_phone.($row['phone_opt1']?' (Activated)':''),
				'action' => '<a href="javascript:;" onclick="move(\'left\', \''.$row['resource_nric_or_reg'].'\', \'\')"><i class="material-icons">arrow_back</i></a>'
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>