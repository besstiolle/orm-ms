<?php
/**
 * Contains the class OrmCore
 * 
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
 
/**
 * Main part of the interface between Cmsmadesimple natives function and the necessity for the Orm functions
 *   
 * @since 0.0.1
 * @author Bess
 * @package Orm
*/
class OrmCore {  
    /**
    * Protected constructor
    *     
    */
	protected function __construct() {}
      
    /**
    * transforms the entity's structure into adodb informations 
    *         
    * @param OrmEntity the entity
    * @return the adodb informations
    */
	private static final function getFieldsToHql(OrmEntity &$entity) {    
		$hql = '';

		$listeField = $entity->getFields();

		//For each Field contained in the entity
		foreach($listeField as $field) {
			//We don't create the Field which are links externals on associative tables
			if($field->isAssociateKEY()) {
				continue;
			}	
			
			//We don't create the transient Fields 
			if($field->getType() == OrmCAST::$NONE) {
				continue;
			}

			if(!empty($hql)) {
				$hql .= ' , ';
			}

			$hql .= ' '.$field->getName().' ';
			$size = $field->getSize() != "" ? " (".$field->getSize().") " : "";
			switch($field->getType()) {
				case OrmCAST::$STRING : $hql .= 'C'.$size; break;

				case OrmCAST::$INTEGER : $hql .= 'I'.$size; break;

				case OrmCAST::$NUMERIC : $hql .= 'N'.$size; break;

				case OrmCAST::$DOUBLE : $hql .= 'F'.$size; break;

				case OrmCAST::$BUFFER : $hql .= 'X'; break;

				case OrmCAST::$DATE : $hql .= 'D'; break;

				case OrmCAST::$TIME : $hql .= 'T'; break;   

				case OrmCAST::$UUID : $hql .= 'C (36) '; break;   

				case OrmCAST::$TS : $hql .= 'I (10) '; break; //workaround for the real timestamp missing in ADODBLITE

				case OrmCAST::$DATETIME : $hql .= CMS_ADODB_DT; break;   
			}
			
			//Manage the default value
			if($field->getDefaultValue() != null){
				if($field->getType() == OrmCAST::$STRING || $field->getType() == OrmCAST::$BUFFER) {
					$hql .= "  DEFAULT '".str_replace("'", "''",$field->getDefaultValue())."' ";
				} else {
					$hql .= "  DEFAULT ".$field->getDefaultValue()." ";
				}
				
			}

			if($field->isPrimaryKEY()) {
				if($entity->isAutoincrement()) {
					$hql .= ' KEY AUTO';
				} else {
					$hql .= ' KEY ';
				}
			}
				
		}
	
		OrmTrace::info($hql);

		return $hql;
	}
	
    /**
    * Create a table into Database from the structure of an OrmEntity
    *  Will also create the sequence if it's needed
    *  
    *   example with a Customer entity : 
    * <code>
    * 
    * class Customer extends OrmEntity
    * {
    *    public function __construct()
    *    {
    *        parent::__construct($this->GetName(), 'customer');
    *        
    *        $this->add(new OrmField('customer_id'  
	*			, OrmCAST::$INTEGER
	*			, null
	*			, null
	*			, OrmKEY::$PK 
	*			));
    * 
    *        $this->add(new OrmField('name'
    *        	, OrmCAST::$STRING 
    *        	, 32
    *        	));
    * 
    *        $this->add(new OrmField('lastname'
    *        	, OrmCAST::$STRING
    *        	, 32
	*			, true  // Is nullable
    *        	));
    *    }
    * }
    * </code>
    * 
    *  The best way to create its table into the database : 
    * 
    * <code>
    *   $customer = MyAutoload::getInstance($this->GetName(), 'customer');
    *   OrmCore::createTable($customer);
    * </code>
    * 
    *  The function will also try to populate the table with a call to the function initTable() if it's define into the entity class.
    * 
	* @param Orm the module which extends the Orm module
    * @param OrmEntity an instance of the entity
    */
	public static final function createTable(OrmEntity &$entityParam) {
	
		$hql = OrmCore::getFieldsToHql($entityParam);
		$result = OrmDB::createTable($entityParam->getDbname(), $hql);
				
		//If necessary, it will create a sequence on the table.
		if($entityParam->getSeqname() != null){
			OrmDB::createSequence($entityParam->getSeqname());
		}
		
		//We manage the ("unique") indexes
		$indexes = $entityParam->getIndexes();

		//For each Field contained in the entity
		foreach($indexes as $index) {
			$result = OrmDB::createIndex($entityParam->getDbname(), $index['fields'], $index['unique']);
		}

		//We initiate the table.
		$entityParam->initTable();
	}
    
    /**
    * Drop the table for the OrmEntity in parameters
    *  Will also drop the sequence if it's needed
    * 
    * @param OrmEntity an instance of the entity
    */
	public static final function dropTable(OrmEntity &$entityParam) {

		OrmDB::dropTable($entityParam->getDbname());

		//If necessary, it will delete a sequence on the table.
		if($entityParam->getSeqname() != null){
			OrmDB::dropSequence($entityParam->getSeqname());
		}
	}  
  
    /**
    * Will edit the table of the OrmEntity in parameters with the SQL query in parameters
    * 
    *   example : if you need to do 
    * 
    * <code>        
    *   ALTER TABLE ` table of the Customer entity ` ADD `newColumn` INT NOT NULL 
    *   ALTER TABLE ` table of the Customer entity ` DROP `oldColumn` 
    * </code>
    * 
    *  the code must be :
    * 
    * <code>
    *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
    *       OrmCore::alterTable($customer, "ADD `newColumn` INT NOT NULL");
    *       OrmCore::alterTable($customer, "DROP `oldColumn`");
    * </code>
    *   
    * 
    * @param OrmEntity an instance of the entity
    * @param string the SQL query
    */
	public static final function alterTable(OrmEntity &$entityParam, $sql) {	
		$queryAlter = "ALTER TABLE ".$entityParam->getDbname()." ".$sql;    
		
		//Execution
		$result = OrmDb::execute($queryAlter,
									null,
									"Database error during OrmCore::alterTable(OrmEntity &{$entityParam->getName()}, {$sql})");
	}
    
    /**
     * Insert data into database.
     * 
     * Example for a new Customer : customer_id, name, lastName (optional) 
     *   
     * <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     *       
     *       $customer->set('name','Durant');
     *       $customer->set('lastName'=>'John');
     * 
     *       OrmCore::insertEntity($customer);
     * </code>
	 *
	 * You could also code for the last line : $customer->save();  it will automatically 
     * 
     * Important : you must not set the primaryKey value. It will be calculate by the system it-self
     *                                      
     * @param OrmEntity an instance of the entity
	 *
     * @return the entity saved with its new new Id (customer_id in my example)
     */
	public static final function insertEntity(OrmEntity &$entityParam) {

		$listeField = $entityParam->getFields();
		$values = $entityParam->getValues();
		$indexes = $entityParam->getIndexes();

		$queryInsert = 'INSERT INTO '.$entityParam->getDbname().' (%s) values (%s)';

		$str1 = "";
		$str2 = "";

		$params = array();
			   
		//All the required values must be present
		foreach($listeField as $field)
		{
			if($field->isAssociateKEY()) {
				continue;
			}

			if(!empty($str1)) {
				$str1 .= ',';
				$str2 .= ',';
			}
			$str1 .= ' '.$field->getName().' ';
			$str2 .= '?';
			
			if($field->isPrimaryKEY()) {
				if(!empty($values[$field->getName()])) {
					if($entityParam->isAutoincrement()){
						throw new OrmIllegalArgumentException('Primary Key '.$field->getName().' can\'t be setted during insert operation for OrmEntity'.$entityParam->getName());
					}
				} else if(!$entityParam->isAutoincrement()){
					$newId = OrmDB::genID($entityParam->getSeqname());
					$values[$field->getName()] = $newId;
					$entityParam->set($field->getName(), $newId);
				} else{
					$values[$field->getName()] = 0;
					$entityParam->set($field->getName(), 0);
				}
			}
			
			//Empty Field that shouldn't be !
			if(!$field->isNullable() && !isset($values[$field->getName()])) {
				//Exception : if the field have a default value we set it manually
				if(!is_null($field->getDefaultValue())){
					$values[$field->getName()] = $field->getDefaultValue();
				} else {
					throw new OrmIllegalArgumentException('the field '.$field->getName().' of OrmEntity  '.$entityParam->getName().' can\'t be null');
				}
			}
			
			// Control UUID type
			if($field->getType() == OrmCAST::$UUID && !empty($values[$field->getName()])){
				$pattern = "/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i";
				if(!preg_match($pattern, $values[$field->getName()])){
					throw new OrmIllegalArgumentException('the field '.$field->getName().' of OrmEntity  '.$entityParam->getName().' doesn\'t match the UUID pattern : '.$pattern);
				}
			}
			
			
			$val = null;
			if(isset($values[$field->getName()]))
			{
				$params[] = OrmCore::FieldToDBValue($values[$field->getName()], $field->getType());
			} else {
				$params[] = null;
			}
		}
		
		//control uniqueness on unique Field
		foreach($indexes as $index){
			if(!$index['unique']){
				continue;
			}
			
			$query = 'SELECT COUNT(*) FROM '.$entityParam->getDbname().' WHERE 1 ';
			$arrayFind = array();
			$msgError = '{';
			$isFirst = true;
			foreach($index['fields'] as $elt){
				if(!$isFirst){
					$msgError .=',';
				}
				if(empty($values[$elt])){
					$query .= ' AND ' . $elt . ' IS NULL ';
				} else {
					$query .= ' AND ' . $elt . ' = ? ';
					$arrayFind[] = $values[$elt];
				}
				$msgError .= " {$elt} = {$values[$elt]} ";
			}
			$query .= ' AND ' . $entityParam->getPk()->getName() . ' != ? ';
			$arrayFind[] = $values[$entityParam->getPk()->getName()];			
			$msgError .= '}';
			
			//Execution
			$result = OrmDb::getOne($query,
									$arrayFind,
									"Database error during unicity control in OrmCore::insertEntity(OrmEntity &{$entityParam->getName()})");
		
			if($result != 0){
				throw new OrmIllegalArgumentException("an Entity {$entityParam->getName()} with the same fields ({$msgError}) already exists in database");
			}
		}
		
		//Execution
		$result = OrmDb::execute(sprintf($queryInsert, $str1, $str2),
									$params,
									"Database error during OrmCore::insertEntity(OrmEntity &{$entityParam->getName()})");
		

	/*	if($entityParam->isIndexable()) {  
			OrmIndexing::AddWords($entityParam->getModuleName(), OrmCore::findById($entityParam,$arrayKEY[0]));
		}*/
		
		//empty cache
		OrmCache::getInstance()->clearCache();
		
		return $entityParam;

	}
	
    /**
     *  Update data into database. The third parameter must follow this scheme
     * 
     * Example for a new Customer : 
     * 
     * <code>
     *       $myArray = array();
     *       $myArray[] = array('customer_id'=>1, 'lastName'=>null, 'name'=>'Dupont');    <-- update Name value, erase lastName value in database
     *       $myArray[] = array('customer_id'=>2, 'name'=>'Durant');                      <-- update Name value
     *       $myArray[] = array('customer_id'=>3, 'lastName'=>'John', 'name'=>'Doe');     <-- update Name value and lastName value
     * 
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     *       
     *       $customer = OrmCore::findById($customer, 1);
     *       $customer->set('lastName'=>'NewLastName');
     * 
     *       OrmCore::updateEntity($customer);
     * </code>
     * 	 
     * You could also code for the last line : $customer->save();  it will automatically 
	 *
     * @param OrmEntity an instance of the entity
	 *
	 * @return the entity saved with its new Id (customer_id in my example)
     */	
	public static final function updateEntity(OrmEntity &$entityParam) {

		$listeField = $entityParam->getFields();
		$values = $entityParam->getValues();
		$indexes = $entityParam->getIndexes();

		$str = "";
		$where = '';
		$params = array();
		$hasKey = false;
		  
		//All the required values must be present
		foreach($listeField as $field) {
		
			//if the field is empty and we have a default value we set it manually
			if(empty($values[$field->getName()]) && $field->getDefaultValue() != null){
				$values[$field->getName()] = $field->getDefaultValue();
			} 
		
			//If it's not set
			if(empty($values[$field->getName()])) {
			
				//If it's a primaryKey we throw a exception
				if($field->isPrimaryKEY()) {
					throw new OrmIllegalArgumentException('the primaryKey '.$field->getName().' is missing for the entity : '.$entityParam->getName());
				}
				
				//an empty associative field : no problem, we can pass
				if($field->isAssociateKEY()) {
					continue;
				}
				
				//If it's a no nullable field we throw a exception
				if(!$field->isNullable()) {
					throw new OrmIllegalArgumentException('the field '.$field->getName().' of Entity  '.$entityParam->getName().' can\'t be null');
				}
			} else {
				// Control UUID type
				if($field->getType() == OrmCAST::$UUID){
					$pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
					
					if(!preg_match($pattern, $values[$field->getName()])){
						throw new OrmIllegalArgumentException('the field '.$field->getName().' of Entity  '.$entityParam->getName().' doesn\'t match the UUID pattern : '.$pattern);
					}
				}
			}
			
			
			
			//If it's a primaryKey
			if($field->isPrimaryKEY()) {
				$where = ' WHERE '.$field->getName().' = ?';
				$hasKey = true;
				$keyValue = $values[$field->getName()];
			}

			if(!empty($str)) {
			  $str .= ',';
			}

			$str .= ' '.$field->getName().' = ? ';

			$params[] = OrmCore::FieldToDBValue($values[$field->getName()], $field->getType());

		}
		
		//control uniqueness on unique Field
		foreach($indexes as $index){
			if(!$index['unique']){
				continue;
			}
			
			$query = 'SELECT COUNT(*) FROM '.$entityParam->getDbname().' WHERE 1 ';
			$arrayFind = array();
			$msgError = '{';
			$isFirst = true;
			foreach($index['fields'] as $elt){
				if(!$isFirst){
					$msgError .=',';
				}
				if(empty($values[$elt])){
					$query .= ' AND ' . $elt . ' IS NULL ';
				} else {
					$query .= ' AND ' . $elt . ' = ? ';
					$arrayFind[] = $values[$elt];
				}
				$msgError .= " {$elt} = {$values[$elt]} ";
			}
			$query .= ' AND ' . $entityParam->getPk()->getName() . ' != ? ';
			$arrayFind[] = $values[$entityParam->getPk()->getName()];			
			$msgError .= '}';
			
			//Execution
			$result = OrmDb::getOne($query,
									$arrayFind,
									"Database error during unicity control in OrmCore::updateEntity(OrmEntity &{$entityParam->getName()})");
		
			if($result != 0){
				throw new OrmIllegalArgumentException("an Entity {$entityParam->getName()} with the same fields ({$msgError}) already exists in database");
			}
		}

		if($hasKey) {
			$params[] = $keyValue;
		}

		$queryUpdate = 'UPDATE '.$entityParam->getDbname().' SET '.$str.$where;
	
		//Execution
		$result = OrmDb::execute($queryUpdate,
									$params,
									"Database error during OrmCore::updateEntity(OrmEntity &{$entityParam->getName()})");
		
		/*if($entityParam->isIndexable()) {  
			OrmIndexing::UpdateWords($entityParam->getModuleName(), $entityParam);
		}*/
		
		//empty cache
		OrmCache::getInstance()->clearCache();
		
		return $entityParam;
	}
  
    /**
    * Delete information into database
    *   
    * Example for a single deletion : 
    * 
    * <code>
    *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
    * 
    *       OrmCore::deleteByIds($customer, array(1);
    * </code>
    * 
    *  Example for multiple deletion : 
    * 
    * <code>
    *       $myArray = array();
    *       $myArray[] = 1;
    *       $myArray[] = 2;
    *       $myArray[] = 3;
    * 
    *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
    * 
    *       OrmCore::deleteByIds($customer, $myArray);
    * </code>
    *                                    
    * @param OrmEntity an instance of the entity    
    * @param array all the ids to delete ($customer_id in my example)
    */
	public static final function deleteByIds(OrmEntity &$entityParam, $ids) {

		$listeField = $entityParam->getFields();

		foreach($listeField as $field)
		{
		  if(!$field->isPrimaryKEY())
		  { 
			continue;
		  }
		  $type = $field->getType();
		  $name = $field->getName();
		}  

		$where = '';
		foreach($ids as $sid)
		{
		  if(!empty($where))
		  {
			$where .= ' OR ';
		  }
		  
		  $where .= $name.' = ?';
		  $params[] = OrmCore::FieldToDBValue($sid, $type);  
		}


		$queryDelete = 'DELETE FROM '.$entityParam->getDbname().' WHERE '.$where;

		//Execution
		$result = OrmDb::execute($queryDelete,
									$params,
									"Database error during OrmCore::deleteEntity(OrmEntity &{$entityParam->getName()})");
									
		

		if($entityParam->isIndexable())
		{  
		  // $modops = cmsms()->GetModuleOperations();
		  // if(method_exists($modops,"GetSearchModule"))
		  // {
			// OrmIndexing::setSearch($modops->GetSearchModule());
		  // } else
		  // {
			// die("ko");
		  // }
		  foreach($ids as $sid)
		  {
			OrmIndexing::DeleteWords($entityParam->getModuleName(), $entityParam, $sid);
		  }
		  
		}
		
		//empty cache
		OrmCache::getInstance()->clearCache();
	}
   
    /**
    * Returns the number of occurrences from the table of the entity in Parameters
    *                                     
    * @param OrmEntity an instance of the entity    
	* 
    * @return int the number of occurrences from the table   
    */
	public static final function countAll(OrmEntity &$entityParam) {

		$querySelect = 'SELECT count(*) FROM '.$entityParam->getDbname();
		
		//Execution
		$result = OrmDb::getOne($querySelect,
									null,
									"Database error during OrmCore::countAll(OrmEntity &{$entityParam->getName()})");
		
		return $result;
	}
  
    /**
    * Returns all the occurrences from the table of the entity in Parameters
    * 
    * @param OrmEntity an instance of the entity  
	*
    * @return array<OrmEntity> list of Entities found
    */
	public static final function findAll(OrmEntity &$entityParam, OrmOrderBy &$orderBy=null, OrmLimit &$limit = null) {

		$querySelect = 'SELECT * FROM '.$entityParam->getDbname();

		// Order By 
		if($orderBy != null) {
			$querySelect .= $orderBy->getOrderBy();
		}
		else if($entityParam->getDefaultOrderBy() != null) {
			$querySelect .= $entityParam->getDefaultOrderBy()->getOrderBy();
		}
				
		if($limit != null) {
			$querySelect .= $limit->getLimit();
		}
		
		//If it's already in the cache, we return the result
		if(OrmCache::getInstance()->isCache($querySelect)) {
			$entities = OrmCache::getInstance()->getCache($querySelect);
		} else {
			//Execution
			$result = OrmDb::execute($querySelect,
									null,
									"Database error during OrmCore::findAll(OrmEntity &{$entityParam->getName()})");

			$entities = OrmCore::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			OrmCache::getInstance()->setCache($querySelect, null, $entities);
		}
		
		return array_values($entities);
	}
	
	/**
	 * Inner function to factorize some code for each "find*" functions
	 *  It will retrieve all the informations on the AK's Field
	 *
     * @param OrmEntity an instance of the entity  
	 * @param resultQuery the returned value of sql execution
	 *
     * @return array<OrmEntity> list of Entities populate with all the informations on AK's Field
	 **/
	private static final function _processArrayEntity(OrmEntity &$entityParam, $resultQuery, OrmOrderBy &$orderBy=null) {

		$entitys = array();
		while ($row = $resultQuery->FetchRow()) {
		  $anEntity = OrmCore::rowToEntity($entityParam, $row);
		  $entitys[$anEntity->get($anEntity->getPk()->getName())] = $anEntity;
		}
				
		//Test the presence of $AK
		$listeField = $entityParam->getFields();
		foreach($listeField as $field) {
		  if($field->isAssociateKEY()) {
			
			//Initiate the field associate with an empty array.
			foreach(array_keys($entitys) as $key){
				$entitys[$key]->set($field->getName(), array());
			}
		  
			list($entityAssocName, $fieldAssociateName) = explode(".", $field->getKEYName());
			$entityAssoc = new $entityAssocName();
			
			$queryAdd = 'SELECT * FROM '.$entityAssoc->getDbname().' WHERE '.$fieldAssociateName.' IN ('.implode(',',array_keys($entitys)).')';
		
			// Order By 
			if($orderBy != null) {
				$queryAdd .= $orderBy->getOrderBy();
			}
			else if($entityParam->getDefaultOrderBy() != null) {
				$queryAdd .= $entityParam->getDefaultOrderBy()->getOrderBy();
			}
			
			//Execution
			$result = OrmDb::execute($queryAdd,
									null,
									"Database error during request to get associative entity $entityAssocName");
			
			while ($rowAssociate = $result->FetchRow()) {
				$arrayIdEntitiesDest = $entitys[$rowAssociate[$fieldAssociateName]]->get($field->getName());
				$arrayIdEntitiesDest[] = $rowAssociate[$entityAssoc->getPk()->getName()];
				$entitys[$rowAssociate[$fieldAssociateName]]->set($field->getName(),$arrayIdEntitiesDest);
			}
		  } 
		}
		
		return $entitys;
	}
  
	/**
	* Return a OrmEntity from its Id
	* 
	* @param OrmEntity an instance of the entity  
	* @param int the Id to find
	* @return OrmEntity the OrmEntity found or NULL
	*/
	public static final function findById(OrmEntity &$entityParam, $id) {
		$liste = OrmCore::findByIds($entityParam, array($id));
		
		if(!isset($liste[0])){
			return null;
		}
		
		return $liste[0];
	}
  
	/**
	* Return Entities from their Ids
	* 
	* @param OrmEntity an instance of the entity  
	* @param array list of the Ids to find
	*
	* @return array<OrmEntity> list of Entities found
	*/
	public static final function findByIds(OrmEntity &$entityParam, $ids, OrmOrderBy &$orderBy=null) {
		if(count($ids) == 0)
		  return array();
			
		$listeField = $entityParam->getFields();
		$where = "";
			
		foreach($listeField as $field) {
		  if(!$field->isPrimaryKEY()) { 
			continue;
		  }
		  
		  foreach($ids as $id) {
			if(!empty($where)) {
			  $where .= ' OR ';
			}
				  
			$where .= $field->getName().' = ?';
			$params[] = OrmCore::FieldToDBValue($id, $field->getType());
		  }
		}

		$querySelect = 'SELECT * FROM '.$entityParam->getDbname().' WHERE '.$where;
		
		// Order By 
		if($orderBy != null) {
			$querySelect .= $orderBy->getOrderBy();
		}
		else if($entityParam->getDefaultOrderBy() != null) {
			$querySelect .= $entityParam->getDefaultOrderBy()->getOrderBy();
		}
		
		//If it's already in the cache, we return the result
		if(OrmCache::getInstance()->isCache($querySelect, $params)) {
		  $entities = OrmCache::getInstance()->getCache($querySelect,$params);
		} else {
			
			//Execution
			$result = OrmDb::execute($querySelect,
									$params,
									"Database error during OrmCore::findByIds(OrmEntity &{$entityParam->getName()}, ".print_r($ids, true).")");
		
			$entities = OrmCore::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			OrmCache::getInstance()->setCache($querySelect, null, $entities);
		}

		return array_values($entities);
	}
  
    /**
     * Allow search a list of OrmEntity from a list of OrmCriteria
     * 
     * Example : find the customers with lastName 'Roger' (no casse sensitive)
     * 
     *  <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new OrmExample();
     *       $example->addCriteria('lastName', OrmTypeCriteria::$EQ, array('roger'), true);
     * 
     *       OrmCore::findByExample($customer, $example);
     * </code>
     * 
     *  Example : find the customers with Id >= 90
     * 
     * <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new OrmExample();
     *       $example->addCriteria('customer_id', OrmTypeCriteria::$GTE, array(90));
     * 
     *       OrmCore::findByExample($customer, $example);
     * </code>
     * 
     * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals
     * 
     * NOTE 2 : you can add as many Criterias as you want in an Example Object
     * 
     * @param OrmEntity an instance of the entity
     * @param OrmExample the Object OrmExample with some Criterias inside
     * 
     * @see OrmExample
     * @see OrmTypeCriteria
     */
	public static final function findByExample(OrmEntity &$entityParam, OrmExample $example, OrmOrderBy &$orderBy = null, OrmLimit &$limit = null) {
		$listeField = $entityParam->getFields();

		$criterias = $example->getCriterias();
		$select = "SELECT * FROM ".$entityParam->getDbname();
		$hql = "";
		$params = array();
		
		foreach($criterias as $criteria) {
		  if(!empty($hql)) {
			$hql .= ' AND ';
		  }
		  
		  if(empty($hql)) {
			$hql .= ' WHERE ';
		  }

		  if($listeField[$criteria->fieldname] == null)
		  {
			throw new Exception("Field '".$criteria->fieldname."' not defined in entity '".$entityParam->getName()."' while you're searching on it");
		  }
		  $filterType =  $listeField[$criteria->fieldname]->getType();
		  
				//1 parameter
		  if($criteria->typeCriteria == OrmTypeCriteria::$EQ || $criteria->typeCriteria == OrmTypeCriteria::$NEQ 
			|| $criteria->typeCriteria == OrmTypeCriteria::$GT || $criteria->typeCriteria == OrmTypeCriteria::$GTE 
			|| $criteria->typeCriteria == OrmTypeCriteria::$LT || $criteria->typeCriteria == OrmTypeCriteria::$LTE 
			|| $criteria->typeCriteria == OrmTypeCriteria::$BEFORE || $criteria->typeCriteria == OrmTypeCriteria::$AFTER
			|| $criteria->typeCriteria == OrmTypeCriteria::$LIKE || $criteria->typeCriteria == OrmTypeCriteria::$NLIKE) {  
			$val = $criteria->paramsCriteria[0];
			
			if($criteria->typeCriteria == OrmTypeCriteria::$LIKE || $criteria->typeCriteria == OrmTypeCriteria::$NLIKE)
			{
			  $val.= '%';
			}
			
			$params[] = OrmCore::FieldToDBValue($val, $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? ';
			continue;
		  }
		  
				//0 parameter
		  if($criteria->typeCriteria == OrmTypeCriteria::$NULL || $criteria->typeCriteria == OrmTypeCriteria::$NNULL) {  
			$hql .= $criteria->fieldname.$criteria->typeCriteria;
			continue;
		  }
		  
				//2 parameters
		  if($criteria->typeCriteria == OrmTypeCriteria::$BETWEEN) {  
			$params[] = OrmCore::FieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$params[] = OrmCore::FieldToDBValue($criteria->paramsCriteria[1], $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? AND ?';
			continue;
		  }
		  
			// N parameters
		  if($criteria->typeCriteria == OrmTypeCriteria::$IN || $criteria->typeCriteria == OrmTypeCriteria::$NIN) {
			$hql .= ' ( ';
			$second = false; 
			foreach($criteria->paramsCriteria as $param) {
			  if($second) {
				$hql .= ' OR ';
			  }
			  
			  $params[] = OrmCore::FieldToDBValue($param, $filterType); 
			  $hql .= $criteria->fieldname.OrmTypeCriteria::$EQ.' ? ';
			  
			  $second = true;
			}
			$hql .= ' )';
			continue;
		  }
		  
		  //Other cases
		  if($criteria->typeCriteria == OrmTypeCriteria::$EMPTY) {
			$hql .= ' ( '.$criteria->fieldname .' is null || ' . $criteria->fieldname . "= '')";
			continue;
		  }
		  if($criteria->typeCriteria == OrmTypeCriteria::$NEMPTY) {
			$hql .= ' ( '.$criteria->fieldname .' is not null && ' . $criteria->fieldname . "!= '')";
			continue;
		  }
						 
		  throw new Exception("The OrmCriteria $criteria->typeCriteria is not manage");
		}
		
		// Order By 
		if($orderBy != null) {
			$hql .= $orderBy->getOrderBy();
		}
		else if($entityParam->getDefaultOrderBy() != null) {
			$hql .= $entityParam->getDefaultOrderBy()->getOrderBy();
		}		
		
		if($limit != null) {
			$hql .= $limit->getLimit();
		}
		
		$queryExample = $select.$hql;
		
		//If it's already in the cache, we return the result
		if(OrmCache::getInstance()->isCache($queryExample, $params)) {
		  $entities = OrmCache::getInstance()->getCache($queryExample,$params);
		} else {
			//Execution
			$result = OrmDb::execute($queryExample,
									$params,
									"Database error during OrmCore::findByExample(OrmEntity &{$entityParam->getName()}, , OrmExample \$example)");

			OrmTrace::info("findByExample : ".$result->RecordCount()." resultat(s)");
			
			$entities = OrmCore::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			OrmCache::getInstance()->setCache($queryExample, null, $entities);
		}

		return array_values($entities);

	}
   
	/**
     * Allow delete a list of OrmEntity from a list of OrmCriteria
     * 
     * Example : delete the customers with lastName 'Roger' (no casse sensitive)
     * 
     *  <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new OrmExample();
     *       $example->addCriteria('lastName', OrmTypeCriteria::$EQ, array('roger'), true);
     * 
     *       OrmCore::deleteByExample($customer, $example);
     * </code>
     * 
     *  Example : delete the customers with Id >= 90
     * 
     * <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new OrmExample();
     *       $example->addCriteria('customer_id', OrmTypeCriteria::$GTE, array(90));
     * 
     *       OrmCore::deleteByExample($customer, $example);
     * </code>
     * 
     * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals
     * 
     * NOTE 2 : you can add as many Criterias as you want in an OrmExample Object
     * 
     * @param OrmEntity an instance of the entity
     * @param OrmExample the Object OrmExample with some Criterias inside
     * 
     * @see OrmExample
     * @see OrmTypeCriteria
     */
	public static final function deleteByExample(OrmEntity &$entityParam, OrmExample $OrmExample) {
		$listeField = $entityParam->getFields();

		$criterias = $OrmExample->getCriterias();
		$delete = "delete from ".$entityParam->getDbname();
		$hql = "";
		$params = array();
		foreach($criterias as $criteria)
		{
		  if(!empty($hql))
		  {
			$hql .= ' AND ';
		  }
		  
		  if(empty($hql))
		  {
			$hql .= ' WHERE ';
		  }

		  $filterType = $listeField[$criteria->fieldname]->getType();
		  
				// 1 paramètre  
		  if($criteria->typeCriteria == OrmTypeCriteria::$EQ || $criteria->typeCriteria == OrmTypeCriteria::$NEQ 
			|| $criteria->typeCriteria == OrmTypeCriteria::$GT || $criteria->typeCriteria == OrmTypeCriteria::$GTE 
			|| $criteria->typeCriteria == OrmTypeCriteria::$LT || $criteria->typeCriteria == OrmTypeCriteria::$LTE 
			|| $criteria->typeCriteria == OrmTypeCriteria::$BEFORE || $criteria->typeCriteria == OrmTypeCriteria::$AFTER
			|| $criteria->typeCriteria == OrmTypeCriteria::$LIKE || $criteria->typeCriteria == OrmTypeCriteria::$NLIKE)
		  {  
			$params[] = OrmCore::FieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? ';
			continue;
		  }
		  
				// 0 paramètre
		  if($criteria->typeCriteria == OrmTypeCriteria::$NULL || $criteria->typeCriteria == OrmTypeCriteria::$NNULL)
		  {  
			$hql .= $criteria->fieldname.$criteria->typeCriteria;
			continue;
		  }
		  
				// 2 paramètres  
		  if($criteria->typeCriteria == OrmTypeCriteria::$BETWEEN)
		  {  
			$params[] = OrmCore::FieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$params[] = OrmCore::FieldToDBValue($criteria->paramsCriteria[1], $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? AND ?';
			continue;
		  }
				
				// N paramètres
				if($criteria->typeCriteria == OrmTypeCriteria::$IN || $criteria->typeCriteria == OrmTypeCriteria::$NIN)
				{
					$hql .= ' ( ';
					$second = false; 
					foreach($criteria->paramsCriteria as $param)
					{
						if($second)
						{
							$hql .= ' OR ';
						}
						$params[] = OrmCore::FieldToDBValue($param, $filterType); 
						$hql .= $criteria->fieldname.OrmTypeCriteria::$EQ.' ? ';
						
						$second = true;
					}
					$hql .= ' )';
					continue;
				}                        
		  
		  throw new Exception("Le OrmCriteria $criteria->typeCriteria n'est pas encore pris en charge");
		}
		$queryExample = $delete.$hql;
										
		//Execution
		$result = OrmDb::execute($queryExample,
									$params,
									"Database error during OrmCore::deleteByExample(OrmEntity &{$entityParam->getName()}, , OrmExample \$example)");
	}
      
    /**
     * Transforms an array of value into a entire OrmEntity. The array must fallow this scheme
     * 
     * Example :
     * 
     * <code>
     *       $myArray1 = array('customer_id'=>1, 'name'=>'Dupont');       
     *       $myArray2 = array('customer_id'=>2, 'name'=>'Durand', 'lastName'=>'Joe');       
     *   
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $customer1 = OrmCore::rowToEntity($customer, $myArray1);
     *       $customer2 = OrmCore::rowToEntity($customer, $myArray2);
     * 
     *       echo $customer1->get('lastName'); //return null
     *       echo $customer2->get('lastName'); //return Joe
     * 
     * </code>
     *         
     * @param OrmEntity an instance of the entity
     * @param array the list with the data
    */
	public static final function rowToEntity (OrmEntity &$entityParam, $row) {

		OrmTrace::debug("rowToEntity : ".print_r($row,true));
		$listeField = $entityParam->getFields();

		$newEntity = clone $entityParam;
		foreach($listeField as $field)
		{
		  if(!$field->isAssociateKEY())
		  {
			$newEntity->set($field->getName(),OrmCore::dbValueToField($row[$field->getName()], $field->getType()));
		  } 
		}
		return $newEntity;  
	}
  
    /**
     * Transform a PHP value into a SQL value
     * 
     * @param mixed the PHP value
     * @param mixed the CAST value
     * 
     * @see OrmCAST
     */
	private static final function FieldToDBValue($data, $type) {
		if($data == null){
			return null;
		}
		
		switch($type) {
		  case OrmCAST::$STRING : return $data;
		  case OrmCAST::$DOUBLE : return $data;
		  case OrmCAST::$INTEGER : return $data;
		  case OrmCAST::$NUMERIC : return $data;
		  case OrmCAST::$BUFFER : return $data;
		  case OrmCAST::$UUID : return $data;
		  
		  case OrmCAST::$DATE : return str_replace("'", "", cmsms()->GetDb()->DBDate($data));       
		  case OrmCAST::$TIME : return str_replace("'", "", cmsms()->GetDb()->DBTimeStamp($data));   	  
		  case OrmCAST::$DATETIME : return date("Y-m-d H:i:s",$data); 
		  case OrmCAST::$TS : return $data;
		}
	}
  
    /**
     * Transform a SQL value into a PHP value
     * 
     * @param mixed the SQL value 
     * @param mixed the CAST value
     * 
     * @see OrmCAST
     */
	private static final function dbValueToField($data, $type) {
		switch($type) {
		  case OrmCAST::$STRING : return $data;		  
		  case OrmCAST::$DOUBLE : return $data;
		  case OrmCAST::$INTEGER : return $data;		  
		  case OrmCAST::$NUMERIC : return $data;		  
		  case OrmCAST::$BUFFER : return $data;		  		
		  case OrmCAST::$UUID : return $data;
		  
		  case OrmCAST::$DATE : return cmsms()->GetDb()->UnixDate($data);
		  case OrmCAST::$TIME : return $data;
		  case OrmCAST::$DATETIME : return strtotime($data);  
		  case OrmCAST::$TS : return $data;

		}
	}
  
    /**
     * Verify in all type of entities if anyone still has a link with the OrmEntity passed in parameters (ForeignKEy and AssociateKey)
     * 
     *  This function is used by the delete* functions to avoid orphans data in database
     * 
	 * @param Orm the module which extends the Orm module                                      
     * @param OrmEntity an instance of the entity
     * @param mixed the id of the OrmEntity to verify
	 *
	 * @return a message if a link is still present. nothing if the integrity is ok
     */
	public static final function verifIntegrity(OrmEntity &$entity, $sid) {
		$listeEntitys = MyAutoload::getAllInstances($entity->getModuleName());

		foreach($listeEntitys as $key=>$anEntity) {
		  if($anEntity instanceOf OrmEntityAssociation)
			continue;
			
		  foreach($anEntity->getFields() as $field) {
			if($field->isAssociateKEY()) {
			  continue;
			}
			
			if($field->getKEYName() != null) {
			  $vals = explode('.',$field->getKEYName(),2);
			  
			  if(strtolower ($vals[0]) == strtolower ($entity->getName()))  {
				$OrmExample = new OrmExample();
				$OrmExample->addCriteria($field->getName(), OrmTypeCriteria::$EQ, array($sid));
				$entitys = OrmCore::findByExample($anEntity, $OrmExample);
				if(count($entitys) > 0)
				{
				  return "La ligne &agrave; supprimer est encore utilis&eacute;e par &laquo; ".$anEntity->getName()." &raquo;";
				}
			  }
			}
		  }
		}

		return;

	}

    /**
     * Allow realise deep search on different type of OrmEntity linked together
     * 
     * Example : 
     *   An Order has a link to a Customer (Order.customer_id)
     *   A Customer has a link to an Address (Customer.addresse_id)
     *   An Address has a link to a city (Address.city_id)
	 *   A city has a ZipCode (maybe shared by different cities)
     * 
     *  If i want the Orders for Customers from the city with zipcode equals to "01234" or "4567" I could write some shitty code !
     * 
     * <code>
     *  $cities = //Find my cities with ZipCode "01234" or "4567"
     *  foreach($cities as $city)
     *  {
     *       $addresses = //Find the address for the city $city
     *       foreach($addresses as $address)
     *       {
     *           $customers = //Find the Customers for the address $address
     *           foreach($customers as $customer)
     *           {
     *               $commandes =  //Find Traitement de recherche d'une commande possèdant le numeroclient = $customer->get('numeroclient')   
     *           }                   
     *       }                  
     *  }
     * 
     *  </code>
     * 
     *  I could also write a better code : 
     * 
     * <code>
	 *   $order = MyAutoload::getInstance($this->GetName(), 'order');
     *   $orders = OrmCore::makeDeepSearch(order, 'Order.customer_id.address_id.city_id.zipcode', array('01234', '4567'));
     * </code>
     * 
     * @param OrmEntity The entity i want to have at the end
     * @param string the path to fallow. Must be ended with the name of the Field to make the comparaison
     * @param array the array of value to make the comparaison
     * 
     */
	public static final function makeDeepSearch(OrmEntity $previousEntity, $cle, $values) {    
		OrmTRACE::debug("# : "."Start makeDeepSearch() ".$previousEntity->getName()."->".$cle);

		if($previousEntity == null)
		{
		  
		  $newCle = explode('.',$cle,2);
		  $previousEntity = $newCle[0];
		  $cle = $newCle[1];
		  $previousEntity = new $previousEntity();
		}

		$newCle = explode('.',$cle,2);
		$fieldname = $newCle[0];

		//Test de sortie : on a un seul résultat dans $newCle : le champs final
		if(count($newCle) == 1)
		{
		  OrmTRACE::debug("# : "." count(\$newCle) == 1 , donc sortie ");
		  $OrmExample = new OrmExample;
		  $OrmExample->addCriteria($fieldname, OrmTypeCriteria::$IN, $values);
		  $entitys = OrmCore::findByExample($previousEntity, $OrmExample);
		  OrmTRACE::debug("# : ".count($entitys)." R&eacute;sultat(s) retourn&eacute;s");
		  return $entitys;
		} else
		{
		  OrmTRACE::debug("# : "." poursuite ");
		}

		//Récupération de la clé distance pour une FK
		$field = $previousEntity->getFieldByName($fieldname);
		if($field->isForeignKEY() || $field->isAssociateKey())
		{
		  $foreignKEY = explode('.',$field->getKEYName(),2);
		  $nextEntity = new $foreignKEY[0]();
		} 

		if($field->isAssociateKey())
		{
		  $cle = explode('.',$newCle[1],2);
		  $cle = $cle[1];
		} else
		{
		  $cle = $newCle[1];
		} 
		
		OrmTRACE::info("# : "." make new recherche : ".$nextEntity->getName() ." , ". $cle);

		$entitys = OrmCore::makeDeepSearch($nextEntity, $cle, $values);

		if(count($entitys) == 0)
		{
		  return array();
		}

		if($nextEntity instanceof OrmEntityAssociation)
		{  
		  $fields = $nextEntity->getFields();
		  $nomFieldSuivit = explode('.',$cle,2);
		  $nomFieldSuivit = $nomFieldSuivit[0];
		  $nomFieldRetour = "N/A";
		  foreach($fields as $afield)
		  {
			if($afield->getName() == $nomFieldSuivit)
			{
			  continue;
			}
			$nomFieldRetour = $afield;
		  }
		  
		}

		$ids = array();
		foreach($entitys as $anEntity)
		{
		  OrmTRACE::info("On a trouv&eacute;  : ".$anEntity->getName()."");
		  if($anEntity instanceof OrmEntityAssociation)
		  {
			$value = $anEntity->get($nomFieldRetour->getName());
			$ids[] = $value;
			OrmTRACE::info(" valeur assoc : ".$value." pour le champs ".$nomFieldRetour->getName());
		  } else
		  {
			$value = $anEntity->get($nextEntity->getPk()->getName());
			$ids[] = $value;
			OrmTRACE::info(" valeur id : ".$value);
		  }
		  
		}


		$OrmExample = new OrmExample;
		if($nextEntity instanceof OrmEntityAssociation)
		{
		  $OrmExample->addCriteria($previousEntity->getPk()->getName(), OrmTypeCriteria::$IN, $ids);
		} else
		{
		  $OrmExample->addCriteria($fieldname, OrmTypeCriteria::$IN, $ids);
		}
		$entitys = OrmCore::findByExample($previousEntity, $OrmExample);

		return $entitys;
	}
  
	/**
	 * Will return a UUID : a unique identifier
	 * @since 0.0.2
	 */
    public static final function generateUUID(){

		//Execution
		$result = OrmDb::execute('SELECT UUID() AS uuid;',
									null,
									"Database error during OrmCore::generateUUID()");

		$row=$result->FetchRow();

		return $row['uuid'];
	}
}

?>
