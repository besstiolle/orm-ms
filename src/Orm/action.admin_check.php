<style>
	label.help{
		
	}
	
	div.left{
	    float: left;
		text-align: center;
		width: 50px;
	}
	
	div.right{
		border-left: 2px solid #CCCCCC;
		float: left;
		padding-left: 5px;
	}
	.ormbutton {
		clear: both;
		text-align: left
	}
	a.ormbutton{
		display: inline-block;
		position: relative;
		margin: 10px 0;
		line-height: 26px;
		color: #232323;
		text-decoration: none;
		padding: 1px 8px 2px 20px;
	}
	a.ormbutton .ui-icon {
		position: absolute;
		left: 0;
		top: 6px;
	}
	a.ormbutton:hover {
		color: #fff
	}
</style>
<script type="text/javascript" src='http://prettydiff.com/lib/diffview.js'></script>
<?php

//Find all module ORM-like
$modops = cmsms()->GetModuleOperations();
$allmods  = $modops->FindAllModules();
$instanceOrm = array();
foreach ($allmods as $mod) {
	$instance = $modops->get_module_instance($mod);
	if(class_exists($mod) &&  in_array($this->GetName(),class_parents($mod))){
		echo "Module <b>{$mod}</b> detected<br/>";		
		$instanceOrm[$mod] = $instance;
	}
}
if(empty($instanceOrm)){
	echo "No module detected";
}

foreach ($instanceOrm as $moduleName => $module) {
	$instance = new $moduleName;
	echo "<h3> Module '".$moduleName."'</h3>";
	$liste = $instance->scan();
	$entites = $liste['entities'];
	foreach ($entites as $entite) {
		echo "<h5 style='margin-left:10px;'>{$entite['classname']}</h5>";
		$obj = new $entite['classname']();
		$tempname = 'XXXXXXXXXXX';
		$descXXQuery = "desc ".$tempname;
		$descDBQuery = "desc ".$obj->getDbname();
		$findDBQuery = "SHOW TABLES LIKE '".$obj->getDbname()."'";

		$result = OrmDb::execute($findDBQuery, null, $errorMsg = "Find Table Query error");
		if(empty($result->GetArray())){
			echo "<p style='color:#F00;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> is not found.</p>";
			continue;
		} 

		$hql = OrmCore::_getFieldsToHql($obj);
		OrmDb::dropTable($tempname);
		OrmDb::createTable($tempname, $hql);
		$resultXX = OrmDb::execute($descXXQuery, null, $errorMsg = "Desciption on table 'XXX' produce an error");
		$resultDB = OrmDb::execute($descDBQuery, null, $errorMsg = "Desciption on table '".$obj->getDbname()."' produce an error");
		
		$arrayXX = $resultXX->GetAssoc();
		$arrayDB = $resultDB->GetAssoc();

		$descXX = print_r($arrayXX, true);
		$descDB = print_r($arrayDB, true);

		$arrayDiffXX = json_encode(preg_split('/$\R?^/m', $descXX));
		$arrayDiffDB = json_encode(preg_split('/$\R?^/m', $descDB));

		if($descXX === $descDB){
			echo "<p style='color:#0F0;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> is well formed</p>";
			continue;	
		}
		echo <<<HTML
			<script type="text/javascript" >
				$( document ).ready(function() {
				    \$html = diffview({$arrayDiffXX},{$arrayDiffDB},'premier','second');
					$('#{$entite['classname']}').html(\$html);
				});
			</script>
HTML;
		echo "<div id='{$entite['classname']}'></div>";

	}
}





$back = $this->CreateLink ($id, 'defaultadmin', null, '',array(),'',true);


echo <<<HTML
	<a class="ormbutton ui-state-default ui-corner-all" href="{$back}">
		<span class="ui-icon  ui-icon-arrowreturnthick-1-w"></span>
		Back
	</a>

HTML;
?>



