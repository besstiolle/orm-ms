<?php

if (!function_exists("cmsms")) exit;

$country = null;
if(!empty($params['country_id'])){
	//Let's retrieve our country !
	$country = Core::findById(new CountrySkeleton(), $params['country_id']);
} 

if($country == null){
	// we can't delete something that doesn't exist
	$params['error'] = "CountrySkeleton not found";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

if(Core::verifIntegrity($country, $country->get('country_id')) != ""){
	// we can't delete something that still used by something else
	$params['error'] = "CountrySkeleton still used by another Entity";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}


try{
	// We simply delete the entity
	$country->delete();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
} catch (Exception $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the country
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
?>