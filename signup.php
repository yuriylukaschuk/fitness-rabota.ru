<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$userdata = login($exception = 2);
$currtime = time();
$site = get_siteinfo();
$params = array(
	'where' => array(
		'id' => $userdata['id'],
		'uid' => $userdata['uid']
	)
);
$usersList = db_get('testing_users',$params);
$email = $usersList[0]['username'];
$username = trim(mb_ucfirst($usersList[0]['lname']).' '.mb_ucfirst($usersList[0]['fname']).' '.mb_ucfirst($usersList[0]['pname']));

if (isset($_POST['signup'])){
	$password = cleardata($_POST['password']);
	$username = cleardata(strtolower($_POST['username']),FILTER_VALIDATE_EMAIL);
	$captchacode = cleardata($_POST['captchacode']);
	if (empty($password)) {
		$err = 'passisempty';
		$field = 'password';
	} elseif (strlen($password) < 5 || strlen($password) > 20) {
		$err = 'badpasswordlenght';
		$field = 'password';
	} elseif (strtolower($_COOKIE["captcha"]) != strtolower($captchacode)){
		$err = 'badcaptchacode';
		$field = 'captchacode';
	} else {
		unset($params);
		$status_id = 1; // Состояние учетной записи - активно
		$params[0] = array(
			'where' => array(
				'id' => $userdata['id'],
				'uid' => array(
					'val' => "LIKE '".$userdata['uid']."'"
				)
			),
			'set' => array(
				'status_id' => $status_id, 
				'lastlogintime' => $currtime,
				'lastloginip' => $userip,
				'password' => password_hash($password, PASSWORD_DEFAULT)
			)
		);
		db_update('testing_users',$params);
		setcookie('users_id', $userdata['id'], time() + 31536000, '/', $site['host']);
		setcookie('username', $email, time() + 31536000, '/', $site['host']);
		$htmlTitle = 'Учетные данные';
		$htmlBody = 'Здравствуйте, '.$username.'.<br><br>
		Для прохождения тестирования используйте:<br><br>
		Сайт: '.$site['domain'].'<br>
		Имя пользователя: '.$email.'<br>
		Пароль для входа: '.$password;
		unset($params);
		$params = array(
			'Subject' => $htmlTitle,
			'site' => $site,
			'recipient' => array($email),
			'htmlBody' => $htmlBody
		);
		if (send_mail($params)){
			header("Location: /");
			exit;
		}
	}
}

if (!empty($err)) {
	$filemsg = ($err == 'successregistered') ? 'success' : 'error';
	$msg = file_get_contents("templates/".$filemsg.".php");
	$msg = str_replace("#MSG#",$errs[$err],$msg);
	$script = '<script type="text/javascript">$(function(){$("'.$field.'").html(\''.$msg.'\');$("input[name='.$field.']").focus();})</script>';
}


$captcha = get_code(5);
setcookie('captcha', $captcha, time()+86400, '/', $site['host']);

$page = 'signup';
$action_file = $page.'.php';
$scc = '<link rel="stylesheet" href="/css/confirm.css?#CURRTIME#">';

$title = 'Работа в фитнесе. Регистрация на сайте';
$keywords = 'фитнес, фитнес клуб, работа, Москва, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж, трудоустройство';
$description = 'Работа и обучение по результатам тестирования';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$action_file);
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#USERNAME#",$username,$out);
$out=str_replace("#EMAIL#",$email,$out);
$out=str_replace("#PASSWORD#",$password,$out);
$out=str_replace("#REPEAT#",$repeat,$out);
$out=str_replace("#CAPTCHA#",$captcha,$out);
$out=str_replace("#SCRIPT#",$script,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;
?>