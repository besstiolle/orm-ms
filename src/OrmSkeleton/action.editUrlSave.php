<?php

if (!function_exists("cmsms")) exit;

$url = null;
if(!empty($params['url']) && !empty($params['lang_iso'])){
	//Let's retrieve our url !
	$example = new OrmExample();
	$example->addCriteria('url', OrmTypeCriteria::$EQ, array($params['url']));
	$example->addCriteria('lang_iso', OrmTypeCriteria::$EQ, array($params['lang_iso']));
	
	$url = OrmCore::findByExample(new UrlSkeleton(), $example);
	if(!empty($url)){
		$url = $url[0];
	}
} else {
	//ERROR : we can't create an URL if we don't have both of it's PK because they're not INTEGER PK
	$params['error'] = "Parameters url and lang_iso are both required";
	$this->Redirect($id, 'editUrl', $returnid, $params);
}

if($url == null){
	$url = new UrlSkeleton();
	$url->set('url', $params['url']);
	$url->set('lang_iso', $params['lang_iso']);
}

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