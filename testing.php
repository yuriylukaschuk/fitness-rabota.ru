<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$userdata = login($exception = 2);
$msg = '';
$currtime = time();
$params = array(
	'where' => array(
		'id' => $userdata['id'],
		'uid' => $userdata['uid']
	)
);
$usersList = db_get('testing_users',$params);
$program_id = $usersList[0]['program_id'];
$question_completed = $usersList[0]['question_completed'];
$username = trim(mb_ucfirst($usersList[0]['lname']).' '.mb_ucfirst($usersList[0]['fname']).' '.mb_ucfirst($usersList[0]['pname']));

if (isset($_POST['save'])){
	$correct_reply = 0;
	$replys = array();
	$form_id = $_POST['form_id'];
	$question_id = $_POST['question_id'];
	// Проверяем не было ли ответа ранее на поставленный вопрос
	unset($params);
	$params = array(
		'where' => array(
			'users_id' => $userdata['id'],
			'question_id' => $question_id
		),
		'count' => 1
	);
	$reply_exists = db_get('testing',$params);
	// Ответ принимается, если ранее не было
	if (!$reply_exists){
		if ($form_id == 1){
			$reply_id = isset($_POST['reply_id']) ? $_POST['reply_id'] : 0;
			if ($reply_id){
				$replys = array($reply_id);
			} else {
				$err = 'emptyreply';
			}
		} elseif ($form_id == 3) {
			$replys = isset($_POST['reply_id']) ? $_POST['reply_id'] : array();
			if (empty($replys)) $err = 'emptyreply';
		}
		if (empty($err)){
			if ($form_id == 1 || $form_id == 3){
				unset($params);
				$params = array(
					'where' => array(
						'question_id' => $question_id
					)
				);
				$replyList = db_get('testing_reply',$params);
				$reply_all = $reply_correct = $reply_users = 0;
				foreach ($replyList as $key => $val){
					if (in_array($val['id'],$replys)) $reply_all++;
					if ($val['is_correct']) $reply_correct++;
					if (in_array($val['id'],$replys) && $val['is_correct']) $reply_users++;
				}
				if ($reply_correct == $reply_all && $reply_users == $reply_all) $correct_reply = 1;
			}
			unset($params);
			$params = array();
			if ($form_id == 1){
				$params[] = array(
					'users_id' => $userdata['id'],
					'question_id' => $question_id,
					'reply_id' => $reply_id
				);
			} elseif ($form_id == 3){
				foreach ($replys as $key => $val){
					$params[] = array(
						'users_id' => $userdata['id'],
						'question_id' => $question_id,
						'reply_id' => $val
					);
				}
			}
			db_add('testing',$params);
			unset($params);
			$question_completed = $question_completed + 1;
			$params[0] = array(
				'where' => array(
					'id' => $userdata['id']
				),
				'set' => array(
					'question_completed' => $question_completed
				)
			);
			if ($correct_reply){
				$params[0]['increment']['question_correct'] = array(
					'value' => 1,
					'op' => '+'
				);
			}
			db_update('testing_users',$params);
		}
	}
}

$question_count = 0;
unset($params);
$params = array(
	'where' => array(
		'program_id' => $program_id,
		'status_id' => 1
	),
	'orderby' => array(
		'npp' => 'ASC'
	)
);
$tqList = db_get('testing_question',$params);
foreach ($tqList as $key => $val){
	$question[$val['id']] = array(
		'form_id' => $val['form_id'],
		'npp' => $val['npp'],
		'name' => $val['name']
	);
	$question_count++;
}
if ($question_completed < $question_count){
	// Программа обучения
	unset($params);
	$params = array(
		'where' => array(
			'id' => $program_id
		)
	);
	$pList = db_get('program',$params);
	$program = $pList[0]['name'];

	// Формируем перечень не отвеченных ответов
	$questions = array();
	unset($params);
	$params = array(
		'where' => array(
			'users_id' => $userdata['id']
		)
	);
	$tList = db_get('testing',$params);
	if (!empty($tList)){
		foreach ($tList as $key => $val){
			unset($question[$val['question_id']]);
		}
	}
	$new_question = false;
	foreach ($question as $question_id => $questionData){
		if (!$new_question){
			$reply = '';
			$new_question = true;
			$form_id = $questionData['form_id'];
			$npp = $questionData['npp'];
			$question = $questionData['name'];
			unset($params);
			$params = array(
				'where' => array(
					'question_id' => $question_id
				),
				'orderby' => array(
					'npp' => 'ASC'
				)
			);
			$replyList = db_get('testing_reply',$params);
			foreach ($replyList as $key => $val){
				if ($form_id == 1) {
					$reply .= '<div class="form-item form-type-radio">
						<input class="radio_input" type="radio" id="reply'.$val['id'].'" name="reply_id" value="'.$val['id'].'">
						<label class="radio_label" for="reply'.$val['id'].'">'.$val['name'].'</label>
					</div>';
				} else {
					$reply .= '<div class="form-item form-type-checkbox">
						<input class="checkbox_input" type="checkbox" id="reply'.$val['id'].'" name="reply_id['.$val['id'].']" value="'.$val['id'].'">
						<label for="reply'.$val['id'].'">'.$val['name'].'</label>
					</div>';
				}
			}
			$content=file_get_contents("templates/testing.php");
			$content=str_replace("#PROGRAM#",$program,$content);
			$content=str_replace("#QUESTION_ID#",$question_id,$content);
			$content=str_replace("#FORM_ID#",$form_id,$content);
			$content=str_replace("#NPP#",$npp,$content);
			$content=str_replace("#QUESTION#",$question,$content);
			$content=str_replace("#QUESTION_COUNT#",$question_count,$content);
			$content=str_replace("#REPLY#",$reply,$content);
		}
	}
} else {
	unset($params);
	$params[0] = array(
		'where' => array(
			'id' => $userdata['id']
		),
		'set' => array(
			'status_id' => 3
		)
	);
	db_update('testing_users',$params);
	header('Location: testresult.php');
	exit;
}

if (!empty($err)){
	$msg = '<div class="error bold">'.$errs[$err].'</div>';
}

$scc = '';
$page = 'testing';
$action_file = $page.'.php';
$title = 'Работа в фитнесе. Тестирование по программе '.$program;
$keywords = 'проверка, тестирование, специлисты, фитнес, фитнес клуб, работа, Москва, высокая зарплата, тренер, менеджер';
$test = $program.'а'.($program_id == 5 ? ' отдела продаж' : '').' фитнес клуба';
$description = 'Тест для '.$test.'. '.$question;

$out=file_get_contents("templates/out.php");
$out=str_replace("#KEYWORDS#",$keywords,$out);
$out=str_replace("#DESCRIPTION#",$description,$out);
$out=str_replace("#CONTENT#",$content,$out);
$out=str_replace("#CSS#",$scc,$out);
$out=str_replace("#TITLE#",$title,$out);
$out=str_replace("#USERNAME#",$username,$out);
$out=str_replace("#SCRIPT#",$script,$out);
$out=str_replace("#MSG#",$msg,$out);
$out=str_replace("#CURRTIME#",$currtime,$out);
$out=str_replace("#ACTION_FILE#",$action_file,$out);
echo $out;
?>
