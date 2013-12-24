<?php

if (!function_exists("cmsms")) exit;

$config = cmsms()->GetConfig();
if( !class_exists(OrmTrace)) {
	require_once($config['root_path'].'/modules/Orm/lib/class.ormtrace.php');
}
if( !class_exists(OrmCache)) {
	require_once($config['root_path'].'/modules/Orm/lib/class.ormcache.php');
}

$this->SetPreference('loglevel', OrmTrace::$INFO);
$this->SetPreference('cacheType', OrmCache::$NONE);

$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>