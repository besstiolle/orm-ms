<?php

if (!function_exists("cmsms")) exit;

if (isset($params['delete'])){
	unlink(OrmTRACE::getLogFile());
	OrmTrace::info("Reinitiate the log file");
} else if (isset($params['level']) && $params['level'] >= OrmTRACE::$DEBUG && $params['level'] <= OrmTRACE::$ERROR){
	$this->SetPreference('loglevel', $params['level']);
}

$this->redirect($id,'defaultadmin');


?>