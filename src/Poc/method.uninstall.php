<?php

if (!function_exists("cmsms")) exit;

//Drop all the tables
$entities = MyAutoload::getAllInstances($this->GetName(), $this->getName());
foreach($entities as $anEntity)
{
	Core::dropTable($anEntity);
}

// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>