<?php

if (!function_exists("cmsms")) exit;

$config = cmsms()->GetConfig();
if( !class_exists("OrmTrace")) {
	require_once($config['root_path'].'/modules/Orm/lib/class.OrmTrace.php');
}
if( !class_exists("OrmCache")) {
	require_once($config['root_path'].'/modules/Orm/lib/class.OrmCache.php');
}

$this->SetPreference('loglevel', OrmTrace::$INFO);
$this->SetPreference('cacheType', OrmCache::$NONE);

$this->CreatePermission('Manage_Orm', 'Manage Orm');

$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>