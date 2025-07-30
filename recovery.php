<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

login($exception = 4);
$currtime = time();
$site = get_siteinfo();
$status_id = 5;

if (isset($_POST['savepassword'])){
	$password = cleardata($_POST['password']);
	$username = cleardata(strtolower($_POST['username']),FILTER_VALIDATE_EMAIL);
	if (empty($username)) {
		$err = 'notusername';
		$field = 'username';
	} elseif (strlen($username) > 64) {
		$err = 'badusernamelenght';
		$field = 'username';
	} elseif (empty($password)) {
		$err = 'passisempty';
		$field = 'password';
	} elseif (strlen($password) < 5 || strlen($password) > 20) {
		$err = 'badpasswordlenght';
		$field = 'password';
	} else {
		// Проверяем наличие в базе зарегистрированного username
		$params = array(
			'where' => array(
				'username' => array(
					'val' => "LIKE '".$username."'"
				)
			)
		);
		$usersList = db_get('testing_users',$params);
		if (empty($usersList)){
			$err = 'usernameexist';
			$field = 'username';
		} else {
			$status_id = $usersList[0]['status_id'];
			$dateadd = $usersList[0]['dateadd'];
			$control_time = $dateadd + 3 * $secInDay;
			$dateentry = date('d.m.Y',$control_time);
		}
	}
	if (empty($err)) {
		$status_id = 1; // Состояние учетной записи - активно
		unset($params);
		$params[] = array(
			'where' => array(
				'id' => $usersList[0]['id']
			),
			'set' => array(
				'status_id' => $status_id,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'lastlogintime' => $currtime,
				'lastloginip' => userip()
			)
		);
		db_update('testing_users',$params);
		setcookie('users_id', $usersList[0]['id'], time() + 31536000, '/', $site['host']);
		setcookie('username', $username, time() + 31536000, '/', $site['host']);
		$htmlTitle = 'Учетные данные';
		$htmlBody = 'Здравствуйте, '.trim(mb_ucfirst($usersList[0]['lname']).' '.mb_ucfirst($usersList[0]['fname']).' '.mb_ucfirst($usersList[0]['pname'])).'.<br><br>
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

// Если пользователь проходит подтверждение адреса электронной почты
if (isset($_GET['form']) && $_GET['form'] == 'recovery'){
	$username = cleardata(strtolower($_GET['username']),FILTER_VALIDATE_EMAIL);
	$content=file_get_contents("templates/recovery.php");
} else {
	$content=file_get_contents("templates/remembered.php");
}

if (!empty($err)) {
	$filemsg = ($err == 'successremember') ? 'success' : 'error';
	$msg = file_get_contents("templates/".$filemsg.".php");
	$msg = str_replace("#MSG#",$errs[$err],$msg);
	$script = '<script type="text/javascript">$(function(){$("'.$field.'").html(\''.$msg.'\');$("input[name='.$field.']").focus();})</script>';
}

$page = 'recovery';
$action_file = $page.'.php';
$scc = '<link rel="stylesheet" href="/css/confirm.css?#CURRTIME#">';

$title = 'Работа в фитнесе. Восстановление пароля для входа на сайт';
$keywords = 'фитнес, фитнес клуб, работа, Москва, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж';
$description = 'Трудоустройство менеджеров и тренеров фитнеса. Предоставляем обучение и работу по результатам тестирования';

$out=file_get_contents("templates/out.php");
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#USERNAME#",$username,$out);
$out=str_replace("#STATUS_ID#",$status_id,$out);
$out=str_replace("#SCRIPT#",$script,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;
?>