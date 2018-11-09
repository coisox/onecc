<?php
    require_once('connection.php');

	$sql = "
		SELECT
			resource_nric_or_reg,
			resourcetype_desc,
			resource_name,
			(SELECT mobile_phone FROM mobile WHERE mobile_resource_nric_or_reg = resource_nric_or_reg) phone_opt1,
			NULLIF(resource_phone, '') phone_opt2,
			responder_id,
			responder_statustype_code,
			statustype_icon,
			statustype_color,
			statustype_order
		FROM
			responder,
			resource,
			resourcetype,
			statustype
		WHERE
			resource_resourcetype_code = resourcetype_code
			AND responder_resource_nric_or_reg = resource_nric_or_reg
			AND resource_id = (SELECT MAX(R2.resource_id) FROM resource R2 WHERE R2.resource_nric_or_reg = responder_resource_nric_or_reg)
			AND statustype_code = responder_statustype_code
			AND responder_callcard_id = '".$_GET['callcard_id']."'
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
				'responder_id' => $row['responder_id'],
				'responder_statustype_code' => $row['responder_statustype_code'],
				'statustype_icon' => $row['statustype_icon'],
				'statustype_color' => $row['statustype_color'],
				'statustype_order' => $row['statustype_order'],
				'action' => '<a href="javascript:;" onclick="move(\'right\', \''.$row['resource_nric_or_reg'].'\', \''.$row['responder_id'].'\')"><i class="material-icons">arrow_forward</i></a>'
			]
		);
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>