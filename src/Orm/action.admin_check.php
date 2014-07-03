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
}





$back = $this->CreateLink ($id, 'defaultadmin', null, '',array(),'',true);


echo <<<HTML
	<a class="ormbutton ui-state-default ui-corner-all" href="{$back}">
		<span class="ui-icon  ui-icon-arrowreturnthick-1-w"></span>
		Back
	</a>

HTML;
?>



