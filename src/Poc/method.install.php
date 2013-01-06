<?php

if (!function_exists("cmsms")) exit;

//Create all the tables
$entities = MyAutoload::getAllInstances($this->GetName());
foreach($entities as $anEntity)
{
	Core::createTable($anEntity);
}

// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>
