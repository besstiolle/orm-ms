<?php

if (!function_exists("cmsms")) exit;

$currentLevel = $this->GetPreference('loglevel');
$items = array("DEBUG"=>TRACE::$DEBUG, "INFO"=>TRACE::$INFO,"WARN"=>TRACE::$WARN,"ERROR"=>TRACE::$ERROR);

$smarty = cmsms()->GetSmarty();
$smarty->assign("formstart",$this->CreateFormStart($id, 'admin_save'));
$smarty->assign("submit",$this->CreateInputSubmit ($id, 'submit', 'Save Prefs'));
$smarty->assign("delete",$this->CreateInputSubmit ($id, 'delete', 'Clean Logs'));
$smarty->assign("select", $this->CreateInputDropdown ($id, 'level', $items, -1, $currentLevel));

$smarty->assign("urlLog", TRACE::getLogUrl());

echo $this->ProcessTemplate('admin.tpl');


?>