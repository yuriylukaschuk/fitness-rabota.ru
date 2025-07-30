<?php
require_once('constants.php');
require_once('PHPMailer/src/Exception.php');
require_once('PHPMailer/src/PHPMailer.php');
require_once('PHPMailer/src/SMTP.php');

function get_siteinfo(){
	$host = $_SERVER["HTTP_HOST"];
	$domain = SCHEME.$host;
	$response = array(
		'host' => $host,
		'domain' => $domain,
	);
	return $response;
}

function login($exception = 0){
	$status_id = 0; $url = ''; $response = array();
	$urls = array(
		0 => 'index',
		1 => 'testconfirm',
		2 => 'testing',
		3 => 'testresult'
	);
	$users_id = isset($_COOKIE['users_id']) ? $_COOKIE['users_id'] : 0;
	if ($users_id){
		$params = array(
			'where' => array(
				'id' => $users_id,
			)
		);
		$users = db_get('testing_users',$params);
		if (!empty($users)){
			$status_id = $users[0]['status_id'];
			$response = array(
				'id' => $users[0]['id'],
				'uid' => $users[0]['uid'],
			);
		}
	}
	if (!$exception){
		if ($status_id) $url = $urls[$status_id];
	} else {
		if (!$status_id){
			$url = $urls[$status_id];
		} elseif ($status_id != $exception){
			$url = $urls[$status_id];
		}
	}
	if (empty($url)){
		return $response;
	} else {
		header('Location: '.$url.'.php');
		exit;
	}
}

function calc_percent($all = 1, $correct){
	return ceil($correct * 100 / $all);
}

// Отправка отчетов
function send_mail($sendData = array()){
	global $id_projects;
	$site = $sendData['site'];
	$setFrom = 'Сайт работы в фитнесе';
	$Subject = $sendData['Subject'].'. Работа в фитнес индустрии';
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->isSMTP();
	$mail->Host = 'smtp.yandex.ru';
	$mail->SMTPAuth = true;
	$mail->Username = 'support@praktika.fit';
	$mail->Password = 'Y94G55K04wXxOaDkiySi';
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPKeepAlive = true;
	$mail->Port = 465;
	$mail->charSet = 'utf-8';
	$mail->Encoding = 'base64';
	$mail->Subject = '=?UTF-8?B?'.base64_encode($Subject).'?=';
	$mail->setFrom('support@praktika.fit', '=?UTF-8?B?'.base64_encode($setFrom).'?=');
	foreach ($sendData['recipient'] as $key => $val){
		$mail->addAddress($val);
	}
	$htmlBody=file_get_contents('/var/www/p'.$id_projects.'/templates/mail.php');
	$htmlBody=str_replace("#CONTENT#",$sendData['htmlBody'],$htmlBody);
	$htmlBody=str_replace("#SITENAME#",$site['domain'],$htmlBody);
	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	$mail->send();
	$mail->ClearAddresses();
	return true;
}

function mb_ucfirst($string) {
	$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
	return $string;
}

function cleardata($text = '', $flag = FILTER_SANITIZE_STRING){
	$text = filter_var($text,$flag);
	$text = (string)$text;
	$text = preg_replace('/^ +| +$|( ) +/m', '$1', $text);
	return trim($text);
}

// Формирование рейтинга
function get_rating($question_completed = 0, $question_correct = 0){
	$ratestr = '';
	$rating = ($question_completed) ? ceil($question_correct * 5 / $question_completed) : 0;
	for ($rate=1;$rate<=5;$rate++){
		$prev = $rate - 1;
		if ($rating >= $rate) {
			$ratestr .= '<div class="star full"></div>';
		} elseif ($rating > $prev && $rating < $rate) {
			$ratestr .= '<div class="star half"></div>';
		} else {
			$ratestr .= '<div class="star off"></div>';
		}
	}
	return $ratestr;
}

// Список options
function get_option($arr = array(), $id = 0){
	$option = '';
	foreach ($arr as $key => $val){
		$option .= '<option value="'.$key.'"';
		if ($id == $key) $option .= ' selected';
		$option .= '>'.$val.'</option>';
	}
	return $option;
}
function getuniquestring($length = 5){
	$str = '';
	$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','y','z',  
	'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T','U','V','X','Y','Z',  
	'1','2','3','4','5','6','7','8','9','0');  
	for ($i=0;$i<$length;$i++){
		$index = rand(0, count($arr) - 1);
		$str .= $arr[$index];  
	}
	return $str;
}

function get_code($length = 64){
	$str = '';
	$arr = array('a','b','c','d','e','f','g','h','j','k','m','n','p','r','s','t','u','v','x','y','z',  
	'A','B','C','D','E','F','G','H','J','K','L','M','N','P','R','S','T','U','V','X','Y','Z',  
	'2','3','4','5','6','7','8','9');  
	for ($i=0;$i<$length;$i++){
		$index = rand(0, count($arr) - 1);
		$str .= $arr[$index];  
	}
	return $str;
}

function getuid($length = 5){
	global $official_words;
	for ($i=0; $i< 100; $i++){
		$uid = getuniquestring($length);
		if (is_numeric($uid)){
			continue;
		} else {
			$official_word = false;
			foreach ($official_words as $key => $word){
				$pos = stripos($uid,$word);
				if ($pos !== false){
					$official_word = true;
				}
			}
			if ($official_word) continue;
		}
		$params = array(
			'where' => array(
				'uid' => array(
					'val' => "LIKE '".$uid."'"
				)
			),
			'count' => 1
		);
		$cnt = db_get('testing_users',$params);
		if (!$cnt) break;
	}
	return $uid;
}

function phoneToSite($phone = ''){
	if (empty($phone)) return '';
	$phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
	$code = substr($phone,strlen($phone) - 10,3);
	$num1 = substr($phone,strlen($phone) - 7,3);
	$num2 = substr($phone,strlen($phone) - 4,2);
	$num3 = substr($phone,strlen($phone) - 2,2);
	$phone = '+7 ('.$code.')'.' '.$num1.'-'.$num2.'-'.$num3;
	return $phone;
}

function phoneToDB($phone = ''){
	if (empty($phone)) return '';
	$phone = preg_replace('/[^0-9]/', '', $phone);
	$phone = substr($phone,-10);
	return $phone;
}

function userip(){
	$ipaddr = $_SERVER["REMOTE_ADDR"];
	if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
		$ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
		$ipaddr = $_SERVER['HTTP_CLIENT_IP'];
	$pos = stripos($ipaddr,",");
	if ($pos === false){
		$ip = $ipaddr;
	} else {
		$ipline = explode(",",$ipaddr);
		$ip = $ipline[0];
	}
	return $ip;
}

function diff_days($date1, $date2){
	$first = date_create($date1);
	$second = date_create($date2);
	$interval = date_diff($second, $first);
	return $interval->format('%a');
}

function get_age($birthday){
	$diff = date('Ymd') - date('Ymd', strtotime($birthday));
	return substr($diff, 0, -4);
}

function get_age_str($year){
	$data = array('год', 'года', 'лет');
	$rest = array($year % 10, $year % 100);
	if($rest[1] > 10 && $rest[1] < 20) {
		return $data[2];
	} elseif ($rest[0] > 1 && $rest[0] < 5) {
		return $data[1];
	} else if ($rest[0] == 1) {
		return $data[0];
	}
	return $data[2];
}

function getlang($country = 'ru'){
	global $id_projects;
	$countryfile = '/var/www/p'.$id_projects.'/js/country.json';
	$countryjson = file_get_contents($countryfile);
	$countries = json_decode($countryjson);
	foreach ($countries as $lang => $carr){
		if (in_array($country,$carr)) return $lang;
	}
	return 'ru';
}

function getcountry($ip = ''){
	$country = '';
	if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])){
		$country = strtolower(trim($_SERVER["HTTP_CF_IPCOUNTRY"]));
	} elseif (filter_var($ip, FILTER_VALIDATE_IP)) {
		$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
		$country = strtolower(@$ipdat->geoplugin_countryCode);
	}
	return $country;
}
?>