<?php

if (!function_exists("cmsms")) exit;


if( version_compare($oldversion, '0.2.2') < 0 ) {
	$this->CreatePermission('Manage_Orm', 'Manage Orm');
}

// put mention into the admin log
$this->Audit( 0,  $this->Lang('friendlyname'),  $this->Lang('upgraded', $this->GetVersion()));


?>