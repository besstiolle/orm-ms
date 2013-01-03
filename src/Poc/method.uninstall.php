<?php

if (!function_exists("cmsms")) exit;
/*
//Drop the table
$entities = MyAutoload::getAllInstances($this->getName());
foreach($entities as $anEntity)
{
	Core::dropTable($this,$anEntity);
}*/

// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>