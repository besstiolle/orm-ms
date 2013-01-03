<?php
/**
 * Contient les principales classes utilisées pour gérer les moteurs de recherche dans le front-office
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/

/**
 *   Représente un champs du moteur de recherche
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
class SeachElement
{
	private $name;
	private $fieldname;
	private $typeCritere;
	private $searchField;
	
    /**
    * constructeur public
    * 
    * @param string le nom du champs de recherche. Doit être unique dans le moteur de recherche
    * @param string le nom du champs de l'entité liée.
    * @param TypeCritere le Type de Critère associé (égalité absolue, supérieur à...)
    * @param SEARCH_FIELD le champs HTML auto-généré dédié aux moteurs de recherche
    * 
    * @return SeachElement un champs de moteur de recherche.
    */
	public function __construct($name,$fieldname,$typeCritere, $searchField)
	{	
		$this->name = $name;
		$this->fieldname = $fieldname;
		$this->typeCritere = $typeCritere;
		$this->searchField = $searchField;
	}
       
   /**
    * function getter
    * 
    * @return string le nom du champs
    */
	public function getName()
	{return $this->name;} 
       
   /**
    * function getter
    * 
    * @return string le nom du champs de l'entité liée
    */
	public function getFieldname()
	{return $this->fieldname;} 
       
   /**
    * function getter
    * 
    * @return TypeCritere le Type de Critère associé (égalité absolue, supérieur à...)   
    */
	public function getTypeCritere()
	{return $this->typeCritere;} 
       
   /**
    * function getter
    * 
    * @return SEARCH_FIELD le champs HTML auto-généré dédié aux moteurs de recherche
    */
	public function getSearchField()
	{return $this->searchField;}
}


?>