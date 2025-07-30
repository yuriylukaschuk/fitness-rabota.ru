<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

login($exception = 0);
$currtime = time();

$scc = '';
$page = 'index';
$action_file = 'survey.php';
$title = 'Работа в фитнесе. Главная страница';
$keywords = 'фитнес, фитнес клуб, работа, Москва, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж, трудоустройство';
$description = 'Престижная работа в фитнес индустрии. Требуются тренеры. Требуются менеджеры. Тестирование, собеседование, устройство на работу';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/index.php");
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;
?>