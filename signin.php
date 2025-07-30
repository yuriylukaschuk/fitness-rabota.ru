<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

login($exception = 2);
$currtime = time();

if (isset($_POST['signin'])){
	$username = cleardata(strtolower($_POST['username']));
	$password = cleardata($_POST['password']);
	if (empty($username)) {
		$err = 'badusername';
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
		$params = array(
			'where' => array(
				'username' => array(
					'val' => "LIKE '".$username."'"
				)
			)
		);
		$users = db_get('testing_users',$params);
		if (!empty($users) && password_verify($password, $users[0]['password'])){
			$userip = userip();
			unset($params);
			$params[] = array(
				'where' => array(
					'id' => $users[0]['id']
				),
				'set' => array(
					'lastlogintime' => $currtime,
					'lastloginip' => $userip
				)
			);
			db_update('testing_users',$params);
			setcookie('users_id', $users[0]['id'], time() + 31536000, '/', $site['host']);
			setcookie('username', $username, time() + 31536000, '/', $site['host']);
			switch ($users[0]['status_id']){
				case 1: $url = 'testing'; break;
				case 3: $url = 'emailconfirm'; break;
				case 4: $url = 'recovery'; break;
				case 5: $url = 'testresult'; break;
			}
			header('Location: '.$url.'.php');
			exit;
		} else {
			$err = 'badusernameorpassword';
			$field = 'username';
		}
	}
}

if (!empty($err)){
	$msg = '<div class="error bold">'.$errs[$err].'</div>';
	$script = '<script type="text/javascript">$(function(){$("'.$field.'").html(\''.$msg.'\');$("input[name='.$field.']").focus();})</script>';
}

$page = 'signin';
$action_file = $page.'.php';
$scc = '<link rel="stylesheet" href="/css/confirm.css?#CURRTIME#">';

$title = 'Работа в фитнесе. Вход на сайт';
$keywords = 'фитнес, фитнес клуб, работа, Москва, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж, трудоустройство';
$description = 'Проводим набор менеджеров и тренеров фитнеса. Предоставляем обучение и работу по результатам тестирования';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$action_file);
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#USERNAME#",$username,$out);
$out=str_replace("#PASSWORD#",$password,$out);
$out=str_replace("#SCRIPT#",$script,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;
?>