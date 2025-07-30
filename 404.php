<?php
require_once("cgi-bin/constants.php");
require_once("cgi-bin/_dbfuncs.php");
require_once("cgi-bin/_funcs.php");

$form = '';
$currtime = time();
$site = get_siteinfo();
$url = 'index.php';
$request = str_replace("/","",$_SERVER["REQUEST_URI"]);
$verify_form = array('signup','recovery');
foreach ($verify_form as $key => $val){
	$searchform = 'form='.$val;
	if (stripos($request,$searchform)){
		$form = $val;
	}
}
if (empty($form)){
	$url = 'linknotactive.php?form=unknown';
} else {
	$request_params = explode('&',$request);
	if (!empty($request_params)){
		foreach ($request_params as $key => $val){
			$params = explode('=',$val);
			if ($params[0] == 'username') $username = cleardata(strtolower($params[1]),FILTER_VALIDATE_EMAIL);
			if ($params[0] == 'code') $code = cleardata($params[1]);
			if ($params[0] == 'status_id') $status_id = cleardata($params[1]);
		}
		$params = array(
			'where' => array(
				'form' => array(
					'val' => "LIKE '".$form."'"
				),
				'username' => array(
					'val' => "LIKE '".$username."'"
				),
				'code' => array(
					'val' => "LIKE '".$code."'"
				)
			)
		);
		$verifyList = db_get('email_verify',$params);
		if (empty($verifyList)){
			$url = 'linknotactive.php?form='.$form;
		} else {
			$users_id = $verifyList[0]['users_id'];
			unset($params);
			$params[0] = array(
				'where' => array(
					'form' => array(
						'val' => "LIKE '".$form."'"
					),
					'username' => array(
						'val' => "LIKE '".$username."'"
					),
					'code' => array(
						'val' => "LIKE '".$code."'"
					)
				)
			);
			db_del('email_verify',$params);
			if ($form == 'recovery') {
				$status_id = 4;
				$url = 'recovery.php?form=recovery&status_id='.$status_id.'&username='.$username;
			} elseif ($form == 'signup'){
				$status_id = 2;
				$url = 'signup.php';
			}
			unset($params);
			$params[0] = array(
				'where' => array(
					'id' => $users_id,
					'username' => array(
						'val' => "LIKE '".$username."'"
					)
				),
				'set' => array(
					'status_id' => $status_id
				)
			);
			db_update('testing_users',$params);
			setcookie('users_id', $users_id, time() + 31536000, '/', $site['host']);
			setcookie('username', $username, time() + 31536000, '/', $site['host']);
		}
	}
}
header("Location: ".$site['domain'].'/'.$url);
exit;
?>