<?php
require_once("constants.php");
require_once("_dbfuncs.php");
require_once("_funcs.php");

$userdata = get_userdata();
$trueType = false;
$response = array();

if ($userdata['id']){
	$dataStr = file_get_contents("php://input");
	$jsonArray = json_decode($dataStr);
	if ($jsonArray->staff_id){
		if ($jsonArray->method == 'addavatar'){
			$dirname = '/var/www/p'.$id_projects.'/img/staff/';
			if (!is_dir($dirname)) mkdir($dirname,0777,true);
			$imgFile = $dirname.$jsonArray->staff_id;
			$file_img = $jsonArray->file_img;
			$data = explode(',', $file_img);
			foreach ($imgType as $key => $val){
				if (substr_count($data[0],$key)){
					$trueType = true;
					$base64 = base64_encode($file_img);
				}
				if ($trueType) file_put_contents($imgFile, $base64);
			}
			if ($trueType){
				$params = array(
					'imgFormat' => 'middle',
					'staff_id' => $jsonArray->staff_id,
					'prePath' => '../'
				);
				$img = getImage($params);
				$response = array(
					'act' => 'Удалить',
					'width' => $img['width'],
					'height' => $img['height'],
					'src' => $img['file']
				);
			}
		} elseif ($jsonArray->method == 'removeavatar') {
			$imgFile = '/var/www/p'.$id_projects.'/img/staff/'.$jsonArray->subject_id;
			if (file_exists($imgFile)) unlink($imgFile);
		}
	}
}
echo json_encode($response);
?>