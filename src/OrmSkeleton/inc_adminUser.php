<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "UserSkeleton" whenever i want in my module
$user = new UserSkeleton();

// In the same way i can interrogate the table of UserSkeleton : 
$count = OrmCore::countAll(new UserSkeleton());

$add = $this->CreateLink($id, 'editUser', $returnid, 'add');

$all = array();
$edit = array();
$delete = array();

if($count !== 0){

	//I can also retrieve all the UserSkeleton
	$all = OrmCore::findAll(new UserSkeleton());

	//And iterate over each one
	foreach($all as $user){
		$delete[$user->get('user_id')] = $this->CreateLink($id, 'editUserDelete', $returnid, $img_delete,array('user_id'=>$user->get('user_id')));
		$edit[$user->get('user_id')] = $this->CreateLink($id, 'editUser', $returnid, $img_edit,array('user_id'=>$user->get('user_id')));
	}
}

$smarty->assign('all',$all);
$smarty->assign('count',$count);
$smarty->assign('edit',$edit);
$smarty->assign('delete',$delete);
$smarty->assign('add',$add);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('user_view.tpl');
?>