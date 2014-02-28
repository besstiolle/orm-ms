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
} 

if($url == null){
	// we can't delete something that doesn't exist
	$params['error'] = "UrlSkeleton not found";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

try{
	// We simply delete the entity
	$url->delete();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
} catch (Exception $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the url
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
?>