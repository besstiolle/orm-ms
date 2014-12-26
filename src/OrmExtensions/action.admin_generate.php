<?php

if (!function_exists("cmsms")) exit;

//liste of table
$config = cmsms()->GetConfig();
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

$entityName = '';
if(isset($params['entityName'])){
	$entityName = $params['entityName'];
	$entityName = ucfirst($entityName); 
}

$moduleName = '';
if(isset($params['moduleName'])){
	$moduleName = $params['moduleName'];
	$moduleName = ucfirst($moduleName);
}

$smarty->assign('entityName', $entityName);
$smarty->assign('moduleName', $moduleName);
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

//echo var_dump($arrayQuery);


$fields = array();
foreach ($arrayQuery as $queryKey => $values) {

	/***********  Type & Size ***********/
	$type = $values['Type'];
	$posSize = strpos($type, '(');
	$size = 'NULL';
	if($posSize){
		$size = substr($type, $posSize + 1, strlen($type) - $posSize - 2 );
		$type = substr($type, 0, $posSize);
	} 

	$ormType = 'N/A';
	$ormSize = 'NULL';

	if($type === 'int'){
		$ormType = '$INTEGER';
		if($size !== '11') {
			$ormSize = $size;	
		}
	} else if($type === 'varchar'){
		$ormType = '$STRING';
		$ormSize = $size;
	} else if($type === 'text'){
		$ormType = '$BUFFER';
	} else if($type === 'datetime'){
		$ormType = '$DATETIME';
	} else if($type === 'tinyint'){
		$ormType = '$INTEGER';
		$ormSize = $size;
	} else if($type === 'int'){
	} else if($type === 'double'){
		$ormType = '$DOUBLE';
	}
	/***********  Type & Size ***********/



	/***********  Nullable ***********/
	$nullable = $values['Null'];
	$ormNullable = 'NULL'; 
	if($nullable === 'YES') {
		$ormNullable = 'TRUE';
	}
	/***********  Nullable ***********/



	/***********  PK/AK/FK ***********/
	$key = $values['Key'];
	$outputKey = 'NULL';
	if($key === 'PRI'){
		$outputKey = 'OrmKEY::$PK';
	}
	/***********  PK/AK/FK ***********/


	/***********  Complement ***********/
	$extra = $values['Extra'];
	$outputExtra = array();
	if($extra === 'auto_increment'){
		$outputExtra[] = '$this->garnishAutoincrement();';
	}

	$extra = $values['Default'];
	if($extra !== NULL){
		if($type === 'int' || $type === 'tinyint') {
			$outputExtra[] = '$this->garnishDefaultValue("'.$queryKey.'",'.$extra.');';
		} else {
			$outputExtra[] = '$this->garnishDefaultValue("'.$queryKey.'","'.$extra.'");';
		}
		
	}

	if($key === 'MUL'){
		$outputExtra[] = '$this->addIndexes("'.$queryKey.'");';
	}
	/***********  Complement ***********/

	$fields[$queryKey] = array(
		'type' => $ormType,
		'size' => $ormSize,
		'nullable' => $ormNullable,
		'key' => $outputKey,
		'extra' => $outputExtra,

		);
}


$smarty->assign('output', $fields);
$output = $this->ProcessTemplate('admin_generate_output.tpl');
$smarty->assign('output', $output);

$dir = $config['root_path'].'/modules/'.$moduleName.'/lib';
$file = 'class.'.$entityName.'.php';

$smarty->assign('pathFile', $dir.'/'.$file);

$persist = null;
$pathFile = null;
if(isset($params['persist']) && isset($params['pathFile'])){
	$persist = false;
	
	$path = $params['pathFile'];
	$pos = strrpos($path, '/');
	$dir = substr($path, 0, $pos);
	$file = substr($path, $pos);

	if(!is_dir($dir)){
		mkdir($dir, 0755, true);
	}

	if(file_put_contents($dir.$file, $output) !== FALSE){
		$persist = true;
	}
}

$smarty->assign('resultPersist', $persist);

echo $this->ProcessTemplate('admin_generate.tpl');
?>