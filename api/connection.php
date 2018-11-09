<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
$db = new mysqli('127.0.0.1', 'root', '', 'onecc');
if(mysqli_connect_errno()) {
	$timestamp = date("YmdHis");
	file_put_contents("log/error.log", $timestamp . "\t\t" . 'Connection error: '. mysqli_connect_errno() . "\n", FILE_APPEND);
	echo json_encode(['status' => "Connection error"]);
	die();
}
session_start();
?>