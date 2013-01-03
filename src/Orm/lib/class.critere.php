<?php
/**
 * Contient toutes les fonctionnalités relatives aux recherches par Critères
 * 
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
 
 

/**
* Classe structurant un critère 
* 
* Contient un nom de champs, un TypeCritere, un tableau de paramètre et un boolean pour la casse
*
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
final class Critere
{
	public $fieldname;
	public $typeCritere;
	public $paramsCritere;
	public $ignoreCase;
	
    /**
    * Constructeur public
    * 
    * @param string le nom du champs Field
    * @param TypeCritere une valeur de la class TypeCritere 
    * @param array un tableau de paramètres utilisés en association avec le paramètre $typeCritere
    * @param boolean $ignoreCase faux par défaut, spécifie si l'on souhaite utiliser le TypeCritere avec ou sans casse (aze != AZE)
    * @return Critere
    */
	public function __construct($fieldname, $typeCritere, $paramsCritere, $ignoreCase = false)
	{
		$this->fieldname = $fieldname;
		$this->typeCritere = $typeCritere;
		$this->paramsCritere = $paramsCritere;
		$this->ignoreCase = $ignoreCase;
	}
	
}

?>