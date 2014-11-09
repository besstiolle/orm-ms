<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['user_id'])){
	//Let's retrieve our user !
	$user = OrmCore::findById(new UserSkeleton(), $params['user_id']);
	$action = "Edition";
	if($user == null){
		// We create a new one
		$user = new UserSkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$user = new UserSkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editUserSave');
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
$smarty->assign('user',$user);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('user_edit.tpl');