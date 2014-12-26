<?php

if (!function_exists("cmsms")) exit;

//Find all module ORM-like
$modops = cmsms()->GetModuleOperations();
$allmods  = $modops->FindAllModules();
$instanceOrm = array();
foreach ($allmods as $mod) {
	$instance = $modops->get_module_instance($mod);
	if(class_exists($mod) &&  in_array($this->GetName(),class_parents($mod))){		
		$instanceOrm[$mod] = $instance;
	}
}

$tempname = 'XXXXXXXXXXX';
$descXXQuery = "desc ".$tempname;


$listInstance = array();
foreach ($instanceOrm as $moduleName => $module) {
	$listResultXX = array();
	$listResultDB = array();
	$listEmptyTable = array();

	$instance = new $moduleName;
	$liste = $instance->scan();
	$entites = $liste['entities'];
	foreach ($entites as $entite) {
		$obj = new $entite['classname']();
		$descDBQuery = "desc ".$obj->getDbname();
		$findDBQuery = "SHOW TABLES LIKE '".$obj->getDbname()."'";

		$result = OrmDb::execute($findDBQuery, null, $errorMsg = "Find Table Query error");
		$listEmptyTable[$entite['classname']] = '';
		$arrayResult = $result->GetArray();
		if(empty($arrayResult)){
			$listEmptyTable[$entite['classname']] = $obj->getDbname();
			continue;
		} 

		$hql = OrmCore::_getFieldsToHql($obj);
		OrmDb::dropTable($tempname);
		OrmDb::createTable($tempname, $hql);

		//We manage the ("unique") indexes
		$indexes = $obj->getIndexes();

		//For each Field contained in the entity
		foreach($indexes as $index) {
			$result = OrmDb::createIndex($tempname, $index['fields'], $index['unique']);
		}

		$resultXX = OrmDb::execute($descXXQuery, null, $errorMsg = "Description on table '".$tempname."' produce an error");
		$resultDB = OrmDb::execute($descDBQuery, null, $errorMsg = "Description on table '".$obj->getDbname()."' produce an error");
		
		$arrayXX = $resultXX->GetAssoc();
		$arrayDB = $resultDB->GetAssoc();

		$descXX = print_r($arrayXX, true);
		$descDB = print_r($arrayDB, true);

		$listResultXX[$entite['classname']] = $descXX;
		$listResultDB[$entite['classname']] = $descDB;

	}
	
	$listInstance[$moduleName] = array('listResultXX' => $listResultXX,
								'listResultDB' => $listResultDB,
								'listEmptyTable' => $listEmptyTable
								);
}

OrmDb::dropTable($tempname);

$smarty->assign('listInstance', $listInstance);
$smarty->assign('back', $this->CreateLink ($id, 'defaultadmin', null, '',array(),'',true));


echo $this->ProcessTemplate('admin_check.tpl');

?>



