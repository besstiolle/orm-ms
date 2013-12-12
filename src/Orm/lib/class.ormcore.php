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
class OrmCore
{  
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

			switch($field->getType()) {
				case OrmCAST::$STRING : 
					$hql .= 'C'; 
					if($field->getSize() != "" ) {$hql.= " (".$field->getSize().") ";} 
					break;

				case OrmCAST::$INTEGER : 
					$hql .= 'I'; 
					if($field->getSize() != "" ) {$hql.= " (".$field->getSize().") ";} 
					break;

				case OrmCAST::$NUMERIC : 
					$hql .= 'N'; 
					if($field->getSize() != "" )
					{$hql.= " (".$field->getSize().") ";} 
					break;

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

			if($field->isPrimaryKEY())
			{
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
		$db = cmsms()->GetDb();
		$taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
		$idxoptarrayUnique = array('UNIQUE');
		$dict = NewDataDictionary( $db );
		$hql = OrmCore::getFieldsToHql($entityParam);
		
		//Calling the adodb functionalities
		$sqlarray = $dict->CreateTableSQL($entityParam->getDbname(), 
												$hql,
												$taboptarray);
												
		$result = $dict->ExecuteSQLArray($sqlarray);

		if ($result === false) {
			OrmTrace::error($hql);
			OrmTrace::error("Database error during the creation of table ".$entityParam->getDbname()." for the entity " . $entityParam->getName().$db->ErrorMsg());
			throw new Exception("Database error during the creation of table ".$entityParam->getDbname()." for the entity " . $entityParam->getName().$db->ErrorMsg());
		}
		   
		OrmTrace::debug("createTable : ".print_r($sqlarray, true));
		
		//If necessary, it will create a sequence on the table.
		if($entityParam->getSeqname() != null){
			$db->CreateSequence($entityParam->getSeqname());
		}
		
		//We manage the "unique" keys
		$listesUniqueKeys = $entityParam->getUniqueKeys();

		//For each Field contained in the entity
		foreach($listesUniqueKeys as $listField) {
			//Case : unique index on many fields
			if(is_array($listField)) {
				$idxflds = implode(',', $listField);
				$md5 = md5(serialize($listField));
			} else {
				$idxflds = $listField;
				$md5 = md5($listField);
			}
			
			$sqlarray = $dict->CreateIndexSQL($md5, $entityParam->getDbname(), $idxflds, $idxoptarrayUnique);
			$result = $dict->ExecuteSQLArray($sqlarray);

			if ($result === false) {
				OrmTrace::error($hql);
				OrmTrace::error("Database error during the creation of the unique index ".$md5."(".$idxflds.") for the entity " . $entityParam->getName().$db->ErrorMsg());
				throw new Exception("Database error during the creation of the unique index ".$md5."(".$idxflds.") for the entity " . $entityParam->getName().$db->ErrorMsg());
			}
			
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

		$db = cmsms()->GetDb();

		$dict = NewDataDictionary( $db );

		$sqlarray = $dict->DropTableSQL($entityParam->getDbname());
		$dict->ExecuteSQLArray($sqlarray);

		//If necessary, it will delete a sequence on the table.
		if($entityParam->getSeqname() != null){$db->DropSequence($entityParam->getSeqname());}
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
		$db = cmsms()->GetDb();
			
		$queryAlter = "ALTER TABLE ".$entityParam->getDbname()." ".$sql;    
		$result = $db->Execute($queryAlter);
		if ($result === false){die("Database error durant l'alter de la table $entityParam->getDbname()!");}
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

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
		$values = $entityParam->getValues();
		$uniques = $entityParam->getUniqueKeys();

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
					throw new OrmIllegalArgumentException('Primary Key '.$field->getName().' can\'t be setted during insert operation for OrmEntity'.$entityParam->getName());
				} else {
					$newId = $db->GenID($entityParam->getSeqname());
					$values[$field->getName()] = $newId;
					$entityParam->set($field->getName(), $newId);
				}
			}
			
			//Empty Field that shouldn't be !
			if(!$field->isNullable() && !isset($values[$field->getName()])) {
				//Exception : if the field have a default value we set it manually
				if($field->getDefaultValue() != null){
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
		
		//control unicity on unique Field
		foreach($uniques as $couple){
			if(!is_array($couple)){
				$couple = array($couple);
			}
			$query = 'SELECT COUNT(*) FROM '.$entityParam->getDbname().' WHERE 1 ';
			$arrayFind = array();
			foreach($couple as $elt){
				if(empty($values[$elt])){
					$query .= ' AND ' . $elt . ' IS NULL ';
				} else {
					$query .= ' AND ' . $elt . ' = ? ';
					$arrayFind[] = $values[$elt];
				}
			}
			$query .= ' AND ' . $entityParam->getPk()->getName() . ' != ? ';
			$arrayFind[] = $values[$entityParam->getPk()->getName()];			
				
			$result = $db->getOne($query, $arrayFind);
			
			if ($result === false) {
				OrmTrace::error(print_r($params, true));
				OrmTrace::error($query);
				OrmTrace::error("Database error during unicity control!".$db->ErrorMsg());
				throw new Exception("Database error during unicity control!".$db->ErrorMsg());
			}
			
			if($result != 0){
				throw new OrmIllegalArgumentException('an OrmEntity '.$entityParam->getName().' with the same fields already exists in database');
			}
		}
		  		  
		//Execution
		$db->debug = true;

		OrmTrace::debug("insertEntity : ".sprintf($queryInsert, $str1, $str2));
		$result = $db->Execute(sprintf($queryInsert, $str1, $str2), $params);
		if ($result === false) {
			OrmTrace::error(print_r($params, true));
			OrmTrace::error(sprintf($queryInsert, $str1, $str2));
			OrmTrace::error("Database error durant l'insert!".$db->ErrorMsg());
			throw new Exception("Database error durant l'insert!".$db->ErrorMsg());
		}

	/*	if($entityParam->isIndexable()) {  
			OrmIndexing::AddWords($entityParam->getModuleName(), OrmCore::findById($entityParam,$arrayKEY[0]));
		}*/
		
		//empty cache
		OrmCache::clearCache();
		
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

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
		$values = $entityParam->getValues();
		$uniques = $entityParam->getUniqueKeys();

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
		
		//control unicity on unique Field
		foreach($uniques as $couple){
			if(!is_array($couple)){
				$couple = array($couple);
			}
			$query = 'SELECT COUNT(*) FROM '.$entityParam->getDbname().' WHERE 1 ';
			$arrayFind = array();
			foreach($couple as $elt){
				if(empty($values[$elt])){
					$query .= ' AND ' . $elt . ' IS NULL ';
				} else {
					$query .= ' AND ' . $elt . ' = ? ';
					$arrayFind[] = $values[$elt];
				}
			}
			$query .= ' AND ' . $entityParam->getPk()->getName() . ' != ? ';
			$arrayFind[] = $values[$entityParam->getPk()->getName()];			
				
			$result = $db->getOne($query, $arrayFind);
			
			if ($result === false) {
				OrmTrace::error(print_r($params, true));
				OrmTrace::error($query);
				OrmTrace::error("Database error during unicity control!".$db->ErrorMsg());
				throw new Exception("Database error during unicity control!".$db->ErrorMsg());
			}
			
			if($result != 0){
				throw new OrmIllegalArgumentException('an Entity '.$entityParam->getName().' with the same fields already exists in database');
			}
		}

		if($hasKey) {
			$params[] = $keyValue;
		}

		$queryUpdate = 'UPDATE '.$entityParam->getDbname().' SET '.$str.$where;

		//Execution
		$result = $db->Execute($queryUpdate, $params);
		if ($result === false){die("Database error durant l'update!");}
		/*if($entityParam->isIndexable()) {  
			OrmIndexing::UpdateWords($entityParam->getModuleName(), $entityParam);
		}*/
		
		//empty cache
		OrmCache::clearCache();
		
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

		$db = cmsms()->GetDb();
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
		$result = $db->Execute($queryDelete, $params);
		if ($result === false){die("Database error durant la suppression!");}

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
		OrmCache::clearCache();
	}
   
    /**
    * Returns the number of occurrences from the table of the entity in Parameters
    *                                     
    * @param OrmEntity an instance of the entity    
	* 
    * @return int the number of occurrences from the table   
    */
	public static final function countAll(OrmEntity &$entityParam) {

		$db = cmsms()->GetDb();

		$querySelect = 'Select count(*) FROM '.$entityParam->getDbname();

		OrmTrace::debug("countAll : ".$querySelect);
		  
		$compteur= $db->getOne($querySelect);
		if ($compteur === false){die("Database error durant la requete count(*)!");}

		return $compteur;
	}
  
    /**
    * Returns all the occurrences from the table of the entity in Parameters
    * 
    * @param OrmEntity an instance of the entity  
	*
    * @return array<OrmEntity> list of Entities found
    */
	public static final function findAll(OrmEntity &$entityParam) {
		$db = cmsms()->GetDb();

		$querySelect = 'Select * FROM '.$entityParam->getDbname();

		//If it's already in the cache, we return the result
		if(OrmCache::isCache($querySelect)) {
			$entities = OrmCache::getCache($querySelect);
		} else {
			$result = $db->Execute($querySelect);
			if ($result === false){die("Database error during OrmCore::findAll(OrmEntity &$entityParam)");}

			$entities = OrmCore::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			OrmCache::setCache($querySelect, null, $entities);
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
	private static final function _processArrayEntity(OrmEntity &$entityParam, $resultQuery) {
	
		$db = cmsms()->GetDb();
		
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
			
			$queryAdd = 'SELECT * FROM '.$entityAssoc->getDbname().' WHERE '.$fieldAssociateName.' in ('.implode(',',array_keys($entitys)).')';
			$result = $db->Execute($queryAdd);
			if ($result === false){die("Database error during request to get associative entity $entityAssocName!");}
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
	public static final function findByIds(OrmEntity &$entityParam, $ids) {
		if(count($ids) == 0)
		  return array();
			
		$db = cmsms()->GetDb();
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

		$querySelect = 'Select * FROM '.$entityParam->getDbname().' WHERE '.$where;

		//If it's already in the cache, we return the result
		if(OrmCache::isCache($querySelect, $params)) {
		  $entities = OrmCache::getCache($querySelect,$params);
		} else {
			$result = $db->Execute($querySelect, $params);
			if ($result === false){die("Database error durant la requete par Ids!");}

			$entities = OrmCore::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			OrmCache::setCache($querySelect, null, $entities);
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
	public static final function findByExample(OrmEntity &$entityParam, OrmExample $example) {

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();

		$criterias = $example->getCriterias();
		$select = "select * from ".$entityParam->getDbname();
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
		$queryExample = $select.$hql;
		
		//If it's already in the cache, we return the result
		if(OrmCache::isCache($queryExample, $params)) {
		  $entities = OrmCache::getCache($queryExample,$params);
		} else {
			$result = $db->Execute($queryExample, $params);
			if ($result === false){die($db->ErrorMsg().OrmTrace::error("Database error durant la requete par Example!"));}

			OrmTrace::info("findByExample : ".$result->RecordCount()." resultat(s)");
			
			$entities = OrmCore::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			OrmCache::setCache($queryExample, null, $entities);
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

		$db = cmsms()->GetDb();
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
										

		$result = $db->Execute($queryExample, $params);
		if ($result === false){die("Database error durant la requete par OrmExample!");}
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
		  case OrmCAST::$INTEGER : return $data;
		  case OrmCAST::$NUMERIC : return $data;
		  case OrmCAST::$BUFFER : return $data;
		  case OrmCAST::$TS : return $data;
		  case OrmCAST::$UUID : return $data;
		  
		  case OrmCAST::$DATE : return str_replace("'", "", cmsms()->GetDb()->DBDate($data));       

		  case OrmCAST::$TIME : return str_replace("'", "", cmsms()->GetDb()->DBTimeStamp($data));   	  
		  case OrmCAST::$DATETIME : return $data;
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
		  case OrmCAST::$INTEGER : return $data;		  
		  case OrmCAST::$NUMERIC : return $data;		  
		  case OrmCAST::$BUFFER : return $data;		  		  
		  case OrmCAST::$TS : return $data;
		  case OrmCAST::$UUID : return $data;
		  
		  case OrmCAST::$DATE : return cmsms()->GetDb()->UnixDate($data);
		  case OrmCAST::$TIME : return $data;//return cmsms()->GetDb()->UnixTimeStamp($data);
		  case OrmCAST::$DATETIME : return $data;

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
		OrmTRACE::info("# : "."Start makeDeepSearch() ".$previousEntity->getName()."->".$cle);

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
		  OrmTRACE::info("# : "." count(\$newCle) == 1 , donc sortie ");
		  $OrmExample = new OrmExample;
		  $OrmExample->addCriteria($fieldname, OrmTypeCriteria::$IN, $values);
		  $entitys = OrmCore::findByExample($previousEntity, $OrmExample);
		  OrmTRACE::info("# : ".count($entitys)." R&eacute;sultat(s) retourn&eacute;s");
		  return $entitys;
		} else
		{
		  OrmTRACE::info("# : "." poursuite ");
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
		 $db = cmsms()->GetDb();

		$result = $db->Execute('SELECT UUID() AS uuid;');

		$row=$result->FetchRow();

		return $row['uuid'];
	}
}

?>
