<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$userdata = login($exception = 1);
$page = 'testconfirm';
$action_file = $page.'.php';
$currtime = time();
$site = get_siteinfo();

$params = array(
	'where' => array(
		'id' => $userdata['id'],
		'uid' => $userdata['uid']
	)
);
$usersList = db_get('testing_users',$params);
$program_id = $usersList[0]['program_id'];
// Программа обучения
unset($params);
$params = array(
	'where' => array(
		'id' => $program_id
	)
);
$pList = db_get('program',$params);
$program = $pList[0]['name'];

if (isset($_POST['send'])){
	unset($params);
	$params[0] = array(
		'where' => array(
			'id' => $userdata['id'],
			'uid' => $userdata['uid']
		),
		'set' => array(
			'status_id' => 2 // Готов пройти тест
		)
	);
	db_update('testing_users',$params);
	header("Location: /testing.php");
	exit;
}

$scc = '<link rel="stylesheet" href="/css/body.css?#CURRTIME#">';
$title = 'Работа в фитнесе. Результаты тестирования';
$keywords = 'фитнес, фитнес клуб, работа, Москва, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж, трудоустройство';
$description = 'Престижная работа в фитнес индустрии. Результаты тестирование.';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$action_file);
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#PROGRAM#",$program,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;

?>