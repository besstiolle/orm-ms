<?php

if (!function_exists("cmsms")) exit;

//Drop all the tables and recreate them
$entities = MyAutoload::getAllInstances($this->GetName(), $this->getName());
foreach($entities as $anEntity) {
	Core::dropTable($anEntity);
	Core::createTable($anEntity);
}

// put mention into the admin log
$this->Audit( 0,  $this->Lang('friendlyname'),  $this->Lang('upgraded', $this->GetVersion()));


?>