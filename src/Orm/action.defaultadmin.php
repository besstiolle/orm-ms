<?php

if (!function_exists("cmsms")) exit;

//CONSTANTES
$itemsLog = array("DEBUG"=>OrmTrace::$DEBUG, "INFO & SQL"=>OrmTrace::$SQL, "INFO"=>OrmTrace::$INFO,"WARN"=>OrmTrace::$WARN,"ERROR"=>OrmTrace::$ERROR);
$itemsCache = array("NONE"=>OrmCache::$NONE, "SCRIPT"=>OrmCache::$SCRIPT);

$currentLevelLog = $this->GetPreference('loglevel', OrmTrace::$INFO);
$currentTypeCache = $this->GetPreference('cacheType', OrmCache::$NONE);

//Initiate the file of log
if(!file_exists(OrmTrace::getLogFile())){
	OrmTrace::info("Initiate the log file");;
}

$smarty = cmsms()->GetSmarty();
$smarty->assign("id",$id);
$smarty->assign("formstartcache",$this->CreateFormStart($id, 'admin_save'));
$smarty->assign("formstarttrace",$this->CreateFormStart($id, 'admin_save'));
$smarty->assign("submit",$this->CreateInputSubmit ($id, 'submit', 'Save Prefs'));
$smarty->assign("deleteLog",$this->CreateInputSubmit ($id, 'deleteLog', 'Clean Logs'));
$smarty->assign("deleteCache",$this->CreateInputSubmit ($id, 'deleteCache', 'Clean Cache'));
$smarty->assign("selectLog", $this->CreateInputDropdown ($id, 'level', $itemsLog, -1, $currentLevelLog));
$smarty->assign("selectCache", $this->CreateInputDropdown ($id, 'cache', $itemsCache, -1, $currentTypeCache));

$smarty->assign("urlLog", OrmTrace::getLogUrl());

echo $this->ProcessTemplate('admin.tpl');


?>