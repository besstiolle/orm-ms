<?php
/**
 * Contains the class Field
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
/**
 *   Represent a Entity's field with all its properties
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
class Field 
{
	/**
	 * The (unique) name of the field
	 */
	private $name;
	
	/**
	 * The CAST value of the field
	 */
	private $type;
	
	/**
	 * The max size of the field. May be null if $type is Date, Time, Buffer, None ...
	 */
	private $size;
	
	/**
	 * Boolean : true if the value may be NULL
	 */
	private $nullable;
	
	/**
	 * the KEY value of the field. May be null
	 */
	private $KEY;
	
	/**
	 * the name of the key. Only used for ForeignKey and AssociateKey
	 */
	private $KEYName;
	
	/**
	 * The default value. Not used by default.
	 **/
	private $defaultValue;
	
    /**
    * public constructor
    * 	
    * @param string the (unique) name of the field
    * @param CAST The CAST value of the field example : CAST::$INTEGER
    * @param int The max size of the field. May be null if $type is Date, Time, Buffer, None ...
    * @param true if the value may be NULL. Default value is false
    * @param KEY the KEY value of the field. May be null example: KEY::$PK for a primary key
    * @param string the name of the key. Only used for ForeignKey and AssociateKey example : "Customer.customer_id" in the field "customer" of an entity "Order".
    * 
    * @return Field the Field Object
    * 
    * 
    * @see CAST
    * @see KEY
	* @see Entity
    * 
    */
	public function __construct($fieldname, $cast, $size = null, $nullable = false, $KEY = null, $KEYName=null) {
	
		if(empty($KEY) && !empty($KEYName)) {
			throw new IllegalConfigurationException('Impossible to specify a keyName parameter for the field '.$fieldname.' if the key is not $FK or $AK');
		}
		if($KEY == KEY::$PK && !empty($KEYName)) {
			throw new IllegalConfigurationException('Impossible to specify a keyName parameter for the field '.$fieldname.' if the key is not $FK or $AK');
		}
		if(($KEY == KEY::$FK || $KEY == KEY::$AK) && empty($KEYName)) {
			throw new IllegalConfigurationException('$FK key or $AK key for the field '.$fieldname.' need a keyName');
		}
		
		if(($cast == CAST::$DATE || $cast == CAST::$BUFFER || $cast == CAST::$TIME) && !empty($size)) {
			throw new IllegalConfigurationException('The CAST::DATE or the CAST::BUFFER or the CAST::TIME of field '.$fieldname.' must not have size value');
		}
		

		if($nullable == null) {
			$nullable = false;
		}
			
		$this->name 	= $fieldname;
		$this->type 	= $cast;
		$this->size 	= $size;
		$this->nullable = $nullable;
		$this->KEY 		= $KEY;
		$this->KEYName 	= $KEYName;
	}
	

    /**
    * getter for name
    * 
    * @return string the (unique) name of Field
    */
	public function getName()
	{return $this->name;}

    /**
    * getter for type
    * 
    * @return string the CAST value of Field
    * 
    * @see CAST
    * 
    */	
	public function getType()
	{return $this->type;}
	
   /**
    * getter for size
    * 
    * @return int the size of Field
    */
	public function getSize()
	{return $this->size;}

   /**
    * return true if Field has a $PK
    * 
    * @return boolean true if Field has a $PK
    */	
	public function isPrimaryKEY()
	{return $this->KEY == KEY::$PK;}

   /**
    * return true if Field has a $FK
    * 
    * @return boolean true if Field has a $FK
    */    	
	public function isForeignKEY()
	{return $this->KEY == KEY::$FK;}

   /**
    * return true if Field has a $AK
    * 
    * @return boolean true if Field has a $AK
    */    	
	public function isAssociateKEY()
	{return $this->KEY == KEY::$AK;}
	
    /**
    * getter for keyName
    * 
    * @return string the keyName of Field
    * 
    */
	public function getKEYName()
	{return $this->KEYName;}
	
    /**
    * getter for nullable 
    * 
    * @return true if Field is optional in database
    */
	public function isNullable()
	{return $this->nullable;}
	
    /**
    * getter for defaultValue
    * 
    * @return mixed the default value of Field
    * 
    */	
	public function getDefaultValue()
	{return $this->defaultValue;}
	
   /**
    * setter for defaultValue
    * 
    * @param mixed the default value of Field
    * 
    */	
	public function setDefaultValue($defaultValue){
		if($this->type == CAST::$BUFFER){
			throw new IllegalArgumentException("the Field ".$this->name." of type BUFFER can't have a default value ");
		}
		$this->defaultValue = $defaultValue;
	}
	
}

?>