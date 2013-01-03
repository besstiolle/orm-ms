<?php
/**
 * Contient les principales classes utilisées pour gérer les moteurs de recherche dans le front-office
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/

/**
 *   Représente une entité dédiée à la recherche.
 * 
 * Un moteur de recherche se base toujours sur une entité existante. 
 * Un moteur de recherche contient un ou plusieurs champs (SearchElement) toujours liés à un champs (FIELD) de l'entité'
 * Plusieurs Champs (SearchElement) peuvent être lié à un même champs (FIELD) d'une entité, l'utilisation par exemple d'une recherche avec un prix mini et un prix maxi
 * 
 * La composition d'un moteur de recherche est assez semblable à celle d'une entité, par exemple :
 * 
 * <code>
 *  class SearchClient extends SearchCore
 *  {
 *   public function __construct()
 *   {
 *       parent::__construct('search_client','client'); //liaison sur l'entité Client
 *       
 *        $this->listeElements['codepostal'] =    
 *              new SeachElement('codepostal', 'codepostal', mTypeCritere::$EQ, new SEARCH_FIELD_SELECT());     
 * 
 *        $this->listeElements['agemin'] =         
 *              new SeachElement('agemin',     'age',        mTypeCritere::$GTE, new SEARCH_FIELD_TEXT());
 * 
 *        $this->listeElements['agemax'] =        
 *              new SeachElement('agemax',     'age',        mTypeCritere::$LTE, new SEARCH_FIELD_TEXT());
 *                                                                                                                    
 *   }
 *  }
 * </code>
 * 
 *   Donnera un moteur de recherche de client par leur code postal avec possibilité de préciser l'age minimum et/ou l'age maximum du client
 * 
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/  
class SearchCore 
{	
	protected $listeElements;
	protected $entityName;
	protected $name;
	
    /**
    * Constructeur public
    * 
    *  A noter que la classe une fois créée se charge automatiquement dans l'Autoloader
    * 
	* @param string le nom d'un module de type Mmmfs
    * @param string le nom du moteur de recherche. Doit être unique dans toute l'application
    * @param string $entityName le nom de l'entité liée. Doit être existante
    * @return SearchCore le moteur de recherche
    */
	public function __construct($modulename, $name, $entityName)
	{
		$this->name = strtolower($name);
		$this->entityName = strtolower($entityName);
		$this->listeElements = array();
		
		//On ajoute une instance de soi dans l'autoload
		myAutoload::addInstance(strtolower($modulename), $this);
	}
   
   /**
    * function getter
    * 
    * @return string le nom du moteur de recherche
    */
	public function getName()
	{return $this->name;}
	   
   /**
    * function getter
    * 
    * @return string le nom de l'entité liée
    */
	public function getEntityName()
	{return $this->entityName;}
	
      
   /**
    * function getter
    * 
    * @return array la liste des SearchElement du moteur de recherche
    */
	public function getListeElements()
	{return $this->listeElements;}
	
}

?>