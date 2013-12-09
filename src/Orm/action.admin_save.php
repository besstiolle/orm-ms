<?php

if (!function_exists("cmsms")) exit;

if (isset($params['delete'])){
	unlink(TRACE::getLogFile());
	Trace::info("Reinitiate the log file");
} else if (isset($params['level']) && $params['level'] >= TRACE::$DEBUG && $params['level'] <= TRACE::$ERROR){
	$this->SetPreference('loglevel', $params['level']);
}

$this->redirect($id,'defaultadmin');


?>