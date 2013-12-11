<?php

/* Force the loading of Orm Framework BEFORE this module */
$config = cmsms()->GetConfig();
$Orm = $config['root_path'].'/modules/Orm/Orm.module.php';
if( !is_readable( $Orm ) ) {
  echo '<h1><font color="red">ERROR: The Orm Framework could not be found [<a href="https://github.com/besstiolle/orm-ms/wiki">help</a>].</font></h1>';
  return;
}
require_once($Orm);

class OrmUT extends Orm {   

	function __construct() {
		parent::__autoload();
		parent::__construct();
	}

	function GetName() {
		return 'OrmUT';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.1.1';
	}

	function GetDependencies() {
		return array('Orm'=>'0.1.1');
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
		return "1.11.0";
	}

	function IsPluginModule() {
		return true;
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

	function SetParameters() {
		$this->RegisterModulePlugin();
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
} 

?>
