<?php
    require_once('connection.php');
	$data = $_POST['excel'];
	$success = 0;
	$duplicate = 0;
	$error = 0;
	
	//Massage phone number
	//=================================================================
	function massagePhoneNumber($phone) {
		$phone = str_replace(['+', '-', ' '], '', $phone);
		if($phone[0]=='6') $phone = substr($phone, 1);
		if($phone[1]=='1') $phone = substr($phone, 0, 3) . ' ' . substr($phone, 3);
		else $phone = substr($phone, 0, 2) . ' ' . substr($phone, 2);
		
		return $phone;
	}
	//=================================================================
	
	
	//PHPExcel
	//=====================================================
	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
	require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->createSheet(1);
	$objPHPExcel->setActiveSheetIndex(0)
		->setTitle('Duplicate')
		->setCellValue('A1', 'DESCRIPTION')
		->setCellValue('B1', 'resource_nric_or_reg')
		->setCellValue('C1', 'resource_resourcetype_code')
		->setCellValue('D1', 'resource_name')
		->setCellValue('E1', 'resource_phone')
		->setCellValue('F1', 'resource_standby_location')
		->setCellValue('H1', 'resource_venue_id')
		->setCellValue('I1', 'resource_availability_from')
		->setCellValue('J1', 'resource_availability_to');
	$objPHPExcel->setActiveSheetIndex(1)
		->setTitle('Error')
		->setCellValue('A1', 'ERROR')
		->setCellValue('B1', 'resource_nric_or_reg')
		->setCellValue('C1', 'resource_resourcetype_code')
		->setCellValue('D1', 'resource_name')
		->setCellValue('E1', 'resource_phone')
		->setCellValue('F1', 'resource_standby_location')
		->setCellValue('H1', 'resource_venue_id')
		->setCellValue('I1', 'resource_availability_from')
		->setCellValue('J1', 'resource_availability_to');
	//=====================================================
	
	
	foreach($data as $item){
		
		$validate_resourcetype = 0;
		$sql = "
			SELECT 1 FROM resourcetype WHERE resourcetype_code = '".$item['resource_resourcetype_code']."'
		";
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
		if($row = $result->fetch_assoc()) $validate_resourcetype = 1;
		
		
		$validate_venue = 0;
		$sql = "
			SELECT 1 FROM venue WHERE venue_id = '".$item['resource_venue_id']."'
		";
		if(!$result = $db->query($sql)) {
			$timestamp = date("YmdHis");
			file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
			file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sql) . "\n", FILE_APPEND);
			echo json_encode(['status' => "SQL Error $timestamp"]);
			die();
		}
		if($row = $result->fetch_assoc()) $validate_venue = 1;
		
		
		$validate_date = 1;
		if($item['resource_availability_from'] > $item['resource_availability_to']) $validate_date = 0;
			
		
		if($validate_resourcetype + $validate_venue + $validate_date != 3) {
			$error++;
			$invalid = [];
			if($validate_resourcetype==0) array_push($invalid, 'resource_resourcetype_code');
			if($validate_venue==0) array_push($invalid, 'resource_venue_id');
			if($validate_date==0) array_push($invalid, 'resource_availability_from');
				
			$objPHPExcel->setActiveSheetIndex(1)
				->setCellValue('A'.($error+2), 'Invalid '.implode(', ', $invalid))
				->setCellValue('B'.($error+2), $item['resource_nric_or_reg'])
				->setCellValue('C'.($error+2), $item['resource_resourcetype_code'])
				->setCellValue('D'.($error+2), $item['resource_name'])
				->setCellValue('E'.($error+2), $item['resource_phone'])
				->setCellValue('F'.($error+2), $item['resource_standby_location'])
				->setCellValue('H'.($error+2), $item['resource_venue_id'])
				->setCellValue('I'.($error+2), $item['resource_availability_from'])
				->setCellValue('J'.($error+2), $item['resource_availability_to']);
		}
		else {
			$sql = "
				INSERT INTO resource (
					resource_nric_or_reg,
					resource_resourcetype_code,
					resource_name,
					resource_phone,
					resource_standby_location,
					resource_venue_id,
					resource_availability_from,
					resource_availability_to
				) VALUES (
					'".mysqli_real_escape_string($db, $item['resource_nric_or_reg'])."',
					'".$item['resource_resourcetype_code']."',
					'".strtoupper(mysqli_real_escape_string($db, $item['resource_name']))."',
					'".massagePhoneNumber($item['resource_phone'])."',
					'".$item['resource_standby_location']."',
					'".$item['resource_venue_id']."',
					'".$item['resource_availability_from']."',
					'".$item['resource_availability_to']."'
				)
			";
			
			if(!$result = $db->query($sql)) {
				if(strpos(($db->error), 'Duplicate') !== false) {
					$duplicate++;
					
					$sqlDuplicate = "
						SELECT * FROM resource
						WHERE
							resource_nric_or_reg = '".$item['resource_nric_or_reg']."'
							AND resource_availability_from = '".$item['resource_availability_from']."'
							AND resource_availability_to = '".$item['resource_availability_to']."'
					";
					
					if(!$resultDuplicate = $db->query($sqlDuplicate)) {
						$timestamp = date("YmdHis");
						file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
						file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sqlDuplicate) . "\n", FILE_APPEND);
						echo json_encode(['status' => "SQL Error $timestamp"]);
						die();
					}

					if($row = $resultDuplicate->fetch_assoc()) {
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.($duplicate*3), 'Existing')
							->setCellValue('B'.($duplicate*3), $row['resource_nric_or_reg'])
							->setCellValue('C'.($duplicate*3), $row['resource_resourcetype_code'])
							->setCellValue('D'.($duplicate*3), $row['resource_name'])
							->setCellValue('E'.($duplicate*3), $row['resource_phone'])
							->setCellValue('F'.($duplicate*3), $row['resource_standby_location'])
							->setCellValue('H'.($duplicate*3), $row['resource_venue_id'])
							->setCellValue('I'.($duplicate*3), $row['resource_availability_from'])
							->setCellValue('J'.($duplicate*3), $row['resource_availability_to'])
							
							->setCellValue('A'.($duplicate*3+1), 'New and ignored')
							->setCellValue('B'.($duplicate*3+1), $item['resource_nric_or_reg'])
							->setCellValue('C'.($duplicate*3+1), $item['resource_resourcetype_code'])
							->setCellValue('D'.($duplicate*3+1), $item['resource_name'])
							->setCellValue('E'.($duplicate*3+1), $item['resource_phone'])
							->setCellValue('F'.($duplicate*3+1), $item['resource_standby_location'])
							->setCellValue('H'.($duplicate*3+1), $item['resource_venue_id'])
							->setCellValue('I'.($duplicate*3+1), $item['resource_availability_from'])
							->setCellValue('J'.($duplicate*3+1), $item['resource_availability_to']);
					}
				}
				else {
					$timestamp = date("YmdHis");
					file_put_contents("log/error.log", $timestamp . "\t\t" . 'SQL Error: '.($db->error), FILE_APPEND);
					file_put_contents("log/error.log", preg_replace("/\n/", "\n\t\t\t\t\t\t\t\t", $sqlDuplicate) . "\n", FILE_APPEND);
					//echo json_encode(['status' => "SQL Error $timestamp"]);
					
					$error++;
					
					$objPHPExcel->setActiveSheetIndex(1)
						->setCellValue('A'.($phpRow+1), ($db->error))
						->setCellValue('B'.($phpRow+1), $item['resource_nric_or_reg'])
						->setCellValue('C'.($phpRow+1), $item['resource_resourcetype_code'])
						->setCellValue('D'.($phpRow+1), $item['resource_name'])
						->setCellValue('E'.($phpRow+1), $item['resource_phone'])
						->setCellValue('F'.($phpRow+1), $item['resource_standby_location'])
						->setCellValue('H'.($phpRow+1), $item['resource_venue_id'])
						->setCellValue('I'.($phpRow+1), $item['resource_availability_from'])
						->setCellValue('J'.($phpRow+1), $item['resource_availability_to']);
				}
			}
			else {
				$success++;
			}
		}
	}

	$filename = 'Report'.' '.date('YmdHis').'.xlsx';
	foreach (range('A', $objPHPExcel->setActiveSheetIndex(1)->getHighestDataColumn()) as $col) {
		$objPHPExcel->getActiveSheet()
			->getColumnDimension($col)
			->setAutoSize(true);
    } 
	foreach (range('A', $objPHPExcel->setActiveSheetIndex(0)->getHighestDataColumn()) as $col) {
		$objPHPExcel->getActiveSheet()
			->getColumnDimension($col)
			->setAutoSize(true);
    } 
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save(str_replace(__FILE__, "log/$filename", __FILE__));
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	//$objWriter->save(str_replace(__FILE__, "log/$filename.xls", __FILE__));
	
	$fileurl = str_replace("setResourceFromExcel.php", "log/$filename", (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]");
	echo json_encode(['status' => 'ok', 'received' => count($data), 'success' => $success, 'duplicate' => $duplicate, 'error' => $error, 'fileurl' => $fileurl]);
?>