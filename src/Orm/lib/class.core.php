<?php
/**
 * Contains the class Core
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
class Core 
{  
    /**
    * Protected constructor
    *     
    */
	protected function __construct() {}
      
    /**
    * transforms the entity's structure into adodb informations 
    *         
    * @param Entity the entity
    * @return the adodb informations
    */
	private static final function getFieldsToHql(Entity &$entity) {    
		$hql = '';

		$listeField = $entity->getFields();

		//For each Field contained in the entity
		foreach($listeField as $field) {
			//We don't create the Field which are links externals on associative tables
			if($field->isAssociateKEY()) {
				continue;
			}	
			
			//We don't create the transient Fields 
			if($field->getType() == CAST::$NONE) {
				continue;
			}

			if(!empty($hql)) {
				$hql .= ' , ';
			}

			$hql .= ' '.$field->getName().' ';

			switch($field->getType()) {
				case CAST::$STRING : 
					$hql .= 'C'; 
					if($field->getSize() != "" ) {$hql.= " (".$field->getSize().") ";} 
					break;

				case CAST::$INTEGER : 
					$hql .= 'I'; 
					if($field->getSize() != "" ) {$hql.= " (".$field->getSize().") ";} 
					break;

				case CAST::$NUMERIC : 
					$hql .= 'N'; 
					if($field->getSize() != "" )
					{$hql.= " (".$field->getSize().") ";} 
					break;

				case CAST::$BUFFER : $hql .= 'X'; break;

				case CAST::$DATE : $hql .= 'D'; break;

				case CAST::$TIME : $hql .= 'T'; break;   

				case CAST::$UUID : $hql .= 'C (32) '; break;   

				case CAST::$TS : $hql .= 'I (10) '; break; //workaround for the real timestamp missing in ADODBLITE

				case CAST::$DATETIME : $hql .= CMS_ADODB_DT; break;   
			}
			
			//Manage the default value
			if($field->getDefaultValue() != null){
				if($field->getType() == CAST::$STRING || $field->getType() == CAST::$BUFFER) {
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
	
		Trace::info($hql);

		return $hql;
	}
	
    /**
    * Create a table into Database from the structure of an Entity
    *  Will also create the sequence if it's needed
    *  
    *   example with a Customer entity : 
    * <code>
    * 
    * class Customer extends Entity
    * {
    *    public function __construct()
    *    {
    *        parent::__construct($this->GetName(), 'customer');
    *        
    *        $this->add(new Field('customer_id'  
	*			, CAST::$INTEGER
	*			, null
	*			, null
	*			, KEY::$PK 
	*			));
    * 
    *        $this->add(new Field('name'
    *        	, CAST::$STRING 
    *        	, 32
    *        	));
    * 
    *        $this->add(new Field('lastname'
    *        	, CAST::$STRING
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
    *   Core::createTable($customer);
    * </code>
    * 
    *  The function will also try to populate the table with a call to the function initTable() if it's define into the entity class.
    * 
	* @param Orm the module which extends the Orm module
    * @param Entity an instance of the entity
    */
	public static final function createTable(Entity &$entityParam) {
		$db = cmsms()->GetDb();
		$taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
		$idxoptarrayUnique = array('UNIQUE');
		$dict = NewDataDictionary( $db );
		$hql = Core::getFieldsToHql($entityParam);
		
		//Calling the adodb functionalities
		$sqlarray = $dict->CreateTableSQL($entityParam->getDbname(), 
												$hql,
												$taboptarray);
												
		$result = $dict->ExecuteSQLArray($sqlarray);

		if ($result === false) {
			Trace::error($hql.'<br/>');
			Trace::error("Database error during the creation of table ".$entityParam->getDbname()." for the entity " . $entityParam->getName().$db->ErrorMsg());
			throw new Exception("Database error during the creation of table ".$entityParam->getDbname()." for the entity " . $entityParam->getName().$db->ErrorMsg());
		}
		   
		Trace::debug("createTable : ".print_r($sqlarray, true).'<br/>');
		
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
				Trace::error($hql.'<br/>');
				Trace::error("Database error during the creation of the unique index ".$md5."(".$idxflds.") for the entity " . $entityParam->getName().$db->ErrorMsg());
				throw new Exception("Database error during the creation of the unique index ".$md5."(".$idxflds.") for the entity " . $entityParam->getName().$db->ErrorMsg());
			}
			
		}

		//We initiate the table.
		$entityParam->initTable();
	}
    
    /**
    * Drop the table for the Entity in parameters
    *  Will also drop the sequence if it's needed
    * 
    * @param Entity an instance of the entity
    */
	public static final function dropTable(Entity &$entityParam) {

		$db = cmsms()->GetDb();

		$dict = NewDataDictionary( $db );

		$sqlarray = $dict->DropTableSQL($entityParam->getDbname());
		$dict->ExecuteSQLArray($sqlarray);

		//If necessary, it will delete a sequence on the table.
		if($entityParam->getSeqname() != null){$db->DropSequence($entityParam->getSeqname());}
	}  
  
    /**
    * Will edit the table of the Entity in parameters with the SQL query in parameters
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
    *       Core::alterTable($customer, "ADD `newColumn` INT NOT NULL");
    *       Core::alterTable($customer, "DROP `oldColumn`");
    * </code>
    *   
    * 
    * @param Entity an instance of the entity
    * @param string the SQL query
    */
	public static final function alterTable(Entity &$entityParam, $sql) {
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
     *       Core::insertEntity($customer);
     * </code>
	 *
	 * You could also code for the last line : $customer->save();  it will automatically 
     * 
     * Important : you must not set the primaryKey value. It will be calculate by the system it-self
     *                                      
     * @param Entity an instance of the entity
	 *
     * @return the entity saved with its new new Id (customer_id in my example)
     */
	public static final function insertEntity(Entity &$entityParam) {

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
		$listeValues = $entityParam->getValues();

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
				if(!empty($listeValues[$field->getName()])) {
					throw new IllegalArgumentException('Primary Key '.$field->getName().' can\'t be setted during insert operation for Entity'.$entityParam->getName());
				} else {
					$newId = $db->GenID($entityParam->getSeqname());
					$listeValues[$field->getName()] = $newId;
					$entityParam->set($field->getName(), $newId);
				}
			}
			
			//Empty Field that shouldn't be !
			if(!$field->isNullable() && !isset($listeValues[$field->getName()])) {
				throw new IllegalArgumentException('the field '.$field->getName().' of Entity  '.$entityParam->getName().' can\'t be null');
			}
			
			$val = null;
			if(isset($listeValues[$field->getName()]))
			{
				$params[] = Core::FieldToDBValue($listeValues[$field->getName()], $field->getType());
			} else {
				$params[] = null;
			}
		}
		  		  
		//Execution
		$db->debug = true;

		Trace::debug("insertEntity : ".sprintf($queryInsert, $str1, $str2));
		$result = $db->Execute(sprintf($queryInsert, $str1, $str2), $params);
		if ($result === false) {
			Trace::error(print_r($params, true).'<br/>');
			Trace::error(sprintf($queryInsert, $str1, $str2).'<br/>');
			Trace::error("Database error durant l'insert!".$db->ErrorMsg());
			throw new Exception("Database error durant l'insert!".$db->ErrorMsg());
		}

		if($entityParam->isIndexable()) {  
			Indexing::AddWords($entityParam->getModuleName(), Core::findById($entityParam,$arrayKEY[0]));
		}
		
		//empty cache
		Cache::clearCache();
		
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
     *       $customer = Core::findById($customer, 1);
     *       $customer->set('lastName'=>'NewLastName');
     * 
     *       Core::updateEntity($customer);
     * </code>
     * 	 
     * You could also code for the last line : $customer->save();  it will automatically 
	 *
     * @param Entity an instance of the entity
	 *
	 * @return the entity saved with its new Id (customer_id in my example)
     */	
	public static final function updateEntity(Entity &$entityParam) {

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
		$values = $entityParam->getValues();

		$str = "";
		$where = '';
		$params = array();
		$hasKey = false;
		  
		//All the required values must be present
		foreach($listeField as $field) {
		
			//If it's not set
			if(empty($values[$field->getName()])) {
			
				//If it's a primaryKey we throw a exception
				if($field->isPrimaryKEY()) {
					throw new IllegalArgumentException('the primaryKey '.$field->getName().' is missing for the entity : '.$entityParam->getName());
				}
				
				//an empty associative field : no problem, we can pass
				if($field->isAssociateKEY())
				{
					continue;
				}
				
				//If it's a no nullable field we throw a exception
				if(!$field->isNullable()) {
					throw new IllegalArgumentException('the field '.$field->getName().' is not nullable for the entity : '.$entityParam->getName());
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

			$params[] = Core::FieldToDBValue($values[$field->getName()], $field->getType());

		}

		if($hasKey) {
			$params[] = $keyValue;
		}

		$queryUpdate = 'UPDATE '.$entityParam->getDbname().' SET '.$str.$where;

		//Execution
		$result = $db->Execute($queryUpdate, $params);
		if ($result === false){die("Database error durant l'update!");}
		if($entityParam->isIndexable()) {  
			Indexing::UpdateWords($entityParam->getModuleName(), $entityParam);
		}
		
		//empty cache
		Cache::clearCache();
		
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
    *       Core::deleteByIds($customer, array(1);
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
    *       Core::deleteByIds($customer, $myArray);
    * </code>
    *                                    
    * @param Entity an instance of the entity    
    * @param array all the ids to delete ($customer_id in my example)
    */
	public static final function deleteByIds(Entity &$entityParam, $ids) {

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
		  $params[] = Core::FieldToDBValue($sid, $type);  
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
			// Indexing::setSearch($modops->GetSearchModule());
		  // } else
		  // {
			// die("ko");
		  // }
		  foreach($ids as $sid)
		  {
			Indexing::DeleteWords($entityParam->getModuleName(), $entityParam, $sid);
		  }
		  
		}
		
		//empty cache
		Cache::clearCache();
	}
   
    /**
    * Returns the number of occurrences from the table of the entity in Parameters
    *                                     
    * @param Entity an instance of the entity    
	* 
    * @return int the number of occurrences from the table   
    */
	public static final function countAll(Entity &$entityParam) {

		$db = cmsms()->GetDb();

		$querySelect = 'Select count(*) FROM '.$entityParam->getDbname();

		Trace::debug("countAll : ".$querySelect);
		  
		$compteur= $db->getOne($querySelect);
		if ($compteur === false){die("Database error durant la requete count(*)!");}

		return $compteur;
	}
  
    /**
    * Returns all the occurrences from the table of the entity in Parameters
    * 
    * @param Entity an instance of the entity  
	*
    * @return array<Entity> list of Entities found
    */
	public static final function findAll(Entity &$entityParam) {
		$db = cmsms()->GetDb();

		$querySelect = 'Select * FROM '.$entityParam->getDbname();

		//If it's already in the cache, we return the result
		if(Cache::isCache($querySelect)) {
			$entities = Cache::getCache($querySelect);
		} else {
			$result = $db->Execute($querySelect);
			if ($result === false){die("Database error during Core::findAll(Entity &$entityParam)");}

			$entities = Core::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			Cache::setCache($querySelect, null, $entities);
		}
		
		return array_values($entities);
	}
	
	/**
	 * Inner function to factorize some code for each "find*" functions
	 *  It will retrieve all the informations on the AK's Field
	 *
     * @param Entity an instance of the entity  
	 * @param resultQuery the returned value of sql execution
	 *
     * @return array<Entity> list of Entities populate with all the informations on AK's Field
	 **/
	private static final function _processArrayEntity(Entity &$entityParam, $resultQuery) {
	
		$db = cmsms()->GetDb();
		
		$entitys = array();
		while ($row = $resultQuery->FetchRow()) {
		  $anEntity = Core::rowToEntity($entityParam, $row);
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
	* Return a Entity from its Id
	* 
	* @param Entity an instance of the entity  
	* @param int the Id to find
	* @return Entity the Entity found or NULL
	*/
	public static final function findById(Entity &$entityParam, $id) {
		$liste = Core::findByIds($entityParam, array($id));
		
		if(!isset($liste[0])){
			return null;
		}
		
		return $liste[0];
	}
  
	/**
	* Return Entities from their Ids
	* 
	* @param Entity an instance of the entity  
	* @param array list of the Ids to find
	*
	* @return array<Entity> list of Entities found
	*/
	public static final function findByIds(Entity &$entityParam, $ids) {
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
			$params[] = Core::FieldToDBValue($id, $field->getType());
		  }
		}

		$querySelect = 'Select * FROM '.$entityParam->getDbname().' WHERE '.$where;

		//If it's already in the cache, we return the result
		if(Cache::isCache($querySelect, $params)) {
		  $entities = Cache::getCache($querySelect,$params);
		} else {
			$result = $db->Execute($querySelect, $params);
			if ($result === false){die("Database error durant la requete par Ids!");}

			$entities = Core::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			Cache::setCache($querySelect, null, $entities);
		}

		return array_values($entities);
	}
  
    /**
     * Allow search a list of Entity from a list of Criteria
     * 
     * Example : find the customers with lastName 'Roger' (no casse sensitive)
     * 
     *  <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new Example();
     *       $example->addCriteria('lastName', TypeCriteria::$EQ, array('roger'), true);
     * 
     *       Core::findByExample($customer, $example);
     * </code>
     * 
     *  Example : find the customers with Id >= 90
     * 
     * <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new Example();
     *       $example->addCriteria('customer_id', TypeCriteria::$GTE, array(90));
     * 
     *       Core::findByExample($customer, $example);
     * </code>
     * 
     * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals
     * 
     * NOTE 2 : you can add as many Criterias as you want in an Example Object
     * 
     * @param Entity an instance of the entity
     * @param Example the Object Example with some Criterias inside
     * 
     * @see Example
     * @see TypeCriteria
     */
	public static final function findByExample(Entity &$entityParam, Example $example) {

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

		  $filterType =  $listeField[$criteria->fieldname]->getType();
		  
				//1 parameter
		  if($criteria->typeCriteria == TypeCriteria::$EQ || $criteria->typeCriteria == TypeCriteria::$NEQ 
			|| $criteria->typeCriteria == TypeCriteria::$GT || $criteria->typeCriteria == TypeCriteria::$GTE 
			|| $criteria->typeCriteria == TypeCriteria::$LT || $criteria->typeCriteria == TypeCriteria::$LTE 
			|| $criteria->typeCriteria == TypeCriteria::$BEFORE || $criteria->typeCriteria == TypeCriteria::$AFTER
			|| $criteria->typeCriteria == TypeCriteria::$LIKE || $criteria->typeCriteria == TypeCriteria::$NLIKE) {  
			$val = $criteria->paramsCriteria[0];
			
			if($criteria->typeCriteria == TypeCriteria::$LIKE || $criteria->typeCriteria == TypeCriteria::$NLIKE)
			{
			  $val.= '%';
			}
			
			$params[] = Core::FieldToDBValue($val, $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? ';
			continue;
		  }
		  
				//0 parameter
		  if($criteria->typeCriteria == TypeCriteria::$NULL || $criteria->typeCriteria == TypeCriteria::$NNULL) {  
			$hql .= $criteria->fieldname.$criteria->typeCriteria;
			continue;
		  }
		  
				//2 parameters
		  if($criteria->typeCriteria == TypeCriteria::$BETWEEN) {  
			$params[] = Core::FieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$params[] = Core::FieldToDBValue($criteria->paramsCriteria[1], $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? AND ?';
			continue;
		  }
		  
			// N parameters
		  if($criteria->typeCriteria == TypeCriteria::$IN || $criteria->typeCriteria == TypeCriteria::$NIN) {
			$hql .= ' ( ';
			$second = false; 
			foreach($criteria->paramsCriteria as $param) {
			  if($second) {
				$hql .= ' OR ';
			  }
			  
			  $params[] = Core::FieldToDBValue($param, $filterType); 
			  $hql .= $criteria->fieldname.TypeCriteria::$EQ.' ? ';
			  
			  $second = true;
			}
			$hql .= ' )';
			continue;
		  }
		  
		  //Other cases
		  if($criteria->typeCriteria == TypeCriteria::$EMPTY) {
			$hql .= ' ( '.$criteria->fieldname .' is null || ' . $criteria->fieldname . "= '')";
			continue;
		  }
		  if($criteria->typeCriteria == TypeCriteria::$NEMPTY) {
			$hql .= ' ( '.$criteria->fieldname .' is not null && ' . $criteria->fieldname . "!= '')";
			continue;
		  }
						 
		  throw new Exception("The Criteria $criteria->typeCriteria is not manage");
		}
		$queryExample = $select.$hql;
		
		//If it's already in the cache, we return the result
		if(Cache::isCache($queryExample, $params)) {
		  $entities = Cache::getCache($queryExample,$params);
		} else {
			$result = $db->Execute($queryExample, $params);
			if ($result === false){die($db->ErrorMsg().Trace::error("Database error durant la requete par Example!"));}

			Trace::info("findByExample : ".$result->RecordCount()." resultat(s)");
			
			$entities = Core::_processArrayEntity($entityParam, $result);
			
			//We push the result into the cache before return it
			Cache::setCache($queryExample, null, $entities);
		}

		return array_values($entities);

	}
   
	/**
     * Allow delete a list of Entity from a list of Criteria
     * 
     * Example : delete the customers with lastName 'Roger' (no casse sensitive)
     * 
     *  <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new Example();
     *       $example->addCriteria('lastName', TypeCriteria::$EQ, array('roger'), true);
     * 
     *       Core::deleteByExample($customer, $example);
     * </code>
     * 
     *  Example : delete the customers with Id >= 90
     * 
     * <code>
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $example = new Example();
     *       $example->addCriteria('customer_id', TypeCriteria::$GTE, array(90));
     * 
     *       Core::deleteByExample($customer, $example);
     * </code>
     * 
     * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals
     * 
     * NOTE 2 : you can add as many Criterias as you want in an Example Object
     * 
     * @param Entity an instance of the entity
     * @param Example the Object Example with some Criterias inside
     * 
     * @see Example
     * @see TypeCriteria
     */
	public static final function deleteByExample(Entity &$entityParam, Example $Example) {

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();

		$criterias = $Example->getCriterias();
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
		  if($criteria->typeCriteria == TypeCriteria::$EQ || $criteria->typeCriteria == TypeCriteria::$NEQ 
			|| $criteria->typeCriteria == TypeCriteria::$GT || $criteria->typeCriteria == TypeCriteria::$GTE 
			|| $criteria->typeCriteria == TypeCriteria::$LT || $criteria->typeCriteria == TypeCriteria::$LTE 
			|| $criteria->typeCriteria == TypeCriteria::$BEFORE || $criteria->typeCriteria == TypeCriteria::$AFTER
			|| $criteria->typeCriteria == TypeCriteria::$LIKE || $criteria->typeCriteria == TypeCriteria::$NLIKE)
		  {  
			$params[] = Core::FieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? ';
			continue;
		  }
		  
				// 0 paramètre
		  if($criteria->typeCriteria == TypeCriteria::$NULL || $criteria->typeCriteria == TypeCriteria::$NNULL)
		  {  
			$hql .= $criteria->fieldname.$criteria->typeCriteria;
			continue;
		  }
		  
				// 2 paramètres  
		  if($criteria->typeCriteria == TypeCriteria::$BETWEEN)
		  {  
			$params[] = Core::FieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$params[] = Core::FieldToDBValue($criteria->paramsCriteria[1], $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? AND ?';
			continue;
		  }
				
				// N paramètres
				if($criteria->typeCriteria == TypeCriteria::$IN || $criteria->typeCriteria == TypeCriteria::$NIN)
				{
					$hql .= ' ( ';
					$second = false; 
					foreach($criteria->paramsCriteria as $param)
					{
						if($second)
						{
							$hql .= ' OR ';
						}
						$params[] = Core::FieldToDBValue($param, $filterType); 
						$hql .= $criteria->fieldname.TypeCriteria::$EQ.' ? ';
						
						$second = true;
					}
					$hql .= ' )';
					continue;
				}                        
		  
		  throw new Exception("Le Criteria $criteria->typeCriteria n'est pas encore pris en charge");
		}
		$queryExample = $delete.$hql;
										

		$result = $db->Execute($queryExample, $params);
		if ($result === false){die("Database error durant la requete par Example!");}
	}
      
    /**
     * Transforms an array of value into a entire Entity. The array must fallow this scheme
     * 
     * Example :
     * 
     * <code>
     *       $myArray1 = array('customer_id'=>1, 'name'=>'Dupont');       
     *       $myArray2 = array('customer_id'=>2, 'name'=>'Durand', 'lastName'=>'Joe');       
     *   
     *       $customer = MyAutoload::getInstance($this->GetName(), 'customer');
     * 
     *       $customer1 = Core::rowToEntity($customer, $myArray1);
     *       $customer2 = Core::rowToEntity($customer, $myArray2);
     * 
     *       echo $customer1->get('lastName'); //return null
     *       echo $customer2->get('lastName'); //return Joe
     * 
     * </code>
     *         
     * @param Entity an instance of the entity
     * @param array the list with the data
    */
	public static final function rowToEntity (Entity &$entityParam, $row) {

		Trace::debug("rowToEntity : ".print_r($row,true)."<br/>");
		$listeField = $entityParam->getFields();

		$newEntity = clone $entityParam;
		foreach($listeField as $field)
		{
		  if(!$field->isAssociateKEY())
		  {
			$newEntity->set($field->getName(),Core::dbValueToField($row[$field->getName()], $field->getType()));
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
     * @see CAST
     */
	private static final function FieldToDBValue($data, $type) {
		if($data == null){
			return null;
		}
		
		switch($type) {
		  case CAST::$STRING : return $data;
		  case CAST::$INTEGER : return $data;
		  case CAST::$NUMERIC : return $data;
		  case CAST::$BUFFER : return $data;
		  case CAST::$TS : return $data;
		  case CAST::$UUID : return $data;
		  
		  case CAST::$DATE : return str_replace("'", "", cmsms()->GetDb()->DBDate($data));       

		  case CAST::$TIME : return str_replace("'", "", cmsms()->GetDb()->DBTimeStamp($data));   	  
		  case CAST::$DATETIME : return $data;
		}
	}
  
    /**
     * Transform a SQL value into a PHP value
     * 
     * @param mixed the SQL value 
     * @param mixed the CAST value
     * 
     * @see CAST
     */
	private static final function dbValueToField($data, $type) {
		switch($type) {
		  case CAST::$STRING : return $data;		  
		  case CAST::$INTEGER : return $data;		  
		  case CAST::$NUMERIC : return $data;		  
		  case CAST::$BUFFER : return $data;		  		  
		  case CAST::$TS : return $data;
		  case CAST::$UUID : return $data;
		  
		  case CAST::$DATE : return cmsms()->GetDb()->UnixDate($data);
		  case CAST::$TIME : return $data;//return cmsms()->GetDb()->UnixTimeStamp($data);
		  case CAST::$DATETIME : return $data;

		}
	}
  
    /**
     * Verify in all type of entities if anyone still has a link with the Entity passed in parameters (ForeignKEy and AssociateKey)
     * 
     *  This function is used by the delete* functions to avoid orphans data in database
     * 
	 * @param Orm the module which extends the Orm module                                      
     * @param Entity an instance of the entity
     * @param mixed the id of the Entity to verify
	 *
	 * @return a message if a link is still present. nothing if the integrity is ok
     */
	public static final function verifIntegrity(Entity &$entity, $sid) {
		$listeEntitys = MyAutoload::getAllInstances($entity->getModuleName());

		foreach($listeEntitys as $key=>$anEntity) {
		  if($anEntity instanceOf EntityAssociation)
			continue;
			
		  foreach($anEntity->getFields() as $field) {
			if($field->isAssociateKEY()) {
			  continue;
			}
			
			if($field->getKEYName() != null) {
			  $vals = explode('.',$field->getKEYName(),2);
			  
			  if(strtolower ($vals[0]) == strtolower ($entity->getName()))  {
				$Example = new Example();
				$Example->addCriteria($field->getName(), TypeCriteria::$EQ, array($sid));
				$entitys = Core::findByExample($anEntity, $Example);
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
     * Allow realise deep search on different type of Entity linked together
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
     *   $orders = Core::makeDeepSearch(order, 'Order.customer_id.address_id.city_id.zipcode', array('01234', '4567'));
     * </code>
     * 
     * @param Entity The entity i want to have at the end
     * @param string the path to fallow. Must be ended with the name of the Field to make the comparaison
     * @param array the array of value to make the comparaison
     * 
     */
	public static final function makeDeepSearch(Entity $previousEntity, $cle, $values) {    
		TRACE::info("# : "."Start makeDeepSearch() ".$previousEntity->getName()."->".$cle);

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
		  TRACE::info("# : "." count(\$newCle) == 1 , donc sortie ");
		  $Example = new Example;
		  $Example->addCriteria($fieldname, TypeCriteria::$IN, $values);
		  $entitys = Core::findByExample($previousEntity, $Example);
		  TRACE::info("# : ".count($entitys)." R&eacute;sultat(s) retourn&eacute;s");
		  return $entitys;
		} else
		{
		  TRACE::info("# : "." poursuite ");
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
		
		TRACE::info("# : "." make new recherche : ".$nextEntity->getName() ." , ". $cle);

		$entitys = Core::makeDeepSearch($nextEntity, $cle, $values);

		if(count($entitys) == 0)
		{
		  return array();
		}

		if($nextEntity instanceof EntityAssociation)
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
		  TRACE::info("<br/>On a trouv&eacute;  : ".$anEntity->getName()."");
		  if($anEntity instanceof EntityAssociation)
		  {
			$value = $anEntity->get($nomFieldRetour->getName());
			$ids[] = $value;
			TRACE::info(" valeur assoc : ".$value." pour le champs ".$nomFieldRetour->getName());
		  } else
		  {
			$value = $anEntity->get($nextEntity->getPk()->getName());
			$ids[] = $value;
			TRACE::info(" valeur id : ".$value);
		  }
		  
		}


		$Example = new Example;
		if($nextEntity instanceof EntityAssociation)
		{
		  $Example->addCriteria($previousEntity->getPk()->getName(), TypeCriteria::$IN, $ids);
		} else
		{
		  $Example->addCriteria($fieldname, TypeCriteria::$IN, $ids);
		}
		$entitys = Core::findByExample($previousEntity, $Example);

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
