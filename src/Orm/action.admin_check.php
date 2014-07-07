<?php

$config = cmsms()->GetConfig();


?>
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
	.hidden{
		display:none;
	}

	.div__output{
		max-height: 300px;
		overflow: auto;
		border: 1px solid #000;
	}
</style>
<script type="text/javascript" src='<? echo $config['root_url']; ?>/modules/Orm/js/diffview.js'></script>
<script type="text/javascript" src='<? echo $config['root_url']; ?>/modules/Orm/js/difflib.js'></script>
<link rel="stylesheet" type="text/css" href="<? echo $config['root_url']; ?>/modules/Orm/js/diffview.css" media="screen" />
<script type="text/javascript">
	function diffUsingJS(viewType,idbase, idnew, idoutput) {
	"use strict";
	var byId = function (id) { return document.getElementById(id); },
		base = difflib.stringAsLines(byId(idbase).value),
		newtxt = difflib.stringAsLines(byId(idnew).value),
		sm = new difflib.SequenceMatcher(base, newtxt),
		opcodes = sm.get_opcodes(),
		diffoutputdiv = byId(idoutput),
		contextSize = null; //byId("contextSize").value;

	diffoutputdiv.innerHTML = "";
	contextSize = contextSize || null;

	diffoutputdiv.appendChild(diffview.buildView({
		baseTextLines: base,
		newTextLines: newtxt,
		opcodes: opcodes,
		baseTextName: "Orm Values",
		newTextName: "Database Values",
		contextSize: contextSize,
		viewType: viewType
	}));
}
</script>

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
			echo "<p style='color:#870909;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> is not found.</p>";
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

		$resultXX = OrmDb::execute($descXXQuery, null, $errorMsg = "Desciption on table '".$tempname."' produce an error");
		$resultDB = OrmDb::execute($descDBQuery, null, $errorMsg = "Desciption on table '".$obj->getDbname()."' produce an error");
		
		$arrayXX = $resultXX->GetAssoc();
		$arrayDB = $resultDB->GetAssoc();

		$descXX = print_r($arrayXX, true);
		$descDB = print_r($arrayDB, true);

		//$arrayDiffXX = json_encode(preg_split('/$\R?^/m', $descXX));
		//$arrayDiffDB = json_encode(preg_split('/$\R?^/m', $descDB));

		if($descXX === $descDB){
			echo <<<HTML
				<p style='color:#09870E;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> is well formed</p>
HTML;
			continue;	
		}
		echo <<<HTML
			<p style='color:#FAA00F;'>The table <b>{$obj->getDbname()}</b> for entity <b>{$moduleName}</b> have some differents.</p>
			<textarea id='baseText_{$moduleName}' class='hidden'>{$descXX}</textarea>
			<textarea id='newText_{$moduleName}' class='hidden'>{$descDB}</textarea>
			<div id="diffoutput_{$moduleName}" class='div__output'> </div>
			<script type="text/javascript" >
				$( document ).ready(function() {
					diffUsingJS(0, "baseText_{$moduleName}", "newText_{$moduleName}", "diffoutput_{$moduleName}");
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



