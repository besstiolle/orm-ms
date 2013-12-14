<?php

if (!function_exists("cmsms")) exit;

//Reinit the default level of OrmTrace
$level = $this->GetPreference('loglevel', OrmTrace::$INFO);
$cache = $this->GetPreference('cacheType', OrmTrace::$NONE);

$this->SetPreference('loglevel', $level);
$this->SetPreference('cacheType', $cache);

// put mention into the admin log
$this->Audit( 0,  $this->Lang('friendlyname'),  $this->Lang('upgraded', $this->GetVersion()));


?>