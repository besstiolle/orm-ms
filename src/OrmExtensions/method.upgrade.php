<?php

if (!function_exists("cmsms")) exit;


switch($oldversion){
	case '0.3.2':
}


// put mention into the admin log
$this->Audit( 0,  $this->Lang('friendlyname'),  $this->Lang('upgraded', $this->GetVersion()));


?>