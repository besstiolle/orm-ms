<?php

if (!function_exists("cmsms")) exit;

if (isset($params['deleteLog'])){
	if(file_exists(OrmTrace::getLogFile())){
		unlink(OrmTrace::getLogFile());
	}
	OrmTrace::info("Reinitiate the log file");
} elseif (isset($params['deleteCache'])){

	$cache = OrmCache::getInstance();
	$cache->clearCache();
	OrmTrace::info("Reinitiate the cache content");
} else if ( isset($params['cache']) ){
	$this->SetPreference('cacheType', $params['cache']);
	
	//We reload the cache anyway
	$cache = OrmCache::getInstance();
	$cache->clearCache();
	
	OrmTrace::info("Initiate the cache content");
} else if (isset($params['level']) ){
	$this->SetPreference('loglevel', $params['level']);
}

$this->redirect($id,'defaultadmin');


?>