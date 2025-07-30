<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$userdata = login($exception = 3);
$currtime = time();

$params = array(
	'where' => array(
		'id' => $userdata['id'],
		'uid' => array(
			'val' => "LIKE '".$userdata['uid']."'"
		)
	)
);
$usersList = db_get('testing_users',$params);

$page = 'emailconfirm';
$scc = '<link rel="stylesheet" href="/css/confirm.css?#CURRTIME#">';

$title = 'Работа в фитнесе. Верификация адреса электронной почты';
$keywords = 'фитнес, фитнес клуб, устройство, работа, Москва, молодых, специалист, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж, трудоустройство';
$description = 'Подтвердите адрес электронной почты';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$page.".php");
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#LNAME#",$usersList[0]['lname'],$out);
$out=str_replace("#FNAME#",$usersList[0]['fname'],$out);
$out=str_replace("#USERNAME#",$usersList[0]['username'],$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
echo $out;

?>