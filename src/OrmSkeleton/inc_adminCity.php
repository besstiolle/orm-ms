<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "CitySkeleton" whenever i want in my module
$city = new CitySkeleton();

// In the same way i can interrogate the table of CitySkeleton : 
$count = OrmCore::countAll(new CitySkeleton());

$add = $this->CreateLink($id, 'editCity', $returnid, 'add');

$all = array();
$edit = array();
$delete = array();

if($count !== 0){

	//I can also retrieve all the CitySkeleton
	$all = OrmCore::findAll(new CitySkeleton());
	
	//And iterate over each one
	foreach($all as $city){
		$delete[$city->get('city_id')] = $this->CreateLink($id, 'editCityDelete', $returnid, $img_delete,array('city_id'=>$city->get('city_id')));
		$edit[$city->get('city_id')] = $this->CreateLink($id, 'editCity', $returnid, $img_edit,array('city_id'=>$city->get('city_id')));
	}
}

$smarty->assign('all',$all);
$smarty->assign('count',$count);
$smarty->assign('edit',$edit);
$smarty->assign('delete',$delete);
$smarty->assign('add',$add);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('city_view.tpl');

?>