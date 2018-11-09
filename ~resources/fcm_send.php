<?php
	$msg = array(
		'title' => 'You have new task',
		'body' => 'Tap to open',
		'sound' => 'default'
	);
	
    $fields = array(
		'to' => 'cWRUgeAmuTQ:APA91bH8150LTR_zPBXetxaHp4ehQkpuLJqv5fCJ_d1d8G2e00EzHGPOZTrEC9OUqiIIOw-BUFHl6OjfIL4g3w_QdmMebimYKNSnda-lR473_JPL1oSYS52uZPB-o0CqD6LZ-wLz_pP1',
		'notification' => $msg,
		'data' => array('data1' => 'Lorem Ipsum')
	);

    $headers = array(
		'Authorization: key=' . "AAAAMJJ8Eds:APA91bELdy5Q2Lzcfnz7_Ww4h54RkHOv8QvZvfWFW5nAMHu70fASPN6-KApHwbbc9NHdsjwW9wcm0iYAXTZxwyMpqpynAQAM-jCpXpqwxPNzsXaKGlzelkQX6Qdza8nF22tR_5LbcMmg",
		'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    echo $result;
    curl_close ($ch);
?>