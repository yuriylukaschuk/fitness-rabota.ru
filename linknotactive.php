<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$currtime = time();
$site = get_siteinfo();

$msg = 'Вами использована неизвестная для системы ссылка';
if (isset($_GET['form'])){
	switch ($_GET['form']){
		case 'signup': $msg = 'Регистрация на сайте не может быть подтверждена. Письмо, содержащее данную ссылку более не является актуальным, его можно удалить'; break;
		case 'recovery': $msg = 'Письмо, содержащее данную ссылку более не является актуальным, его можно удалить'; break;
		case 'unknown': $msg = 'Для продолжения работы выполните регистрацию на сайте либо свяжитесь с владельцами ресурса по указанному на сайт телефону'; break;
	}
}

$page = 'linknotactive';
$action_file = $page.'.php';
$title = 'Работа в фитнес индустрии';
$scc = '<link rel="stylesheet" href="/css/confirm.css?#CURRTIME#">';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$action_file);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#MENU#",$menu,$out);
$out=str_replace("#MSG#",$msg,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
echo $out;
?>