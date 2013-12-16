<?php

if (!function_exists("cmsms")) exit;

$currentLevelLog = $this->GetPreference('loglevel', OrmTRACE::$INFO);
$currentTypeCache = $this->GetPreference('cacheType', OrmCache::$NONE);

$itemsLog = array("DEBUG"=>OrmTRACE::$DEBUG, "INFO"=>OrmTRACE::$INFO,"WARN"=>OrmTRACE::$WARN,"ERROR"=>OrmTRACE::$ERROR);
$itemsCache = array("NONE"=>OrmCache::$NONE, "CALL"=>OrmCache::$CALL);

$smarty = cmsms()->GetSmarty();
$smarty->assign("id",$id);
$smarty->assign("formstart",$this->CreateFormStart($id, 'admin_save'));
$smarty->assign("submit",$this->CreateInputSubmit ($id, 'submit', 'Save Prefs'));
$smarty->assign("deleteLog",$this->CreateInputSubmit ($id, 'deleteLog', 'Clean Logs'));
$smarty->assign("deleteCache",$this->CreateInputSubmit ($id, 'deleteCache', 'Clean Cache'));
$smarty->assign("selectLog", $this->CreateInputDropdown ($id, 'level', $itemsLog, -1, $currentLevelLog));
$smarty->assign("selectCache", $this->CreateInputDropdown ($id, 'cache', $itemsCache, -1, $currentTypeCache));

$smarty->assign("urlLog", OrmTRACE::getLogUrl());

echo $this->ProcessTemplate('admin.tpl');


?>