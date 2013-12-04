<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['city_id'])){
	//Let's retrieve our city !
	$city = Core::findById(new CitySkeleton(), $params['city_id']);
} else {
	$city = new CitySkeleton();
}	
if(!empty($params['labelCity'])){
	$city->set('labelCity', $params['labelCity']);
}
if(!empty($params['country'])){
	$city->set('country', $params['country']);
}

try{
	// We simply save the entity
	$city->save();
	// Please note that this code could also work.
	//Core::insertEntity($city);
	
	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
	
// The illegaArgument will happen each time you don't controle enough the data before inserting them
} catch (IllegalArgumentException $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the city
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'editCity', $returnid, $params);
}
?>