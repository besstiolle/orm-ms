<?php
/**
 * Contient les principales classes utilisées pour gérer les Fields
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
 
/**
 *   Représente un champs d'une entité dans son ensemble, depuis la base de donnée à l'affichage 
 *    en passant par les liaisons inter-entité
 * 
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
class Field 
{
	private $name;
	private $type;
	private $size;
	
	private $KEY;
	private $KEYName;
	
	private $HTML;
	private $nullable;
	
	public static $ISNULLABLE = true;
	
    /**
    * constrcteur public 
    * 	
    * @param string le nom du champs (doit être unique pour toute l'entité)
    * @param CAST une propriété de la classe CAST. représente le typage du champs, exemple CAST::$INTEGER
    * @param int la taille max du champs en base et dans l'application. N'est pas nécessaire si le typage du champs est un CAST::$BUFFER, CAST::NONE, ...
    * @param nullable définit si le champs est facultatif en base, exemple Field::$ISNULLABLE pour un champs facultatif, laisser à vide pour un champs obligatoire
    * @param KEY une propriété de la classe KEY ou null, représente les clés primaires, étrangères ou associative, exemple KEY::$PK pour une clé primaire
    * @param string uniquement utilisable avec KEY::$FK et KEY::$AK. Chaine de liaison de l'éventuelle clé étrangère ou associative. Mettre à null si inutilisé. exemple : "Client.client_id" dans le Field "client" servant de clé étrangère pour l'entité "Commande" 
   
    * 
    * @return Field le champs définit prêt à être stocké dans une entité.
    * 
    * 
    * @see CAST
    * @see KEY
    * @see NULLABLE
    * 
    */
	public function __construct($fieldname, $cast, $size = null, $nullable = null, $KEY = null, $KEYName=null)
	{
		if(empty($KEY) && !empty($KEYName))
		{
			throw new IllegalConfigurationException('Impossible de specifier le nom d\'une cle etrangere si le type de cle n\'est pas definie a $PK ou a $AK');
		}
		if($KEY == KEY::$PK && !empty($KEYName))
		{
			throw new IllegalConfigurationException('la cle $PK n\'accepte aucun nom de cle etrangere');
		}
		if(($KEY == KEY::$FK || $KEY == KEY::$AK) && empty($KEYName))
		{
			throw new IllegalConfigurationException('les cles $FK et $AK imposent un nom de cle etrangere');
		}
		
		if(($cast == CAST::$DATE || $cast == CAST::$BUFFER) && !empty($size))
		{
			throw new IllegalConfigurationException('Le typage DATE ou BUFFER du champs '.$fieldname.' ne peut posseder une taille');
		}
		

		if($nullable == null)
			$nullable = false;
			
		$this->name 	= $fieldname;
		$this->type 	= $cast;
		$this->size 	= $size;
		$this->nullable = $nullable;
		$this->KEY 		= $KEY;
		$this->KEYName 	= $KEYName;
	}
	

    /**
    * function getter
    * 
    * @return string le nom du Field
    */
	public function getName()
	{return $this->name;}

    /**
    * function getter
    * 
    * @return string le cast du Field
    * 
    * @see CAST
    * 
    */	
	public function getType()
	{return $this->type;}
	
   /**
    * function getter
    * 
    * @return int la taille max du champs
    */
	public function getSize()
	{return $this->size;}

   /**
    * function getter
    * 
    * @return boolean vrai si le champs est une clé primaire
    */	
	public function isPrimaryKEY()
	{return $this->KEY == KEY::$PK;}

   /**
    * function getter
    * 
    * @return boolean vrai si le champs est une clé étrangère
    */    	
	public function isForeignKEY()
	{return $this->KEY == KEY::$FK;}

   /**
    * function getter
    * 
    * @return boolean vrai si le champs est une clé d'association
    */    	
	public function isAssociateKEY()
	{return $this->KEY == KEY::$AK;}
	
    /**
    * function getter  
    * 
    * @return string la chaine de liaison de l'éventuelle clé étrangère ou associative
    * 
    */
	public function getKEYName()
	{return $this->KEYName;}
	
    /**
    * function getter  
    * 
    * @return true si le champs Field est facultatif. renvoit faux si champs Field obligatoire
    */
	public function isNullable()
	{return $this->nullable;}
	
}

?>