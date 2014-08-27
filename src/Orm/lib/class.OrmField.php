<?php
/**
 * Contains the class OrmField
 *
 * @since 0.0.1
 * @author Bess
 **/
 
/**
 *   Represent a OrmEntity's field with all its properties
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
class OrmField 
{
	/**
	 * The (unique) name of the field
	 */
	private $name;
	
	/**
	 * The OrmCAST value of the field
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
	 * the OrmKEY value of the field. May be null
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
    * @param OrmCAST The OrmCAST value of the field example : OrmOrmCAST::$INTEGER
    * @param int The max size of the field. May be null if $type is Date, Time, Buffer, None ...
    * @param true if the value may be NULL. Default value is false
    * @param OrmKEY the KEY value of the field. May be null example: OrmKEY::$PK for a primary key
    * @param string the name of the key. Only used for ForeignKey and AssociateKey example : "Customer.customer_id" in the field "customer" of an entity "Order".
    * 
    * @return OrmField the Field Object
    * 
    * 
    * @see OrmCAST
    * @see OrmKEY
	* @see OrmEntity
    * 
    */
	public function __construct($fieldname, $cast, $size = null, $nullable = false, $KEY = null, $KEYName=null) {
		
		$errors = array();

		if(empty($KEY) && !empty($KEYName)) {
			$errors[] = 'Impossible to specify a keyName parameter for the field '.$fieldname.' if the key is not $FK or $AK';
		}
		if($KEY == OrmKEY::$PK && !empty($KEYName)) {
			$errors[] = 'Impossible to specify a keyName parameter for the field '.$fieldname.' if the key is not $FK or $AK';
		}
		if(($KEY == OrmKEY::$FK || $KEY == OrmKEY::$AK) && empty($KEYName)) {
			$errors[] = '$FK key or $AK key for the field '.$fieldname.' need a keyName';
		}

		if(($cast == OrmCAST::$INHERIT) && !($KEY == OrmKEY::$FK || $KEY == OrmKEY::$AK)) {
			$errors[] = '$INHERIT cast is only made for a $FK key or a $AK key (field '.$fieldname.')';
		}
		
		if(($cast == OrmCAST::$DATE || $cast == OrmCAST::$BUFFER || $cast == OrmCAST::$TIME
				|| $cast == OrmCAST::$DATETIME || $cast == OrmCAST::$TS || $cast == OrmCAST::$UUID 
				|| $cast == OrmCAST::$INHERIT || $cast == OrmCAST::$NONE) && !empty($size)) {
			$errors[] = 'The field '.$fieldname.' must not have size value because of its own OrmCAST';
		}
		if(($cast == OrmCAST::$STRING) && empty($size)) {
			$errors[] = 'The field '.$fieldname.' must have size value because of its own OrmCAST';
		}
		
		if(!empty($errors)){
			throw new OrmIllegalConfigurationException($errors);
		}

		if($nullable == null) {
			$nullable = false;
		}

		//TODO : fix futur INHERIT fonction
		/*
		if(OrmCAST::$INHERIT == $cast) {
			//Must take the distant type
			list($entityAssocName, $fieldAssociateName) = explode(".", $KEYName);
			$cast = (new $entityAssocName())->getFieldByName($fieldAssociateName)->getType();
			$size = (new $entityAssocName())->getFieldByName($fieldAssociateName)->getSize();
		}*/
			
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
    * @see OrmCAST
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
	{return $this->KEY == OrmKEY::$PK;}

   /**
    * return true if Field has a $FK
    * 
    * @return boolean true if Field has a $FK
    */    	
	public function isForeignKEY()
	{return $this->KEY == OrmKEY::$FK;}

   /**
    * return true if Field has a $AK
    * 
    * @return boolean true if Field has a $AK
    */    	
	public function isAssociateKEY()
	{return $this->KEY == OrmKEY::$AK;}
	
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
		if($this->type == OrmCAST::$BUFFER){
			throw new OrmIllegalArgumentException("the Field ".$this->name." of type BUFFER can't have a default value ");
		}
		$this->defaultValue = $defaultValue;
	}
	
}

?>
