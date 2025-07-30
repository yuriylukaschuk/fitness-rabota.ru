<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$userdata = login($exception = 3);
$page = 'testresult';
$action_file = $page.'.php';
$currtime = time();
$site = get_siteinfo();

unset($params);
$params = array(
	'where' => array(
		'id' => $userdata['id'],
		'uid' => $userdata['uid']
	)
);

$usersList = db_get('testing_users',$params);
$username = $usersList[0]['username'];
$program_id = $usersList[0]['program_id'];
$ready_id = $usersList[0]['ready_id'];
$after_id = $usersList[0]['after_id'];
$question_completed = $usersList[0]['question_completed'];
$question_correct = $usersList[0]['question_correct'];
$lname = mb_ucfirst($usersList[0]['lname']);
$fname = mb_ucfirst($usersList[0]['fname']);
$pname = mb_ucfirst($usersList[0]['pname']);
$rating = get_rating($question_completed, $question_correct);
$percent = calc_percent($question_completed, $question_correct);

if (isset($_POST['send'])){
	$ready_id = (isset($_POST['ready_id'])) ? $_POST['ready_id'] : 0;
	$after_id = (isset($_POST['after_id'])) ? $_POST['after_id'] : 0;
	unset($params);
	$params[] = array(
		'where' => array(
			'id' => $userdata['id'],
			'uid' => $userdata['uid']
		),
		'set' => array(
			'ready_id' => $ready_id,
			'after_id' => $after_id
		)
	);
	db_update('testing_users',$params);
	if ($after_id || $ready_id == 1){
		$lvlList = db_get('lvl');
		foreach ($lvlList as $key => $val){
			if ($val['id'] == $usersList[0]['lvl_id']) $lvl = $val['name'];
		}
		$educationList = db_get('education');
		foreach ($educationList as $key => $val){
			if ($val['id'] == $usersList[0]['education_id']) $education = $val['name'];
		}
		$experienceList = db_get('experience');
		foreach ($experienceList as $key => $val){
			if ($val['id'] == $usersList[0]['experience_id']) $experience = $val['name'];
		}
		$metroList = db_get('metro');
		foreach ($metroList as $key => $val){
			if ($val['id'] == $usersList[0]['metro_id']) $metro = $val['name'];
		}

		if ($program_id == 5){
			if ($percent <= 25){
				$htmlTitle = 'Соискатель с низкими показателями';
				$htmlBody = 'Поступил запрос от соискателя с низкими показателями.<br>Желает пройти обучение<br><br>';
			} else {
				if ($percent > 25 && $percent <= 50){
					$htmlTitle = 'Соискатель с удовлетворительными показателями';
					$htmlBody = 'Поступил запрос от соискателя с удовлетворительными показателями.<br><br>';
				} elseif ($percent > 50 && $percent <= 75){
					$htmlTitle = 'Соискатель с достаточно высокими показателями';
					$htmlBody = 'Поступил запрос от соискателя с достаточно высокими показателями.<br><br>';
				} else {
					$htmlTitle = 'Соискатель с высокими показателями';
					$htmlBody = 'Поступил запрос от соискателя с высокими показателями.<br><br>';
				}
			}
		} elseif ($program_id == 6){
			if ($percent <= 20){
				$htmlTitle = 'Соискатель с низкими показателями';
				$htmlBody = 'Поступил запрос от соискателя с низкими показателями.<br>Желает пройти обучение<br><br>';
			} else {
				$lvl = 'high';
				if ($percent > 20 && $percent <= 60){
					$htmlTitle = 'Соискатель с удовлетворительными показателями';
					$htmlBody = 'Поступил запрос от соискателя с удовлетворительными показателями.<br><br>';
				} elseif ($percent > 60 && $percent <= 80){
					$htmlTitle = 'Соискатель с достаточно высокими показателями';
					$htmlBody = 'Поступил запрос от соискателя с достаточно высокими показателями.<br><br>';
				} else {
					$htmlTitle = 'Соискатель с высокими показателями';
					$htmlBody = 'Поступил запрос от соискателя с высокими показателями.<br><br>';
				}
			}
		}

		$htmlBody .= 'Из <b>'.$question_completed.'</b> вопросов получен правильный ответ на <b>'.$question_correct.'</b><br><br>
		<table border="0">
			<tr><td>Фамилия Имя Отчестов</td><td>'.trim($lname.' '.$fname.' '.$pname).'</td></tr>
			<tr><td>Дата рождения</td><td>'.$usersList[0]['birthday'].'</td></tr>
			<tr><td>Образование</td><td>'.$education.'</td></tr>
			<tr><td>Заявленный уровень подготовки</td><td>'.$lvl.'</td></tr>
			<tr><td>Стаж работы по профилю</td><td>'.$experience.'</td></tr>
			<tr><td>Ближайшая станция метро</td><td>'.$metro.'</td></tr>';
		if ($after_id) {
			$htmlBody .= '<tr><td>Удобное время для связи</td><td>'.$after[$after_id].'</td></tr>';
		}
		$htmlBody .= '<tr><td>Телефон для связи</td><td><a href="tel:'.phoneToSite($usersList[0]['phone']).'">'.phoneToSite($usersList[0]['phone']).'</a></td></tr>
			<tr><td>E-mail</td><td><a href="mailto:'.$usersList[0]['username'].'">'.$usersList[0]['username'].'</a></td></tr>
		</table><br>';
		unset($params);
		$params = array(
			'Subject' => $htmlTitle,
			'site' => $site,
			'recipient' => $Recipients,
			'htmlBody' => $htmlBody
		);
		send_mail($params);
	}
	header("Location: /".$action_file);
	exit;
}

if ($ready_id || $after_id){
	if ($after_id || $ready_id == 1){
		$msg = 'С Вами обязательно свяжутся в течении суток в назначенное время';
	} elseif ($ready_id == 2){
		$msg = 'Мы получили информацию и передали результаты в клубы, имеющие вакансии. В случае положительного рассмотрения Вашей кандидатуры с Вами свяжутся в ближайшее время';
	}
	$result=file_get_contents("templates/relult.php");
	$result=str_replace("#MSG#",$msg,$result);
} else {
	$after_option = '';
	foreach ($after as $key => $val){
		$after_option .= '<option value="'.$key.'">'.$val.'</option>';
	}
	$ready_option = '';
	foreach ($ready as $key => $val){
		$ready_option .= '<option value="'.$key.'">'.$val.'</option>';
	}
	$applicant = trim($fname.' '.$pname);
	if ($program_id == 5){
		if ($percent <= 25){
			$lvl = 'low';
			$msg = 'Ваш уровень базовых знаний недостаточно высок для начала работы менеджером по продажам в фитнес клубе. Предлагаем пройти базовый курс обучения продажам в фитнес индустрии. В случае успешного окончания базового курса готовы предложить Вам работу менеджером по продажам в фитнес клубах г. Москвы';
		} else {
			$lvl = 'high';
			if ($percent > 25 && $percent <= 50){
				$msg = 'Ваш уровень базовых знаний удовлетворительный. Мы готовы пригласить Вас на собеседование. Для записи на собеседование обозначьте, пожалуйста, удобный интервал времени для звонка. С Вами свяжутся в течении суток после получения этого письма';
			} elseif ($percent > 50 && $percent <= 75){
				$msg = 'Ваш уровень базовых знаний достаточно высок для работы менеджером по продажам. Мы готовы пригласить Вас на собеседование. Для записи на собеседование обозначьте, пожалуйста, удобный интервал времени для звонка. С Вами свяжутся в течение суток после получения этого письма';
			} else {
				$msg = 'Ваш уровень базовых знаний полностью удовлетворяет требованиям для менеджера по продажам. Мы готовы предложить Вам работу в фитнес клубах г. Москвы после предварительного собеседования.  Для записи на собеседование обозначьте, пожалуйста, удобный интервал времени для звонка. С Вами свяжутся в течение суток после получения этого письма';
			}
		}
	} elseif ($program_id == 6){
		if ($percent <= 20){
			$lvl = 'low';
			$msg = 'Ваш уровень базовых знаний недостаточно высок для начала работы тренером. Предлагаем пройти базовый курс обучения для тренеров. В случае успешного окончания базового курса готовы предложить Вам работу тренером в фитнес клубах г. Москвы';
			$result=file_get_contents("templates/relult_low.php");
		} else {
			$lvl = 'high';
			if ($percent > 20 && $percent <= 60){
				$msg = 'Ваш уровень базовых знаний удовлетворительный. Мы готовы пригласить Вас на собеседование. Для записи на собеседование обозначьте, пожалуйста, удобный интервал времени для звонка. С Вами свяжутся в течении суток после получения этого письма';
			} elseif ($percent > 60 && $percent <= 80){
				$msg = 'Ваш уровень базовых знаний достаточно высок для работы тренером. Мы готовы пригласить Вас на собеседование. Для записи на собеседование обозначьте, пожалуйста, удобный интервал времени для звонка. С Вами свяжутся в течение суток после получения этого письма';
			} else {
				$msg = 'Ваш уровень базовых знаний полностью удовлетворяет требованиям для персонального тренера. Мы готовы предложить Вам работу в фитнес клубах г. Москвы после предварительного собеседования.  Для записи на собеседование обозначьте, пожалуйста, удобный интервал времени для звонка. С Вами свяжутся в течение суток после получения этого письма';
			}
		}
	}
	$result=file_get_contents("templates/relult_".$lvl.".php");
	$result=str_replace("#APPLICANT#",$applicant,$result);
	$result=str_replace("#AFTER_OPTION#",$after_option,$result);
	$result=str_replace("#READY_OPTION#",$ready_option,$result);
	$result=str_replace("#MSG#",$msg,$result);
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
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#QUESTION_COMPLETED#",$question_completed,$out);
$out=str_replace("#QUESTION_CORRECT#",$question_correct,$out);
$out=str_replace("#RATING#",$rating,$out);
$out=str_replace("#RESULT#",$result,$out);
$out=str_replace("#LNAME#",$lname,$out);
$out=str_replace("#FNAME#",$fname,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;

?>
