<?php

if (!function_exists("cmsms")) exit;

/*
$entities = MyAutoload::getAllInstances($this->getName());
foreach($entities as $anEntity)
{
	Core::createTable($this,$anEntity);
}*/

$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>
?>