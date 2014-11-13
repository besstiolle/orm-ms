<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['city_id'])){
	//Let's retrieve our city !
	$city = OrmCore::findById(new CitySkeleton(), $params['city_id']);
	$action = "Edition";
	if($city == null){
		// We create a new one
		$city = new CitySkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$city = new CitySkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editCitySave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

//We need to propose all countries
$countries = OrmCore::findAll(new CountrySkeleton());
$items = array();
$items['  = Choose one Country =  '] = '';
foreach($countries as $country){ 
	$items[$country->get('labelCountry')] = $country->get('country_id');
}
$selectedvalue = '-1';
if($city->get('country') != '') {
	$selectedvalue = $city->get('country')->get('country_id');
}
$selectCountries = $this->CreateInputDropdown($id, 'country', $items, -1, $selectedvalue); 

$smarty->assign('formStart',$formStart);
$smarty->assign('action',$action);
$smarty->assign('submit',$submit);
$smarty->assign('return',$return);
$smarty->assign('error',$error);
$smarty->assign('city',$city);
$smarty->assign('selectCountries',$selectCountries);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('city_edit.tpl');