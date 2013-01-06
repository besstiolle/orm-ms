<?php
/**
 * Contains the class Entity
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
/**
 * Abstract Classes describing the frame of an Entity into Orm
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
abstract class Entity 
{
	/**
	 * String : Name of the module that currently use this entity
	 * */
	private $moduleName;
	
	/**
	 * String : the official name of the entity
	 * */
	protected $name;

	/**
	 * String : the name of the table in database
	 * */
	private $dbname;
	
	/**
	 * String : name of the sequence linked to the table in the database. May be Null
	 * */
	private $seqname;
	
	/**
	 * list : fields making up the entity
	 * */
	private $fields = array();
	
	/**
	 * list : values of each fields
	 * */
	private $values = array();
	
	/**
	 * String : Name of the field wich will be used as a primaryKey
	 * */
	private $pk;
	
	/**
	 * String : constant, suffix for the sequence name into the database
	 * */
	public static $_CONST_SEQ = '_seq';
	/**
	 * String : constant, suffix used to nammed the table into database for a module
	 * */
	public static $_CONST_MOD = 'module';
	
	
	
    /**
     * constructor protected to avoid a direct instanciation like "new Entity()"
     * Each time a entity is constructed, we place a copy into the autoloader.
     * 
     * @param string The name of the module who calling this method (so not "Orm")
     * @param string The name of the entity
     * @param string [optional] Prefix to use into database for table. If not setted, it will use the name of your module
     * @param string [optional] The name of table for this entity. If not setted, it will use the name of your entity
     *
     * @return Entity the entity as a new instance
     * 
     * @see MyAutoload
     */
	protected function __construct($moduleName, $name, $prefixe = null, $dbName = null)
	{
		$this->moduleName = strtolower($moduleName);
		$this->name = strtolower($name);
		
		$this->dbname = $this->name;
		if(!empty($dbName))
		{
			$this->dbname = strtolower($dbName);
		}
		
		if(empty($prefixe))
		{
			$prefixe = $this->moduleName;
		} else {
			$prefixe = strtolower($prefixe);
		}
		
		$this->dbname = cms_db_prefix().Entity::$_CONST_MOD.'_'.$prefixe.'_'.$this->dbname;
		
		//On ajoute une instance de soit dans l'autoload
		myAutoload::addInstance($this->moduleName,$this);
	}
	
    /**
    *  Can be override. Let you specify how your table must be populated 
    * after you asked to create the table of your Entity
    */
	public function initTable(){}
    	
    /**
    * Add a new Field into the list of Fields
    * 
    * @param Field the object Field to add
	* 
	* @throw IllegalConfigurationException if we try to use more than a single PrimaryKey in the entity
    */
	protected function add(Field $newField)
	{
		$this->fields[$newField->getName()] = $newField;

		//Ajout d'une sequence sur les cle
		if($newField->isPrimaryKEY())
		{
			if($this->pk != null)
				throw new IllegalConfigurationException("Orm doesn't support multi-Primary-Key into the Entity ".$this->name);
				
			$this->pk = $newField->getName();
			$this->seqname = $this->dbname.Entity::$_CONST_SEQ;
		}
	}
	
    /**
    * Return the PrimaryKey Field
    * 
    * @return Field the PrimaryKey Field
    * @IllegalArgumentException if there is no PrimaryKey Field
    */
	public function getPk()
	{
		if($this->pk == null)
			throw new IllegalArgumentException("the entity ".$this->getName()." doesn't have any Primary-Key");
		
		return $this->fields[$this->pk];
	}
	
    /**
    * Return the list of Fields
    * 
    * @return array<Field> an array with all the Fields
    * 
    */
	public function getFields()
	{
		return $this->fields;
	}
	
    /**
    * retourn a Field by name
    * 
    * @param string the name
    * @return Field the Field
    * 
    * @IllegalArgumentException if no Field exist for the name
    */
	public function getFieldByName($name)
	{
		if(isset($this->fields[$name]))
			return $this->fields[$name];
		
		throw new IllegalArgumentException("The field $name doesn't exist into the entity ".$this->getName());
	}
	
    /**
    * Return true if a Field exists for the name
    * 
    * @param string the name
    * @return Boolean if exists
    */
	public function isFieldByNameExists($name)
	{
		return isset($this->fields[$name]);
	}
	
    /**
    * Return the name of the table into the database
    * 
    * @return string the name of the table into the database
    * 
    */
	public function getDbname()
	{
		return $this->dbname;
	}
	
    /**
    * Return the name of the entity
    * 
    * @return string the name of the entity
    * 
    */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 *  Return the name of the current module
	 *  
	 *  @Return string the name of the current module.
	 **/
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
    /**
    * Return the name of the sequence (if exists)
    * 
    * @return string the name of the sequence or NULL
    * 
    */
	public function getSeqname()
	{
		if(empty($this->seqname))
			return null;
		
		return $this->seqname;
	}
	
    /**
    * Return the value for a Field by the name 
    * 	
    * @param string the name of the Field
    * @return mixed the value for the field
    * 
    * @IllegalArgumentException if no Field exists for the name
    */
	public function get($fieldName)
	{
		$fieldnameSid = explode("_sid", $fieldName);
		$fieldnameSid = $fieldnameSid[0];
		if(!array_KEY_exists($fieldName,$this->fields) && !array_KEY_exists($fieldnameSid,$this->fields))
		{
			throw new IllegalArgumentException("fonction Get : Field $fieldName not found for entity ".$this->getName());
		}
		
		if(!isset($this->values[$fieldName]))
		{
			return null;
		}
		
		return $this->values[$fieldName];
	}
	
   /**
    * Set a value to a Field
    *     
    * @param string The name of the Field
    * @param mixed the new value of the Field
    * 
    * @IllegalArgumentException if no Field exists for the name
    */
	public function set($fieldName,$value)
	{
		$fieldnameSid = explode("_sid", $fieldName);
		$fieldnameSid = $fieldnameSid[0];
		if(!array_KEY_exists($fieldName,$this->fields) && !array_KEY_exists($fieldnameSid,$this->fields))
		{throw new IllegalArgumentException('fonction Set : cle '.$fieldName.' non trouvee dans l\'entite');}
		
		$this->values[$fieldName] = $value;
	}
	
    /**
    * Return the values for all the Fields into an associative array with 
    *  * key = name of the Field, and 
    *  * value = its value
    * 
    * @return array an associative array
    * 
    */
	public function getValues()
	{
		return $this->values;
	}
	
	
	public function initForeignKEY($fieldName, $sid = null)
	{			
		$field = $this->getFieldByName($fieldName);
		
		if($field->getKEYName() == '')
			throw new IllegalArgumentException("Le champs $fieldName ne possede aucune cle etrangere associee");
			
		$cle = explode('.',$field->getKEYName(),2);
		//Evaluation de la eclass en cours
		eval('$entity = new '.$cle[0].'();');
		
		if($sid == null)
		{
			$liste = Core::selectAll($entity);
		} else
		{
			$liste = Core::selectByIds($entity, array($sid));
		}
		
		return array($entity,$liste);
	
	}
	
    /**
    * Can be overriden Let you modify some data just before saving the data into the datatable.
    * 
    * @param array all the values to procee
    * @param array more parameters if you need, if you want
    * 
    * @return mixed to define
    */
	public function processValueForSave($rows, $args = null){
	
		return $rows;
	}
	
	/**
	 * Call the compareTo function into your Entity to sort the Entities.
	 * To activate this fonctionnality, The Entity must implement ISortable Interface.
	 *
	 * Example of function compareTo() in a Customer Entity
	 *  <code>
	 *       
	 *	public static function compareTo(Entity $entity1, Entity $entity2)
	 *	{
	 *		$compare = strcmp($entity1->get('name'), $entity2->get('name'));
	 *      	return $compare;
	 *	}
	 * 
	 *  </code>
	 *
	 * @param array<Entity> the list of Entity to sort
	 *
	 * @return array<Entity> the list of entity gracefully sorted
	 */
	public static function sort(array $array)
	{
		//http://php.net/manual/fr/function.get-called-class.php
		//PHP 5.3.0 only
		usort($array, array(get_called_class(), "compareTo"));
	
		return $array;
	}
	
}

?>
