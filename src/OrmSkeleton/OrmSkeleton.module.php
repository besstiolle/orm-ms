<?php

class OrmSkeleton extends Orm{

	function GetName() {
		return 'OrmSkeleton';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.3.4-SNAPSHOT';
	}

	function GetDependencies() {
		return array('Orm'=>'0.3.4-SNAPSHOT');
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
		return "1.11.12";
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
		return true;
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

	function DisplayErrorPage($msg) {
		echo "<h3>".$msg."</h3>";
	}  
	
	/**
	 * a inner function for factorize some recurrent code
	 **/
	function securize($str){
		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}
} 
?>
