<?php
	/**
	* Classe of cmsmadesimple's API. Used to make a link between the API of CmsMadeSimple and other modules'
	*
	* @since 0.0.1
	* @author Bess
	* @package Orm
	**/

	//Unique import, to use the Trace into this classe
	include_once(cms_join_path(dirname(__FILE__),"lib","class.trace.php"));

	//Uncomment the line below to activate the debug mode
	#Trace::$level = Trace::$DEBUG;
	//Other value : $DEBUG|$INFO|$WARN|$ERROR

	/**
	* The Class Orm define the module Orm and allow having all the orm functionnalities into another module
	*
	* @since 0.0.1
	* @author Bess
	* @package cmsms
	*/
class Orm extends CMSModule {

	function __construct() {
		parent::__construct();
	}

	function GetName() {
		return 'Orm';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.0.2';
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

	protected function __autoload() {	
		spl_autoload_register(array($this, 'autoload_classes'));
		//spl_autoload_register(array($this, 'autoload_classes_addon'));
		
		//On liste les classes déclarées dans le répertoire du module enfant
		$repertoire = cms_join_path($this->GetMyModulePath(),'lib');
		
		$liste['entities'] = array();
		$liste['associate'] = array();
		//$liste[''] = array();
		
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($repertoire));
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
		
		foreach($liste['entities'] as $element)
		{
			Trace::debug("import entite ".$element['filename']." dans le module ".$this->getName()."<br/>");
			require_once($element['filename']);
			eval('$entity = new '.$element['classname'].'();');
		}
		foreach($liste['associate'] as $element)
		{
			Trace::debug("import associate entite ".$element['filename']." dans le module ".$this->getName()."<br/>");
			require_once($element['filename']);
			eval('$entity = new '.$element['classname'].'();');
		}
	}
	
	public function autoload_classes($classname){
		Trace::debug("&nbsp;&nbsp;&nbsp; Need $classname<br/>");
		$Orm = new Orm();
		$path = $Orm->GetMyModulePath();
		$fn = cms_join_path($path,"lib","class.".strtolower($classname).".php");


		Trace::debug("import class du framework via ".$this->getName().": $fn<br/>");

		if(file_exists($fn)){
			require_once($fn);
			Trace::debug("import $fn ok<br/>");
		} else {
			Trace::debug("fichier $fn introuvable, on passe<br/>");
		}
	}
	
	/**
	 * Shortcut to call all the instances for a single module
	 *
	 * @return List<Entity> the entities for the current parent's namespace
	 **/
	public function getAllInstances(){
		return MyAutoload::getAllInstances(parent::GetName());
	}
	/*
	public function autoload_classes_addon($classname){
		Trace::debug("&nbsp;&nbsp;&nbsp;$classname<br/>");
		
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
			Trace::debug( "import d'un addon du projet ".$this->getName().": $fn<br/>");
	   
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
			
		// Indexing::setSearch($module);
		Indexing::SearchReindex($this->getName());
	}*/

	/**
	 * Appelée par Search pour afficher un résultat
	 *//*
	function SearchResult($returnid, $entityId, $attr = '') {	
		//On ne retourne rien de Mmmfs de toute manière
		if($this->getName() == 'Mmmfs')
			return;	
		
		return Indexing::SearchResult($this, $id, $returnid, $entityId, $attr);
	}*/

} 
?>
