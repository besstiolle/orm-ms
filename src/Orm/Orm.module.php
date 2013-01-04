<?php
	/**
	* Classe de l'API de cmsmadesimple. Sert à faire la liaison entre l'API de CmsMadeSimple et le framework MmmFS'
	*
	* @since 1.0
	* @author Bess
	* @package cmsms
	**/

	//Unique import, utilisé pour gérer les traces en sortie
	include_once(cms_join_path(dirname(__FILE__),"lib","class.trace.php"));

	#Décommenter pour obtenir le mode debug complet. Autres valeurs possibles : $DEBUG|$INFO|$WARN|$ERROR
	#Trace::$level = Trace::$DEBUG;

	/**
	* Classe Mmmfs définit le module Mmmfs et permet ainsi d'avoir toutes les classes du framework à disposition dans CmsMadeSimple
	*
	* @since 1.0
	* @author Bess
	* @package cmsms
	*/
class Orm extends CMSModule {

	function __construct()
	{
		parent::__construct();
	}

	function GetName()
	{
		return 'Orm';
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
    	return array();
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
		return false;
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
	//private static $instances;
		 
	private function GetMyModulePath()
	{
		$config = cmsms()->getConfig();
		if(strpos(parent::GetModulePath(), $config['root_path']) !== FALSE)
			return parent::GetModulePath();
		else
			return cms_join_path($config['root_path'], 'modules', $this->getName());
	}

	protected function __autoload()
	{	
		spl_autoload_register(array($this, 'autoload_classes'));
		//spl_autoload_register(array($this, 'autoload_classes_addon'));
		
		//On liste les classes déclarées dans le répertoire du module enfant
		$repertoire = cms_join_path($this->GetMyModulePath(),'lib');
		$dir = opendir($repertoire); 
		while($file = readdir($dir)) {$liste[] = $file;}
		closedir($dir);
		
		foreach($liste as $file)
		{
			if($file != "." && $file != ".." && !is_dir($file) 
				&& stripos($file, 'class.') !== FALSE
				&& stripos($file, 'class.add.') === FALSE)
			{
				Trace::debug("import class du module ".$this->getName().": ".cms_join_path($repertoire,$file)."<br/>");
				require_once(cms_join_path($repertoire,$file));
			} 
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
	function SearchReindex(&$module = null)
	{
		//On évite de s'auto-indexer.
		if($this->getName() == 'Mmmfs')
			return;		
			
		// Indexing::setSearch($module);
		Indexing::SearchReindex($this->getName());
	}*/

	/**
	 * Appelée par Search pour afficher un résultat
	 *//*
	function SearchResult($returnid, $entityId, $attr = '')
	{	
		//On ne retourne rien de Mmmfs de toute manière
		if($this->getName() == 'Mmmfs')
			return;	
		
		return Indexing::SearchResult($this, $id, $returnid, $entityId, $attr);
	}*/

} 
?>
