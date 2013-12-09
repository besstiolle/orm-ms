<?php

if (!function_exists("cmsms")) exit;

//Reinit the default level of Trace
$level = $this->GetPreference('loglevel', Trace::$INFO);
$this->SetPreference('loglevel', $level);

// put mention into the admin log
$this->Audit( 0,  $this->Lang('friendlyname'),  $this->Lang('upgraded', $this->GetVersion()));


?>