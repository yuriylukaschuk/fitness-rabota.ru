<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

login($exception = 0);
$currtime = time();
$site = get_siteinfo();

if (isset($_POST['remember'])){
	setcookie('users_id', 0, $currtime - 1, '/', $site['host']);
	setcookie('username', '', $currtime - 1, '/', $site['host']);
	$username = cleardata(strtolower($_POST['username']),FILTER_VALIDATE_EMAIL);
	$captchacode = cleardata($_POST['captchacode']);
	if (empty($username)) {
		$err = 'badusername';
		$field = 'username';
	} elseif (strlen($username) > 64) {
		$err = 'badusernamelenght';
		$field = 'username';
	} elseif (strtolower($_COOKIE["captcha"]) != strtolower($captchacode)){
		$err = 'badcaptchacode';
		$field = 'captchacode';
	} else {
		// Проверяем наличие в базе зарегистрированного username
		unset($params);
		$params = array(
			'where' => array(
				'username' => $username
			)
		);
		$usersList = db_get('testing_users',$params);
		if (empty($usersList)){
			$err = 'usernamenotexist';
			$field = 'username';
		}
	}
	if (empty($err)) {
		$status_id = 4; // Восстановление пароля
		// Отправляем ссылку для верификации E-mail
		$verify_form = 'recovery';
		$verify_code = getuniquestring(64);
		$link = $site['domain'].'/username='.$username.'&form='.$verify_form.'&status_id='.$usersList[0]['status_id'].'&code='.$verify_code;
		$htmlTitle = 'Восстановление пароля';
		$htmlBody = 'Здравствуйте.<br><br>Для восстановления пароля входа на сайт нажмите кнопку &laquo;Восстановить пароль&raquo;<br>
		После перехода на сайт введите новый пароль.<br><br><a href="'.$link.'" class="btn btn-green">Восстановить пароль</a>';
		unset($params);
		$params = array(
			'Subject' => $htmlTitle,
			'site' => $site,
			'recipient' => array($username),
			'htmlBody' => $htmlBody
		);
		if (send_mail($params)){
			unset($params);
			$params[0] = array(
				'where' => array(
					'id' => $usersList[0]['id']
				),
				'set' => array(
					'status_id' => $status_id
				)
			);
			db_update('testing_users',$params);

			unset($params);
			$params = array(
				'where' => array(
					'users_id' => $usersList[0]['id'],
					'username' => $username
				),
				'count' => 1
			);
			$verify_exists = db_get('email_verify',$params);
			if ($verify_exists){
				unset($params);
				$params[0] = array(
					'where' => array(
						'users_id' => $usersList[0]['id'],
						'username' => $username
					),
					'set' => array(
						'form' => $verify_form,
						'code' => $verify_code,
						'timeadd' => $currtime
					)
				);
				db_update('email_verify',$params);
			} else {
				unset($params);
				$params[] = array(
					'users_id' => $usersList[0]['id'],
					'username' => $username,
					'form' => $verify_form,
					'code' => $verify_code,
					'timeadd' => $currtime
				);
				db_add('email_verify', $params);
			}
		}
		unset($params);
		setcookie('users_id', $usersList[0]['id'], time() + 31536000, '/', $site['host']);
		setcookie('username', $username, time() + 31536000, '/', $site['host']);
		header('Location: /recovery.php');
		exit;
	}
}

if (!empty($err)) {
	$filemsg = ($err == 'successremember') ? 'success' : 'error';
	$msg = file_get_contents("templates/".$filemsg.".php");
	$msg = str_replace("#MSG#",$errs[$err],$msg);
	$script = '<script type="text/javascript">$(function(){$("'.$field.'").html(\''.$msg.'\');$("input[name='.$field.']").focus();})</script>';
}

$captcha = get_code(5);
setcookie('captcha', $captcha, time()+86400, '/', $site['host']);

$page = 'remember';
$action_file = $page.'.php';
$scc = '<link rel="stylesheet" href="/css/confirm.css?#CURRTIME#">';

$title = 'Работа в фитнесе. Восстановление пароля для входа на сайт';
$keywords = 'фитнес, фитнес клуб, работа, Москва, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж';
$description = 'Престижная работа и занятия спортом. Трудоустройство в фитнесе. Обучение и работа по результатам тестирования';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$action_file);
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#USERNAME#",$username,$out);
$out=str_replace("#PASSWORD#",$password,$out);
$out=str_replace("#CAPTCHA#",$captcha,$out);
$out=str_replace("#SCRIPT#",$script,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;
?>