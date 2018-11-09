<?php
    $data = [umur => 13, tahun => 2017];
	
	$j = json_encode($data);
	
	$y = json_decode($j);
	
	echo $y -> umur;
	
?>