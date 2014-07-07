<?php

if (!function_exists("cmsms")) exit;

//Find all module ORM-like
$modops = cmsms()->GetModuleOperations();
$allmods  = $modops->FindAllModules();
$instanceOrm = array();
foreach ($allmods as $mod) {
	$instance = $modops->get_module_instance($mod);
	if(class_exists($mod) &&  in_array($this->GetName(),class_parents($mod))){
		//echo "Module <b>{$mod}</b> detected<br/>";		
		$instanceOrm[$mod] = $instance;
	}
}/*
if(empty($instanceOrm)){
	echo "No module detected";
}*/

//$smarty->assign('instanceOrm', $instanceOrm);

$listInstance = array();
foreach ($instanceOrm as $moduleName => $module) {
	$listResultXX = array();
	$listResultDB = array();
	$listEmptyTable = array();

	$instance = new $moduleName;
	//echo "<h3> Module '".$moduleName."'</h3>";
	$liste = $instance->scan();
	$entites = $liste['entities'];
	foreach ($entites as $entite) {
		//echo "<h5 style='margin-left:10px;'>{$entite['classname']}</h5>";
		$obj = new $entite['classname']();
		$tempname = 'XXXXXXXXXXX';
		$descXXQuery = "desc ".$tempname;
		$descDBQuery = "desc ".$obj->getDbname();
		$findDBQuery = "SHOW TABLES LIKE '".$obj->getDbname()."'";

		$result = OrmDb::execute($findDBQuery, null, $errorMsg = "Find Table Query error");
		$listEmptyTable[$entite['classname']] = '';
		if(empty($result->GetArray())){
			$listEmptyTable[$entite['classname']] = $obj->getDbname();
			//echo "<p style='color:#870909;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> is not found.</p>";
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

		//$arrayDiffXX = json_encode(preg_split('/$\R?^/m', $descXX));
		//$arrayDiffDB = json_encode(preg_split('/$\R?^/m', $descDB));

		if($descXX === $descDB){
			/*echo <<<HTML
				<p style='color:#09870E;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> is well formed</p>
HTML;*/
			continue;	
		}
		/*echo <<<HTML
			<p style='color:#FAA00F;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> have some differents.</p>
			<textarea id='baseText_{$moduleName}' class='hidden'>{$descXX}</textarea>
			<textarea id='newText_{$moduleName}' class='hidden'>{$descDB}</textarea>
			<div id="diffoutput_{$moduleName}" class='div__output'> </div>

			<script type="text/javascript" >
				$( document ).ready(function() {
					diffUsingJS(0, "baseText_{$moduleName}", "newText_{$moduleName}", "diffoutput_{$moduleName}");
				});
				
			</script>
HTML;*/
		/*echo "";*/

	}
	
	$listInstance[$moduleName] = array('listResultXX' => $listResultXX,
								'listResultDB' => $listResultDB,
								'listEmptyTable' => $listEmptyTable
								);
}





$back = $this->CreateLink ($id, 'defaultadmin', null, '',array(),'',true);

$smarty->assign('listInstance', $listInstance);
$smarty->assign('back', $back);
/*
echo <<<HTML
	<a class="ormbutton ui-state-default ui-corner-all" href="{$back}">
		<span class="ui-icon  ui-icon-arrowreturnthick-1-w"></span>
		Back
	</a>

HTML;*/

echo $this->ProcessTemplate('admin_check.tpl');

?>



