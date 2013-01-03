<?php
/**
 * Contient les classe mères de toutes les entités et entités associatives
 * @package mmmfs
 **/
 
/**
 * Class abstract décrivant le comportement et les propriétés d'une Entité Associative
 *    
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
abstract class EntityAssociation extends Entity 
{
	private $nbField = 0;

     /**
    * Constructeur semi-privé pour éviter d'instancer cette classe depuis le code par erreur
    * 
    *   A chaque construction, l'entité est placée dans l'Autoloader
    * 
    * @param string le nom d'un module de type Mmmfs
    * @param string le nom de l'entité
    * @param string le préfixe à utiliser en base de donnée pour les tables. En général le nom de votre module
    * @param string si renseigné sera le nom de la table liée à cette entité. Si non renseignée on prendra le nom de l'entité
    * @return EntityAssociation l'entite servant de modèle
    * 
    * @see MyAutoload
    */
	protected function __construct($moduleName, $name,$prefixe = null, $dbName = null)
	{
		parent::__construct($moduleName, $name, $prefixe, $dbName);
	}
	
    /**
    * Ajoute un champs 
    * 
    * Le programme ne gère que deux champs par EntityAssociation
    * 
    * @param Field le champs à ajouter.
    */
	protected function add(Field $newField)
	{
		$this->nbField ++;
		
		if($this->nbField > 2)
			throw new Exception ("impossible d'utiliser une table d'association avec plus de 2 champs cle. Entite : ". $this->getName());
				
		parent::add($newField);
	}

}