<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

//Функция для http get запроса
function GetData($url, $data, $headers) {
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER	=>	true,
	    CURLOPT_SSL_VERIFYPEER	=>	false,
		CURLOPT_SSL_VERIFYHOST	=>	false,
		CURLOPT_HEADER			=>	false,
		CURLOPT_CONNECTTIMEOUT	=>	4,
		CURLOPT_HTTPHEADER		=>	$headers,
		CURLOPT_URL				=>	$url.'?'.http_build_query($data),
		//CURLOPT_PROXY			=>	'127.0.0.1:8888',
	));
	$answer = array(
		'result'	=>	curl_exec($curl),
		'error'		=>	curl_error($curl),
		'http_code'	=>	curl_getinfo($curl, CURLINFO_HTTP_CODE),
	);
	curl_close($curl);
	
	//Обработка ошибки curl
	if($answer['result'] === false){
		exit('{"LocalError":"'.addslashes($answer['error']).'"}');
	}
	
	//Если код ответа не 200
	if($answer['http_code'] != 200) {
		exit('{"LocalError":"Unexpected http code '.$answer['http_code'].'"}');
	}

	return $answer['result'];
}


//Вывод информации о почтовом отделении (getinfo.php?zipcode=101000)
if (isset($_GET['zipcode']) && !empty($_GET['zipcode'])) {
	$zipcode = (int)$_GET['zipcode'];
	
	$url = 'https://www.pochta.ru/portal-portlet/delegate/postoffice-api/method/offices.find.byCode';
	$data = array(
		'postalCode' => $zipcode,
	);
	$headers = array(
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:51.0) Gecko/20100101 Firefox/51.0',
		'Accept: application/json',
		'X-Requested-With: XMLHttpReques',
		'Referer: https://www.pochta.ru/offices',
		'Connection: close',
	);
	exit( GetData($url, $data, $headers) );
	
//Вывод информации о почтовом отправлении (getinfo.php?barcode=12345678901234)
} else if (isset($_GET['barcode']) && !empty($_GET['barcode'])) {
	$barcode = $_GET['barcode'];
	
	$url = 'https://www.pochta.ru/tracking';
	$data = array(
		'p_p_id' => 'trackingPortlet_WAR_portalportlet',
		'p_p_lifecycle' => '2',
		'p_p_state' => 'normal',
		'p_p_mode' => 'view',
		'p_p_resource_id' => 'getList',
		'p_p_cacheability' => 'cacheLevelPage',
		'p_p_col_id' => 'column-1',
		'p_p_col_pos' => '1',
		'p_p_col_count' => '2',
		'barcodeList' => $barcode,
	);
	$headers = array(
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:51.0) Gecko/20100101 Firefox/51.0',
		'Accept: application/json',
		'X-Requested-With: XMLHttpReques',
		'Referer: https://www.pochta.ru/tracking',
		'Connection: close',
	);
	exit( GetData($url, $data, $headers) );
	
} else {
	exit('{"LocalError":"No parameters"}');
}


?>

