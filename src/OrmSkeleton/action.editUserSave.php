<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['user_id'])){
	//Let's retrieve our user !
	$user = OrmCore::findById(new UserSkeleton(), $params['user_id']);
	$user->set('hour_last_modification', date('H:i:s'));
} else {
	$user = new UserSkeleton();
	$user->set('date_creation', date('Y-m-d H:i:s'));
	$user->set('hour_last_modification', date('H:i:s'));
}	

if(!empty($params['login'])){
	$user->set('login', $params['login']);
} else {
	$user->set('login', null);
}
if(!empty($params['name'])){
	$user->set('name', $params['name']);
} else {
	$user->set('name', null);
}
if(!empty($params['description'])){
	$user->set('description', $params['description']);
} else {
	$user->set('description', null);
}

try{
	// We simply save the entity
	$user->save();
	// Please note that this code could also work.
	//OrmCore::insertEntity($user);
	
	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
	
// The illegaArgument will happen each time you don't controle enough the data before inserting them
} catch (OrmIllegalArgumentException $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the user
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'editUser', $returnid, $params);
}
?>