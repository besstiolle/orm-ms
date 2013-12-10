<?php

if (!function_exists("cmsms")) exit;

$user = null;
if(!empty($params['user_id'])){
	//Let's retrieve our user !
	$user = OrmCore::findById(new UserSkeleton(), $params['user_id']);
} 

if($user == null){
	// we can't delete something that doesn't exist
	$params['error'] = "UserSkeleton not found";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

try{
	// We simply delete the entity
	$user->delete();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
} catch (Exception $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the user
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
?>