<?php

if (!function_exists("cmsms")) exit;

//liste of table
$findDBQuery = "SHOW TABLES";
$result = OrmDb::execute($findDBQuery, null, $errorMsg = "Find Tables Query error");
$tables = $result->GetArray();
$stringKey = null;
$items = array('Please select one SQL table' => '');
foreach ($tables as $table) {
	if($stringKey == null){
		$stringKey = array_keys($table);
		$stringKey = $stringKey[0];
	}

	$items[$table[$stringKey]] = $table[$stringKey];
}
$paramTableName = null;
if(isset($params['tableName']) && !empty($params['tableName'])){
	$paramTableName = $params['tableName'];
}

$smarty->assign('dropdown', $this->CreateInputDropdown ($id, 'tableName', $items, null, $paramTableName));
$smarty->assign('formStart', $this->CreateFormStart($id, 'admin_generate', $returnid));
$smarty->assign('cancel', $this->CreateLink ($id, 'defaultadmin', $returnid, '', array(), '', true));

//If a wrong value is passed in the post value
if(!array_key_exists($paramTableName, $items)){
	echo $this->ProcessTemplate('admin_generate.tpl');
	return;
}


$descQuery = "desc ".$paramTableName;
$resultQuery = OrmDb::execute($descQuery, null, $errorMsg = "Description on table '".$paramTableName."' produce an error");
$arrayQuery = $resultQuery->GetAssoc();
echo "<pre>";
echo var_dump($arrayQuery);
echo "</pre>";

$fields = array();
foreach ($arrayQuery as $key => $values) {

	if(strpos($values['Type'], '(')){
		
	}

	$fields[$key] = array();
}


$smarty->assign('output', $fields);

echo $this->ProcessTemplate('admin_generate.tpl');
?>