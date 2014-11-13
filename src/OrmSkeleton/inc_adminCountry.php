<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "CountrySkeleton" whenever i want in my module
$country = new CountrySkeleton();

// In the same way i can interrogate the table of CountrySkeleton : 
$count = OrmCore::countAll(new CountrySkeleton());

$add = $this->CreateLink($id, 'editCountry', $returnid, 'add');


$all = array();
$edit = array();
$delete = array();
$citiesLabel = array();

if($count !== 0){
	//I can also retrieve all the CountrySkeleton
	$all = OrmCore::findAll(new CountrySkeleton());

	//And iterate over each one
	foreach($all as $country){
	
		//We'll automatticly get the cities linked to our Country in an Array form
		$cities = $country->get('cities');
		
		$citiesLabel[$country->get('country_id')] = "= No city =";
		if(count($cities) > 0){
			$citiesLabelArray = array();
			foreach($cities as $city) {
				$citiesLabelArray[] = $city->get('labelCity');
			}
			$citiesLabel[$country->get('country_id')] = implode(',', $citiesLabelArray);
		}		
		
		if(OrmCore::verifIntegrity($country, $country->get('country_id')) == ""){
			$linkDelete = $this->CreateLink($id, 'editCountryDelete', $returnid, $img_delete,array('country_id'=>$country->get('country_id')));
		} else {
			$linkDelete = '<span style="color:#CCC">still used</span>';
		}
		
		$edit[$country->get('country_id')] = $this->CreateLink($id, 'editCountry', $returnid, $img_edit,array('country_id'=>$country->get('country_id')));
		$delete[$country->get('country_id')] = $linkDelete;
	}
}

$smarty->assign('all',$all);
$smarty->assign('count',$count);
$smarty->assign('edit',$edit);
$smarty->assign('delete',$delete);
$smarty->assign('add',$add);
$smarty->assign('citiesLabel',$citiesLabel);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('country_view.tpl');
?>