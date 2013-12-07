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
	 * Boolean : if true the framework will try to use the inner autoincrement of Mysql instead generate a new table xxx_seq like usual
	 */
	private $autoincrement = false;
	
	/**
	 * Array, contains all the uniqueKeys for one or more columns.
	 **/	 
	private $uniqueKeys = array();
	
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
	protected function __construct($moduleName, $name, $prefixe = null, $dbName = null) {
		$this->moduleName = strtolower($moduleName);
		$this->name = strtolower($name);
		
		$this->dbname = $this->name;
		if(!empty($dbName)) {
			$this->dbname = strtolower($dbName);
		}
		
		if(empty($prefixe)) {
			$prefixe = $this->moduleName;
		} else {
			$prefixe = strtolower($prefixe);
		}
		
		$this->dbname = cms_db_prefix().Entity::$_CONST_MOD.'_'.$prefixe.'_'.$this->dbname;
		
		// We add an instance of our-serf into the autoload
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
	* @exception IllegalConfigurationException if we try to use more than a single PrimaryKey in the entity
    */
	protected function add(Field $newField) {
		$this->fields[$newField->getName()] = $newField;

		//Add a sequence on the keys
		if($newField->isPrimaryKEY()) {
			if($this->pk != null)
				throw new IllegalConfigurationException("Orm doesn't support multi-Primary-Key into the Entity ".$this->name);
				
			$this->pk = $newField->getName();
			if(!$this->isAutoincrement()) { // no sequence if autoincrement used
                $this->seqname = $this->dbname.Entity::$_CONST_SEQ;
            }
		}
	}
	
    /**
    * Return the PrimaryKey Field
    * 
    * @return Field the PrimaryKey Field
    * @exception IllegalArgumentException if there is no PrimaryKey Field
    */
	public function getPk() {
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
	public function getFields() {
		return $this->fields;
	}
	
    /**
    * return a Field by name
    * 
    * @param string the name
    * @return Field the Field
    * 
    * @exception IllegalArgumentException if no Field exist for the name
    */
	public function getFieldByName($name) {
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
	public function isFieldByNameExists($name) {
		return isset($this->fields[$name]);
	}
	
    /**
    * Return the name of the table into the database
    * 
    * @return string the name of the table into the database
    * 
    */
	public function getDbname() {
		return $this->dbname;
	}
	
    /**
    * Return the name of the entity
    * 
    * @return string the name of the entity
    * 
    */
	public function getName() {
		return $this->name;
	}
	
	/**
	 *  Return the name of the current module
	 *  
	 *  @return string the name of the current module.
	 **/
	public function getModuleName() {
		return $this->moduleName;
	}
	
    /**
    * Return the name of the sequence (if exists)
    * 
    * @return string the name of the sequence or NULL
    * 
    */
	public function getSeqname() {
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
    * @exception IllegalArgumentException if no Field exists for the name
    */
	public function get($fieldName) {

		$fieldnameSid = $fieldName;
		if(!array_KEY_exists($fieldName,$this->fields) && !array_KEY_exists($fieldnameSid,$this->fields)) {
			throw new IllegalArgumentException("fonction Get : Field $fieldName not found for entity ".$this->getName());
		}
		
		if(!isset($this->values[$fieldName])) {
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
    * @exception IllegalArgumentException if no Field exists for the name
    */
	public function set($fieldName,$value) {
		$fieldnameSid = explode("_sid", $fieldName);
		$fieldnameSid = $fieldnameSid[0];
		if(!array_KEY_exists($fieldName,$this->fields) && !array_KEY_exists($fieldnameSid,$this->fields)) {
			throw new IllegalArgumentException("function Set : Field $fieldName not found into the entity".$this->getName());
		}
		
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
	public function getValues() {
		return $this->values;
	}
		
	/**
	 * Shortcut to save the entity. if the primaryKey is setted, it will be an update operation, else an insert.
	 *
	 * @return the entity saved with its new new Id (customer_id in my example) if it's an insertion
	 **/
	public function save(){
		if($this->pk == null) {
			throw new IllegalArgumentException("you can't save the entity ".$this->getName()." because it doesn't have any Primary-Key");
		}
	
		if($this->get($this->pk) == null) {
			return Core::insertEntity($this);
		} else {
			return Core::updateEntity($this);
		}
	}
	
	/**
	 * Shortcut to delete the entity
	 **/
	public function delete(){
		if($this->pk == null) {
			throw new IllegalArgumentException("you can't delete the entity ".$this->getName()." because it doesn't have any Primary-Key");
		}
		
		if($this->get($this->pk) == null) {
			throw new IllegalArgumentException("you can't delete the entity ".$this->getName()." because its Primary-Key doesn't have any value");
		}
		
		Core::deleteByIds($this, array($this->get($this->pk)));
	}
	
    /**
    * Can be overridden Let you modify some data just before saving the data into the datatable.
    * 
    * @param array all the values to process
    * @param array more parameters if you need, if you want
    * 
    * @return mixed to define
    */
	public function processValueForSave($rows, $args = null){
	
		return $rows;
	}
	
	/**
	 * Call the compareTo function into your Entity to sort the Entities.
	 * To activate this functionality, The Entity must implement ISortable Interface.
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
	public static function sort(array $array) {

		usort($array, array(get_called_class(), "compareTo"));
	
		return $array;
	}
	
	/**
	 * May be override in your Entity's definition to allow Search module indexing your entities's data
	 * 
	 * Example of function isIndexable() in a Customer Entity
	 *  
	 *  <code>
	 *       
	 *	public static function isIndexable()
	 *	{
	 *		return this->get['isActivated'];
	 *	}
	 * 
	 *  </code>
	 **/
	public static function isIndexable(){
		return false;
	}
	
		
	/**
    * getter for autoincrement
    * 
    * @return true of the Field is autoincrement
    */
	public function IsAutoincrement() {
		return $this->autoincrement;
	}
	
	/**
	 * This function will let you define some optional configuration for your Entity
	 *    => the field must be auto-incremental
	 **/
	public function garnishAutoincrement(){
		
		if($this->getPk() == null || $this->getPk()->getType() != CAST::$INTEGER){
			throw new IllegalArgumentException("entity ".$this->getName()." don't have any INTEGER PK and so can't be defined autoincrement");
		}
		$this->autoincrement = true;
		
		//Remove any seq that could be add before
        $this->seqname = null;
	}
	
	/**
	 * Return 
	 * @return array : the list of couple of unique index
	 **/
	public function getUniqueKeys() {
		return $this->uniqueKeys;
	}
	
	/**
	 * This function will let you define some optional configuration for your Entity
	 *    => the field have one or many Unique Key on one or many columns.
	 *
	 *  ex : myEntity->garnishUniqueKeys(array('field1', array('field2', 'field3'))) will create 2 unique index
	 *
	 * @param array a list of one or many name of field 
	 *
	 **/
	public function garnishUniqueKeys($uniqueKeys){
		
		if(!is_array($uniqueKeys)){
			throw new IllegalArgumentException("garnishUniqueKeys() only accept an array as parameter");
		}
		
		//Test the existence of each member
		foreach($uniqueKeys as $member){
			if(is_array($member)){
				foreach($member as $elt){
					if(!$this->isFieldByNameExists($elt)){
						throw new IllegalArgumentException("garnishUniqueKeys() only accept valid Field but ".$elt." is not a existing Field in the Entity ".$this->getName());
					}
				}
			} else {
				if(!$this->isFieldByNameExists($member)){
					throw new IllegalArgumentException("garnishUniqueKeys() only accept valid Field but ".$member." is not a existing Field in the Entity ".$this->getName());
				}
			}
		}
	
		$this->uniqueKeys = $uniqueKeys;
	}
	
	/**
	 * This function will let you define some optional configuration for your Entity
	 *    => the default value of the field is ...
	 *
	 *  ex : myEntity->garnishDefaultValue('field1', 'some text')
	 *
	 * @param String the name of the Field
	 * @param Mixed the default value.
	 *
	 **/
	public function garnishDefaultValue($fieldName,$defaultValue){
		if(!$this->isFieldByNameExists($fieldName)){
			throw new IllegalArgumentException("garnishDefault() only accept valid Field but ".$fieldName." is not a existing Field in the Entity ".$this->getName());
		}
		
		//forbid a default value on a nullable field Because it's make no sence
		if($this->getFieldByName($fieldName)->isNullable()){
			throw new IllegalArgumentException("the Field ".$fieldName." in the Entity ".$this->getName()." can't accept a default value because it's setted Nullable");
		}
		
		$this->getFieldByName($fieldName)->setDefaultValue($defaultValue);
		
	}
	
}

?>
