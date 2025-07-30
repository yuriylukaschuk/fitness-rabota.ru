<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

login($exception = 0);
$currtime = time();
$site = get_siteinfo();
$lname = $fname = $pname = $phone = $err = '';

if (isset($_POST['program_id'])){
	$program_id = $_POST['program_id'];
} elseif (isset($_GET['program_id'])){
	$program_id = $_GET['program_id'];
} else {
	$program_id = 5;
}
/*
echo '<pre>';
print_r($_POST);
echo '</pre>';
*/
if (isset($_POST['manager']) || isset($_POST['trener'])){
	$program_id = isset($_POST['manager']) ? 5 : 6;
}

if (isset($_POST['send'])){
	$fname = cleardata($_POST['fname']);
	$day = ($_POST['day'] < 10) ? '0'.$_POST['day'] : $_POST['day'];
	$month = ($_POST['month'] < 10) ? '0'.$_POST['month'] : $_POST['month'];
	$birthday = $day.'.'.$month.'.'.$_POST['year'];
	$lvl_id = $_POST['lvl_id'];
	$education_id = $_POST['education_id'];
	$experience_id = $_POST['experience_id'];
	$metro_id = $_POST['metro_id'];
	$phone = (string)phoneToDB($_POST['phone']);
	$username = cleardata(strtolower($_POST['username']),FILTER_VALIDATE_EMAIL);
	$connection_id = $_POST['connection_id'];
	if (empty($fname)) {
		$err = 'fnameisempty';
		$field = 'fname';
	} elseif (empty($phone)) {
		$err = 'phoneisempty';
		$field = 'phone';
	} elseif (strlen($phone) != 10 || substr($phone,0,1) !== '9') {
		$err = 'phonebad';
		$field = 'phone';
	} elseif (strlen($username) > 64) {
		$err = 'badusernamelenght';
		$field = 'username';
	} elseif (!empty($username)) {
		// Проверяем наличие в базе зарегистрированного username
		unset($params);
		$params = array(
			'where' => array(
				'username' => array(
					'val' => "LIKE '".$username."'"
				)
			)
		);
		$usersList = db_get('testing_users',$params);
		if (!empty($usersList)){
			$err = 'usernameexist';
			$field = 'username';
		}
	}
	if (empty($err)) {
		$userip = userip();
		$uid = getuid();
		$status_id = 1; // Требуется подтверждение прохождения тестирования
		$country = getcountry($userip);
		$password = password_hash($password, PASSWORD_DEFAULT);
		unset($params);
		$params[] = array(
			'uid' => $uid,
			'program_id' => $program_id,
			'status_id' => $status_id,
			'lvl_id' => $lvl_id,
			'education_id' => $education_id,
			'experience_id' => $experience_id,
			'metro_id' => $metro_id,
			'connection_id' => $connection_id,
			'source_id' => 1, // Заявка с сайта
			'username' => cleardata(strtolower($_POST['username']),FILTER_VALIDATE_EMAIL),
			'fname' => $fname,
			'birthday' => $birthday,
			'phone' => $phone,
			'lastlogintime' => $currtime,
			'lastloginip' => $userip,
			'hostreg' => $site['host'],
			'country' => $country,
			'dateadd' => $currtime
		);
		$users_id = db_add('testing_users',$params);
		setcookie('users_id', $users_id, time() + 31536000, '/', $site['host']);
		header("Location: /testing.php");
		exit;
	}
}

$day_option = $month_option = $year_option = $education_option = $experience_option = $metro_option = $lvl_option = $connection_option = '';

$lvl = db_get('lvl');
foreach ($lvl as $key => $val){
	$lvl_option .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
}
$education = db_get('education');
foreach ($education as $key => $val){
	$education_option .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
}
$experience = db_get('experience');
foreach ($experience as $key => $val){
	$experience_option .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
}
$connection = db_get('connection');
foreach ($connection as $key => $val){
	$connection_option .= '<option value="'.$val['id'].'"';
	if (!$key) $connection_option .= ' selected';
	$connection_option .= '>'.$val['name'].'</option>';
}
unset($params);
$params = array(
	'orderby' => array(
		'name' => 'ASC'
	)
);
$metro = db_get('metro', $params);
foreach ($metro as $key => $val){
	$metro_option .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
}
for ($day=1;$day<=31;$day++){
	$day_option .= '<option value="'.$day.'">'.$day.'</option>';
}
for ($month=1;$month<=12;$month++){
	$month_option .= '<option value="'.$month.'">'.$mon[$month-1].'</option>';
}
$currYear = date('Y');
$YearStart = $currYear - 16;
$YearStop = $YearStart - 64;
for ($year=$YearStart;$year>=$YearStop;$year--){
	$year_option .= '<option value="'.$year.'">'.$year.'</option>';
}

if (!empty($err)) {
	$msg = '<div class="error bold">'.$errs[$err].'</div>';
	$script = '<script type="text/javascript">$(function(){$("'.$field.'").html(\''.$msg.'\');$("input[name='.$field.']").focus();})</script>';
}

$page = 'survey';
$action_file = $page.'.php';
$scc = '<link rel="stylesheet" href="/css/body.css?#CURRTIME#">';

$title = 'Работа в фитнесе. Анкетирование соискателя';
$keywords = 'фитнес, фитнес клуб, устройство, работа, Москва, молодых, специалист, высокая зарплата, обучение, тренер, фитнес тренер, менеджер отдела продаж, трудоустройство';
$description = 'Заполните анкету, укажите образование, опыт, стаж работы, станцию метро';

$out=file_get_contents("templates/out.php");
$content=file_get_contents("templates/".$page.".php");
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#LNAME#",$lname,$out);
$out=str_replace("#FNAME#",$fname,$out);
$out=str_replace("#PNAME#",$pname,$out);
$out=str_replace("#EDUCATION_OPTION#",$education_option,$out);
$out=str_replace("#EXPERIENCE_OPTION#",$experience_option,$out);
$out=str_replace("#LVL_OPTION#",$lvl_option,$out);
$out=str_replace("#METRO_OPTION#",$metro_option,$out);
$out=str_replace("#CONNECTION_OPTION#",$connection_option,$out);
$out=str_replace("#DAY_OPTION#",$day_option,$out);
$out=str_replace("#MONTH_OPTION#",$month_option,$out);
$out=str_replace("#YEAR_OPTION#",$year_option,$out);
$out=str_replace("#PHONE#",$phone,$out);
$out=str_replace("#USERNAME#",$username,$out);
$out=str_replace("#PROGRAM_ID#",$program_id,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#SCRIPT#",$script,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;

?>