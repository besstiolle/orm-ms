<?php
/*
define('Poc_memory', number_format(memory_get_usage(), 0, '.', ',') . " octets<br/>");

//Inclusion par la force du module Mmmfs pour etre certain d'avoir acces aux fonctionnalites du framework
if(!class_exists("Mmmfs")){
	$mmmfs = cms_join_path(cmsms()->config['root_path'],'modules','Mmmfs','Mmmfs.module.php');
	if( !is_readable( $mmmfs ) )
		{echo '<h1><font color="red">ERROR: The Mmmfs module could not be found.</font></h1>';return;}
	require_once($mmmfs);
}
//Inclusion par la force du module FeuUtil pour etre certain d'avoir acces aux fonctionnalites du framework
if(!class_exists("FeuUtil")){
  $FeuUtil = cms_join_path(cmsms()->config['root_path'],'modules','Poc','lib','feuUtil.php');
  if( !is_readable( $FeuUtil ) )
    {echo '<h1><font color="red">ERROR: The FeuUtil tools could not be found.</font></h1>';return;}
  require_once($FeuUtil);
}*/

class Poc extends Orm
{   
	function __construct()
	{
		$this->autoload();
		parent::__construct();
	}

	function GetName()
	{
		return 'Poc';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '0.0.1';
	}

	function GetDependencies()
	{
		return array('Orm'=>'0.0.1');
	}

	function GetHelp()
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Kevin Danezis (aka Bess)';
	}

	function GetAuthorEmail()
	{
		return 'contact at furie point be';
	}

	function GetChangeLog()
	{
		return $this->Lang('changelog');
	}

	function GetAdminDescription()
	{
		return $this->Lang('moddescription');
	}

	function MinimumCMSVersion()
	{
		return "1.11.0";
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetAdminSection()
	{
		return 'extensions';
	}

	function VisibleToAdminUser()
	{
		return true;
	}

	function InitializeFrontend()
	{
	}

	function InitializeAdmin()
	{
	}

	function AllowSmartyCaching()
	{
		return false;
	}

	function LazyLoadFrontend()
	{
		return false;
	}

	function LazyLoadAdmin()
	{
		return false;
	}

	function SetParameters()
	{
		$this->RegisterModulePlugin();
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage()
	{
		return $this->Lang('really_uninstall');
	}

	function DisplayErrorPage($msg)
	{
		echo "<h3>".$msg."</h3>";
	}  
} 

?>
