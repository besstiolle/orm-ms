<?php

if (!function_exists("cmsms")) exit;


if( version_compare($oldversion, '0.2.2') < 0 ) {
	$this->CreatePermission('Manage_Orm', 'Manage Orm');
}

switch($oldversion){
	case '0.2.2':
		$this->SetPreference('useCache', true);
	case '0.3.0':
	case '0.3.1':
	case '0.3.2':
	case '0.3.3':
}



// put mention into the admin log
$this->Audit( 0,  $this->Lang('friendlyname'),  $this->Lang('upgraded', $this->GetVersion()));


?>