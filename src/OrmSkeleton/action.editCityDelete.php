<?php

if (!function_exists("cmsms")) exit;

$city = null;
if(!empty($params['city_id'])){
	//Let's retrieve our city !
	$city = Core::findById(new CitySkeleton(), $params['city_id']);
} 

if($city == null){
	// we can't delete something that doesn't exist
	$params['error'] = "CitySkeleton not found";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

try{
	// We simply delete the entity
	$city->delete();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
} catch (Exception $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the city
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
?>