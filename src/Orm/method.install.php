<?php

if (!function_exists("cmsms")) exit;


$this->SetPreference('loglevel', OrmTrace::$INFO);
$this->SetPreference('cacheType', OrmCache::$NONE);

$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>