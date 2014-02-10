<?php

if (!function_exists("cmsms")) exit;

$this->RemovePreference('loglevel');
$this->RemovePreference('cacheType');

$this->RemovePermission('Manage_Orm');

// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>