<?php

class OrmExtensions extends Orm {

	function GetName() {
		return 'OrmExtensions';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.4.0-SNAPSHOT';
	}
  
	function GetDependencies()
 	{
		return array('Orm'=>'0.4.0-SNAPSHOT');
	}

	function GetHelp() {
		return $this->Lang('help');
	}

	function GetAuthor() {
		return 'Kevin Danezis (aka Bess)';
	}

	function GetAuthorEmail() {
		return 'contact at furie point be';
	}

	function GetChangeLog() {
		return $this->Lang('changelog');
	}

	function GetAdminDescription() {
		return $this->Lang('moddescription');
	}

	function MinimumCMSVersion() {
		return "1.11.13";
	}

	function IsPluginModule() {
		return false;
	}

	function HasAdmin() {
		return true;
	}

	function GetAdminSection() {
		return 'extensions';
	}

	function VisibleToAdminUser() {
		return $this->CheckPermission('Manage_Orm');
	}

	function InitializeFrontend() {
	}

	function InitializeAdmin() {
	}

	function AllowSmartyCaching() {
		return false;
	}

	function LazyLoadFrontend() {
		return false;
	}

	function LazyLoadAdmin() {
	  return false;
	}
	
	function InstallPostMessage() {
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage() {
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage() {
		return $this->Lang('really_uninstall');
	}  
	
} 
?>
