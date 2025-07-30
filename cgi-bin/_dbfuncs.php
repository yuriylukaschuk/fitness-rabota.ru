<?php
function connect(){ 
	global $dbhostname, $dbusername, $dbpassword, $database;
	$mysqli = new mysqli($dbhostname, $dbusername, $dbpassword, $database);
	if ($mysqli->connect_error) {
		die('Ошибка подключения (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
	} else {
		$mysqli->query("SET NAMES 'utf8'");
		$mysqli->set_charset("utf8");
	}
	return $mysqli;
}

function parse_params($mysqli, $params, $type){
	$selField = '*';
	$limit = '';
	$groupby = '';
	$orderby = '';
	$where = '';
	$set = '';
	$response = array(
		'selField' => $selField,
		'limit' => $limit,
		'groupby' => $groupby,
		'orderby' => $orderby,
		'where' => $where,
		'set' => $set
	);
	if (!empty($params['between'])){
		if (count($params['between']) > 1){
			$typeop = $params['between_type'];
			$firstsimbol = '(';
			$lastsimbol = ')';
		} else {
			$typeop = $type;
			$firstsimbol = '';
			$lastsimbol = '';
		}
		foreach ($params['between'] as $fieldName => $fieldValue){
			$where .= ((empty($where)) ? $firstsimbol : $typeop).$fieldName.' BETWEEN '.$fieldValue[0].' AND '.$fieldValue[1];
		}
		$where .= $lastsimbol;
	}
	if (!empty($params['in'])){
		foreach ($params['in'] as $fieldName => $fieldValue){
			$in = '';
			foreach ($fieldValue as $key => $value){
				$pos = mb_stripos($value,".");
				$value = ($pos === false) ? (int)$value * 1 : "'".$mysqli->real_escape_string(filter_var(trim($value),FILTER_SANITIZE_STRING))."'";
				$in .= (empty($in) ? '' : ',').$value;
			}
			$where .= ((empty($where)) ? '' : $type).$fieldName.' IN ('.$in.')';
		}
	}
	if (!empty($params['notin'])){
		foreach ($params['notin'] as $fieldName => $fieldValue){
			$in = '';
			foreach ($fieldValue as $key => $value){
				$in .= (empty($in) ? '' : ',').$value;
				$pos = mb_stripos($value,".");
				$value = ($pos === false) ? (int)$value * 1 : "'".$mysqli->real_escape_string(filter_var(trim($value),FILTER_SANITIZE_STRING))."'";
			}
			$where .= ((empty($where)) ? '' : $type).$fieldName.' NOT IN ('.$in.')';
		}
	}
	if (!empty($params['min'])){
		$selField = 'MIN(';
		foreach ($params['min'] as $key => $val) $selField .= ($key ? ', ' : '').$val;
		$selField .= ') as `minValue`';
	}
	if (!empty($params['max'])){
		$selField = 'MAX(';
		foreach ($params['max'] as $key => $val) $selField .= ($key ? ', ' : '').$val;
		$selField .= ') as `maxValue`';
	}
	if (!empty($params['least'])){
		$selField = 'LEAST(';
		foreach ($params['least'] as $key => $val) $selField .= ($key ? ', ' : '').'MIN('.$val.')';
		$selField .= ') as `minValue`';
	}
	if (!empty($params['where'])){
		foreach ($params['where'] as $fieldName => $fieldValue){
			if (is_array($fieldValue)){
				$op = (empty($fieldValue['op'])) ? '=' : $fieldValue['op'];
				$value = $fieldValue['val'];
			} else {
				$op = '=';
				$value = $fieldValue;
			}
			if (stripos($value,'LIKE') !== false){
				$where .= (empty($where) ? "" : $type).$fieldName.' '.$value;
			} elseif (is_string($value) && $value == 'NULL') {
				$where .= (empty($where) ? "" : $type).$fieldName.' IS NULL';
			} elseif (is_string($value) && $value == 'NOTNULL') {
				$where .= (empty($where) ? "" : $type).$fieldName.' IS NOT NULL';
			} else {
				$value = "'".$mysqli->real_escape_string(filter_var(trim($value),FILTER_SANITIZE_STRING))."'";
				$where .= (empty($where) ? "" : $type).$fieldName.' '.$op.' '.$value;
			}
		}
	}
	if (!empty($params['set'])){
		foreach ($params['set'] as $name => $value){
			$set .= (empty($set) ? "" : ", ")."`".$name."` = '".$mysqli->real_escape_string(filter_var(trim($value),FILTER_SANITIZE_STRING))."'";
		}
	}
	if (!empty($params['increment'])){
		foreach ($params['increment'] as $name => $increment){
			$set .= (empty($set) ? "" : ", ")."`".$name."` = `".$name."` ".$increment['op'].' '.$increment['value'];
		}
	}

	if (!empty($params['groupby'])){
		$groupby = ' GROUP BY '.$params['groupby'];
	}
	if (!empty($params['orderby'])){
		if ($params['orderby'] == 'rand'){
			$orderby = 'RAND()';
		} else {
			foreach ($params['orderby'] as $fieldName => $fieldValue){
				$orderby .= (empty($orderby) ? "" : ", ").$fieldName.' '.$fieldValue;
			}
		}
	}
	if (!empty($params['limit'])){
		if (!empty($params['limit']['from'])) $limit .= $params['limit']['from'].",".$params['limit']['to'];
		else $limit .= $params['limit']['all'];
	}
	$response = array(
		'selField' => $selField,
		'limit' => $limit,
		'groupby' => $groupby,
		'orderby' => $orderby,
		'where' => $where,
		'set' => $set
	);
	return $response;
}

function db_get($tbName = '', $params = array(), $type = ' AND '){
	$response = array();
	$mysqli = connect();
	$param = parse_params($mysqli, $params, $type);
	if (empty($params['count'])){
		$response = array();
		$sql = "SELECT ".$param['selField']." FROM ".$tbName.
			(empty($param['where']) ? "" : " WHERE ".$param['where']).
			(empty($param['groupby']) ? "" : $param['groupby']).
			(empty($param['orderby']) ? "" : " ORDER BY ".$param['orderby']).
			(empty($param['limit']) ? "" : " LIMIT ".$param['limit']);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) $response[] = $row;
			$result->free();
		}
	} else {
		$response = 0;
		$sql = "SELECT COUNT(*) AS cnt FROM ".$tbName.(empty($param['where']) ? "" : " WHERE ".$param['where']);
		if ($result = $mysqli->query($sql)) {
			$obj = $result->fetch_object();
			$result->free();
			$response = (int)$obj->cnt;
		}
	}
	$mysqli->close();
	return $response;
}

function db_add($tbName = '', $params = array()){
	$response_id = 0;
	$delimetr = ', ';
	$mysqli = connect();
	foreach ($params as $npp => $param){
		$params_str = '';
		$values_str = '';
		foreach ($param as $key => $val){
			$val = (is_numeric($val)) ? $val : "'".$mysqli->real_escape_string(filter_var(trim($val),FILTER_SANITIZE_STRING))."'";
			$params_str .= ((empty($params_str)) ? '' : $delimetr).$key;
			$values_str .= ((empty($values_str)) ? '' : $delimetr).$val;
		}
		if (!empty($values_str)){
			$sql = "INSERT INTO ".$tbName."(".$params_str.") VALUES(".$values_str.")";
			$mysqli->query($sql);
			$response_id = $mysqli->insert_id;
		}
	}
   	$mysqli->close();
	return $response_id;
}

function db_update($tbName = '', $params = array(), $type = ' AND '){
	$mysqli = connect();
	foreach ($params as $key => $val){
		$param = parse_params($mysqli, $val, $type);
		$sql = "UPDATE ".$tbName." SET ".$param['set']." WHERE ".(empty($param['where']) ? 1 : $param['where']);
		$mysqli->query($sql);
	}
	$mysqli->close();
	return true;
}

function db_del($tbName = '', $params = array(), $type = ' AND '){
	$mysqli = connect();
	if (empty($params)){
		$sql = "DELETE FROM ".$tbName." WHERE 1";
		$mysqli->query($sql);
	} else {
		foreach ($params as $key => $val){
			$param = parse_params($mysqli, $val, $type);
			$sql = "DELETE FROM ".$tbName.(empty($param['where']) ? "" : " WHERE ".$param['where']);
			$mysqli->query($sql);
		}
	}
	$mysqli->close();
	return true;
}
?>