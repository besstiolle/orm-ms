<?php
/**
* Class of cmsmadesimple's API. Used to make a link between the API of CmsMadeSimple and other modules'
*
* @since 0.0.1
* @author Bess
* @package Orm
**/

	/**
	* The Class Orm define the module Orm and allow having all the orm functionalities into another module
	*
	* @since 0.0.1
	* @author Bess
	* @package cmsms
	*/
class Orm extends CMSModule {

	function __construct(){

		//Required to preserve the {Module} on Front-Office
		parent::__construct();

		//Load all the librairies for ORM exclusivly
		if($this->GetName() === self::GetName()){
			$dir = parent::GetModulePath().'/lib/'; 
			$libs = scandir ( $dir );

			//FIXME : try to avoid conflict during loading class  xx extends yy
			sort($libs);

			foreach ($libs as $librairy) {
				if($librairy !== '.' && $librairy !== '..' && strpos($librairy, '.php', strlen($librairy) - strlen('.php')) !== FALSE ){
					require_once($dir.$librairy);
				}
			}
		} else {
			$this->scan();
		}
	}

	function GetName() {
		return 'Orm';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.3.1';
	}
  
	function GetDependencies()
 	{
    	return array();
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
	
	function DisplayErrorPage($msg) {
		echo "<h3>".$msg."</h3>";
	} 
		 
	private function GetMyModulePath() {
		return parent::GetModulePath();		
	}

	/**
	 * Will found every Entity for the current module and return the liste of their name
	 *
	 * @return Array<String> a list of name of entities founded
	 *
	 **/
	protected function scan(){

		//We're listing the class declared into the directory of the child module
		$dir = cms_join_path(parent::GetModulePath(),'lib');

		$liste['entities'] = array();
		$liste['associate'] = array();
		
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
		foreach($objects as $name => $object){
			if(stripos($name, 'class.entity.') !== FALSE){			
				$classname = substr($object->getFileName() , 13 ,strlen($object->getFileName()) - 4 - 13);
				$liste['entities'][] = array('filename'=>$name, 'basename'=>$object->getFileName(), 'classname'=>$classname);
			} elseif(stripos($name, 'class.assoc.') !== FALSE) {
				$classname = substr($object->getFileName() , 12 ,strlen($object->getFileName()) - 4 - 12);
				$liste['associate'][] = array('filename'=>$name, 'basename'=>$object->getFileName(), 'classname'=>$classname);
			} else {
			}
		}

		$errors = array();
		
		foreach($liste['entities'] as $element) {
			$className = $element['classname'];
			$filename = $element['filename'];
			if(!class_exists($className)){
				OrmTrace::debug("importing Entity ".$className." into the module ".$this->getName());
				require_once($filename);	
				try{
					$entity = new $className();
				} catch(OrmIllegalConfigurationException $oce){
					$errors[$className] = $oce;
					continue;
				}
			}
		}

		foreach($liste['associate'] as $element) {
			$className = $element['classname'];
			$filename = $element['filename'];
			if(!class_exists($className)){
				OrmTrace::debug("importing Associate Entity ".$className." into the module ".$this->getName());
				require_once($filename);
				$entity = new $className();
			}
		}

		//Process all the errors
		if(!empty($errors)){
			echo '<h3 style="color:#F00">Some OrmIllegalConfigurationException have been thrown</h3>';
			
			foreach ($errors as $className => $error) {
				echo '<h4>Entity '.$className.' </h4><ol>';
				foreach ($error->getMessages() as $message) {
					echo '<li>'.$message.'</li>';
				}
				echo '</ol>';
			}
			
			exit;
		}

		return $liste;
	}
	/*
	public function autoload_framework($classname){
		echo "autoload_framework($classname)";
				
		//$Orm = new Orm();
		//$path = $Orm->GetMyModulePath();
		$path = parent::GetModulePath();
		$fn = cms_join_path($path,"lib","class.".$classname.".php");
		
		if(file_exists($fn)){
			require_once($fn);
			return;
		} 
	}*/
	
	/**
	 * Shortcut to call all the instances for a single module
	 *
	 * @return List<OrmEntity> the entities for the current parent's namespace
	 **/
	public function getAllInstances(){
		return MyAutoload::getAllInstances(parent::GetName());
	}
	/*
	public function autoload_classes_addon($classname){
		OrmTrace::debug("&nbsp;&nbsp;&nbsp;$classname");
		
		$path = $this->GetMyModulePath();
		
		$fn = null;
	   if(stripos($classname, "HTML_FIELD") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.fieldhtml.php");
	   } else if(stripos($classname, "SEARCH_FIELD") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.searchfield.php");
	   } else if(stripos($classname, "FIELD_") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.fieldssystem.php");
	   } else if(stripos($classname, "FILTRE_") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.filtre.php");
	   }
	   
	   if($fn != null)
	   {
			OrmTrace::debug( "import d'un addon du projet ".$this->getName().": $fn");
	   
			if(file_exists($fn)){
				require_once($fn);
			}
		}
	}*/
	/*
	function SearchReindex(&$module = null) {
		//On évite de s'auto-indexer.
		if($this->getName() == 'Mmmfs')
			return;		
			
		// OrmIndexing::setSearch($module);
		OrmIndexing::SearchReindex($this->getName());
	}*/

	/**
	 * Appelée par Search pour afficher un résultat
	 *//*
	function SearchResult($returnid, $entityId, $attr = '') {	
		//On ne retourne rien de Mmmfs de toute manière
		if($this->getName() == 'Mmmfs')
			return;	
		
		return OrmIndexing::SearchResult($this, $id, $returnid, $entityId, $attr);
	}*/

} 
?>
