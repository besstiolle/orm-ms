<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['country_id'])){
	//Let's retrieve our country !
	$country = OrmCore::findById(new CountrySkeleton(), $params['country_id']);
	$action = "Edition";
	if($country == null){
		// We create a new one
		$country = new CountrySkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$country = new CountrySkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editCountrySave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}
$smarty->assign('formStart',$formStart);
$smarty->assign('action',$action);
$smarty->assign('submit',$submit);
$smarty->assign('return',$return);
$smarty->assign('error',$error);
$smarty->assign('country',$country);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('country_edit.tpl');
?>
