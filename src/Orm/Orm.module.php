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

	private static $isInitiated = false;

	function __construct(){

        spl_autoload_register(array($this,'autoload'));

		//Required to preserve the {Module} on Front-Office
		parent::__construct();

		//Load Entity in memory
		if(!self::$isInitiated && $this->GetName() !== self::GetName()) {
			self::scan();
			$isInitiated = true;
		}

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
		$liste = array('entities' => array(), 'associate' => array());

		$files = $this->getFilesInDir($dir, "#^class\.entity\.(.)*$#");
		foreach ($files as $path => $filename) {
		    $classname = substr($filename , 13 ,strlen($filename) - 4 - 13);
		    $liste['entities'][] = array('filename'=>$filename, 'basename'=>$path, 'classname'=>$classname);
		}
		$files = $this->getFilesInDir($dir, "#^class\.assoc\.(.)*$#");
		foreach ($files as $path => $filename) {
		    $classname = substr($filename , 12 ,strlen($filename) - 4 - 12);
		    $liste['associate'][] = array('filename'=>$filename, 'basename'=>$path, 'classname'=>$classname);
		}

		$errors = array();
		
		foreach($liste['entities'] as $element) {
			$className = $element['classname'];
			$filename = $element['filename'];
			$basename = $element['basename'];
			if(!class_exists($className)){
				OrmTrace::debug("importing Entity ".$className." into the module ".$this->getName());
				require_once($basename);	
			}

			try{
				$entity = new $className();
			} catch(OrmIllegalConfigurationException $oce){
				$errors[$className] = $oce;
				continue;
			}
		}

		foreach($liste['associate'] as $element) {
			$className = $element['classname'];
			$filename = $element['filename'];
			$basename = $element['basename'];
			if(!class_exists($className)){
				OrmTrace::debug("importing Associate Entity ".$className." into the module ".$this->getName());
				require_once($basename);	
			}

			try{
				$entity = new $className();
			} catch(OrmIllegalConfigurationException $oce){
				$errors[$className] = $oce;
				continue;
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

	/**
     * An extended autoload method.
     * Search for classes a <module>/lib/class.classname.php file.
     * or for interfaces in a <module>/lib/interface.classname.php file.
     * or as a last ditch effort, for simple classes in the <module>/lib/extraclasses.php file.
     * This method also supports namespaces,  including <module> and <module>/sub1/sub2 which should exist in files as described above.
     * in subdirectories below the <module>/lib directory.
     *
     * @internal
     * @param string $classname
     */
    public function autoload($classname)
    {
        if( !is_object($this) ) return FALSE;

        // check for classes.
        $path = $this->GetModulePath().'/lib';
        if( strpos($classname,'\\') !== FALSE ) {
            $t_path = str_replace('\\','/',$classname);
            if( startswith( $t_path, $this->GetName().'/' ) ) {
                $classname = basename($t_path);
                $t_path = dirname($t_path);
                $t_path = substr($t_path,strlen($this->GetName())+1);
                $path = $this->GetModulePath().'/lib/'.$t_path;
            }
        }

        $fn = $path."/class.{$classname}.php";
        if( file_exists($fn) ) {
            require_once($fn);
            return TRUE;
        }

        // check for interfaces
        $fn = $path."/interface.{$classname}.php";
        if( file_exists($fn) ) {
            require_once($fn);
            return TRUE;
        }

        // check for a master file
        $fn = $this->GetModulePath()."/lib/extraclasses.php";
        if( file_exists($fn) ) {
            require_once($fn);
            return TRUE;
        }

        // check for interfaces
        $fn = $path."/class.entity.{$classname}.php";
        if( file_exists($fn) ) {
            require_once($fn);
            return TRUE;
        }

        return FALSE;
    }

	function GetName() {
		return 'Orm';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.4.0-SNAPSHOT';
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
	 * Shortcut to call all the instances for a single module
	 *
	 * @return List<OrmEntity> the entities for the current parent's namespace
	 **/
	public function getAllInstances(){
		return MyAutoload::getAllInstances(parent::GetName());
	}

	/**
     * Will return the list of file in the directory wich match the pattern.
     *
     * @param directory the directory
     * @param pattern the pattern
     *
     * @return Mixed[] list of files
     **/
    public static function getFilesInDir($directory, $pattern, $recursive = TRUE){
        $files = array();
        if(!is_dir($directory)){
            return null;
        }
        if ($handle = opendir($directory)) {
            while (false !== ($entry = readdir($handle))) {
            	if($entry == "." || $entry == ".."){
            		continue;
            	}
                if (!is_dir($directory.'/'.$entry) && preg_match( $pattern , $entry)) {
                   $files[$directory.'/'.$entry] = $entry;
                   continue;
                }

                if($recursive && is_dir($directory.'/'.$entry) ) {
                	$files = array_merge($files, 
                	 		self::getFilesInDir($directory.'/'.$entry, $pattern, $recursive));
                }
            }
            closedir($handle);
        }
        return $files;
    }
	
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
