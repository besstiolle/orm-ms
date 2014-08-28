<?php

if (!function_exists("cmsms")) exit;

$url = null;
if(!empty($params['url']) && !empty($params['lang_iso'])){
	//Let's retrieve our url ! but we can't call findById(s) because UrlSkeleton is a multi-PrimaryKey.
	$example = new OrmExample();
	$example->addCriteria('url', OrmTypeCriteria::$EQ, array($params['url']));
	$example->addCriteria('lang_iso', OrmTypeCriteria::$EQ, array($params['lang_iso']));
	$urls = OrmCore::findByExample(new UrlSkeleton(), $example);
	if(count($urls) > 1){
		// We can't find 2 entity with the same couple of primary key
		$params['error'] = "UrlSkeleton with dupplicate url & lang_iso found";
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	}
	if(!empty($urls)){
		$url = $urls[0];
	}
} 

//The primary keys are requiered
if(empty($params['url']) || empty($params['lang_iso'])){
	$params['error'] = "the param url & lang_iso are both requiered";
	$this->Redirect($id, 'editUrl', $returnid, $params);
}

if($url == null){
	// We create a new one
	$url = new UrlSkeleton();
	$action = "Creation";
}


$url->set('url', $params['url']);
$url->set('lang_iso', $params['lang_iso']);


if(!empty($params['title'])){
	$url->set('title', $params['title']);
} else {
	$url->set('title', null);
}
if(!empty($params['description'])){
	$url->set('description', $params['description']);
} else {
	$url->set('description', null);
}	

try{
	// We simply save the entity
	$url->save();
	// Please note that this code could also work.
	//OrmCore::insertEntity($url);
	
	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
	
// The illegaArgument will happen each time you don't controle enough the data before inserting them
} catch (OrmIllegalArgumentException $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the user
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'editUrl', $returnid, $params);
}
?>