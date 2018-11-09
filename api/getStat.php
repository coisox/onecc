<?php
    require_once('connection.php');

    $sql = "
		SELECT
			(SELECT COUNT(1) FROM callcard WHERE NULLIF(callcard_filingtype_code, '') IS NULL) stat_active,
			(SELECT COUNT(1) FROM callcard WHERE NULLIF(callcard_filingtype_code, '') IS NOT NULL) stat_history,
			(SELECT COUNT(1) FROM callcard WHERE (SELECT COUNT(1) FROM responder WHERE responder_callcard_id = callcard_id AND responder_statustype_code NOT IN ('new')) = 0 AND NULLIF(callcard_filingtype_code, '') IS NULL) stat_unassigned
		FROM DUAL
	";

	if(!$result = $db->query($sql)) {
		$timestamp = date("YmdHis");
		file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
		file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
		echo json_encode(['status' => "SQL Error $timestamp"]);
		die();
	}

	$data = [];
	if($row = $result->fetch_assoc()) {
		$data = [
			'stat_active' => $row['stat_active'],
			'stat_history' => $row['stat_history'],
			'stat_unassigned' => $row['stat_unassigned']
		];
	}

	echo json_encode(['status' => 'ok', 'data' => $data]);
?>