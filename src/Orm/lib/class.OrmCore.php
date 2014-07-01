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
	private static final function _getFieldsToHql(OrmEntity &$entityParam) {    
		$hql = '';

		$listeField = $entityParam->getFields();

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



			$castType = $field->getType();
			$size = $field->getSize();
			if(OrmCAST::$INHERIT == $castType) {
				//Must take the distant type
				list($entityAssocName, $fieldAssociateName) = explode(".", $field->getKeyName());
				$castType = (new $entityAssocName())->getFieldByName($fieldAssociateName)->getType();
				$size = (new $entityAssocName())->getFieldByName($fieldAssociateName)->getSize();
			}

			
			$size = $size != "" ? " (".$size.") " : "";
			switch($castType) {
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

				default : throw new OrmIllegalArgumentException('CAST TYPE '.$castType.' for the field '.$entityParam->getName().'->'.$field->getName().' is not a valid OrmCAST option');
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
				if($entityParam->isAutoincrement()) {
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
	
		$hql = OrmCore::_getFieldsToHql($entityParam);
		var_dump($hql);
		$result = OrmDb::createTable($entityParam->getDbname(), $hql);
				
		//If necessary, it will create a sequence on the table.
		if($entityParam->getSeqname() != null){
			OrmDb::createSequence($entityParam->getSeqname());
		}
		
		//We manage the ("unique") indexes
		$indexes = $entityParam->getIndexes();

		//For each Field contained in the entity
		foreach($indexes as $index) {
			$result = OrmDb::createIndex($entityParam->getDbname(), $index['fields'], $index['unique']);
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

		OrmDb::dropTable($entityParam->getDbname());

		//If necessary, it will delete a sequence on the table.
		if($entityParam->getSeqname() != null){
			OrmDb::dropSequence($entityParam->getSeqname());
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
						
			//We don't insert the transient Fields 
			if($field->getType() == OrmCAST::$NONE) {
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
					$newId = OrmDb::genID($entityParam->getSeqname());
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
				$params[] = OrmCore::_fieldToDBValue($values[$field->getName()], $field->getType());
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
            foreach($entityParam->getPk() as $pkname => $pk){
                $query .= ' AND ' . $pkname . ' != ? ';
                $arrayFind[] = $values[$pkname];
            }
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
		
        if($entityParam->isAutoincrement()){
            //Get the last Id inserted  (only for entity with only 1 integer Field as Key)
            // @see also : http://stackoverflow.com/questions/372388/mysql-select-last-insert-id-for-compound-key-is-it-possible
            $nbPkInteger = 0;
            $nameOfPk = '';
            foreach($entityParam->getPk() as $pk){
                if($pk->getType == OrmCAST::$INTEGER){
                    $nbPkInteger++;
                    $nameOfPk = $pk->getName();
		            }
            	}
            if($nbPkInteger == 1 && $nameOfPk != '' && empty($values[$nameOfPk])){
                $newId = OrmDb::getOne("SELECT LAST_INSERT_ID()");
                $entityParam->set($nameOfPk, $newId);
            }
        }
        
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
		
			//We don't update the transient Fields 
			if($field->getType() == OrmCAST::$NONE) {
				continue;
			}

			//if the field is empty and we have a default value we set it manually
			if(empty($values[$field->getName()]) && $field->getDefaultValue() != null){
				$values[$field->getName()] = $field->getDefaultValue();
			} 
		
			//If it's not set
			if(is_null(($values[$field->getName()]))) {
			
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

			$params[] = OrmCore::_fieldToDBValue($values[$field->getName()], $field->getType());

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
			foreach($entityParam->getPk() as $pkname => $pk){
				$query .= ' AND ' . $pkname . ' != ? ';
				$arrayFind[] = $values[$pkname];			
			}

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
		  $params[] = OrmCore::_fieldToDBValue($sid, $type);  
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
		return OrmCore::findByExample($entityParam, new OrmExample(), $orderBy, $limit);
	}
	
	/**
	 * Inner function to factorize some code for each "find*" functions
	 *  It will retrieve all the informations on the AK's Field
	 *
     * @param OrmEntity an instance of the entity  
	 * @param resultQuery the returned value of sql execution
	 * @param OrmOrderBy if you want order the external entity (via AK or FK)
	 *
     * @return array<OrmEntity> list of Entities populate with all the informations on AK's Field
	 **/
	private static final function _processArrayEntity(OrmEntity &$entityParam, $resultQuery, OrmOrderBy &$orderBy=null) {

		
		$entitys = array();
		while ($row = $resultQuery->FetchRow()) {
		  $entitys[] = OrmCore::rowToEntity($entityParam, $row);
		}
				
		//Test the presence of $AK
		$listeField = $entityParam->getFields();
		foreach($listeField as $field) {
		
		  if($field->isAssociateKEY()) {
			
			$fieldAssociateName = null;
			if(strpos($field->getKEYName(),".")){
				list($entityAssocName, $fieldAssociateName) = explode(".", $field->getKEYName());
				$entityAssoc = new $entityAssocName();
				


				$sqlfieldname = array();
				$sqlfieldvalue = array();
				list($entityCurrentName, $fieldCurrentName) = explode(".", $entityAssoc->getFieldByName($fieldAssociateName)->getKEYName());
						
				//The path must return to the current entity
				if(strcasecmp($entityCurrentName,$entityParam->getName()) != 0){
					throw new OrmIllegalConfigurationException("It seems you have a wrong path between {$entityParam->getName()}.{$field->getName()} and {$entityAssocName}.{$fieldAssociateName}");
				}

				//Initiate the field associate with an empty array.
				$entitysKeys = array();
				foreach($entitys as $entity){
					$entity->set($field->getName(), array());
				}

				//TODO : remembering this part in memory to avoid triple reseach
				$sqlfieldvalue[] = $fieldCurrentName;
				$assocFieldsName[] = $fieldAssociateName;
				$sqlfieldname[] = " {$fieldAssociateName} = ? ";

				

			// We have an AK with 2/more FK (also we are an entity with composite primary key)	
			} else {
				$entityAssocName = $field->getKEYName();
				$entityAssoc = new $entityAssocName();

				//Initiate the field associate with an empty array.
				$entitysKeys = array();
				foreach($entitys as $entity){
					$entity->set($field->getName(), array());
				}

				//TODO : remembering this part in memory to avoid triple reseach
				$sqlfieldname = array();
				$sqlfieldvalue = array();
				foreach ($entityAssoc->getFields() as $assocField) {

					// We don't want another weird FK on an entire entity
					if($assocField->isForeignKey() && strpos($assocField->getKEYName(),".")){
						
						list($entityCurrentName, $fieldCurrentName) = explode(".", $assocField->getKEYName());

						// If this FK point back on us
						if(strcasecmp($entityCurrentName,$entityParam->getName()) === 0){
							$sqlfieldvalue[] = $fieldCurrentName;
							$assocFieldsName[] = $assocField->getName();
							$sqlfieldname[] = " {$assocField->getName()} = ? ";
						}
					}
				}

			}

			// ( field1 = ? and field2 = ? ) 
			$sqlfieldsname = ' ( ' . implode(" AND ", $sqlfieldname) . ' ) ' ;
			$sqlfieldsnameOR = ' OR ' . $sqlfieldsname;

			$sqlfields = " 1 ";
			if(!empty($entitys)){
				$sqlfields = $sqlfieldsname . str_repeat($sqlfieldsnameOR, count($entitys)-1);	
			} 

			$queryAdd = 'SELECT * FROM ' . $entityAssoc->getDbname() . ' WHERE ' . $sqlfields;
			$queryParams = array();
			foreach($entitys as $entity){
				foreach ($sqlfieldvalue as $fieldname) {
					$queryParams[] = $entity->get($fieldname);
				}
				
			}
						
			// Order By 
			if($orderBy != null) {
				$queryAdd .= $orderBy->getOrderBy();
			}
			else if($entityParam->getDefaultOrderBy() != null) {
				$queryAdd .= $entityParam->getDefaultOrderBy()->getOrderBy();
			}

			//Execution
			$result = OrmDb::execute($queryAdd,
									$queryParams,
									"Database error during request to get associative entity $entityAssocName");
			
			$countFields = count($sqlfieldvalue);

			while ($rowAssociate = $result->FetchRow()) {
				foreach($entitys as $entity){
					$alreadyPresent = null;
					$ismatch = true;
					for ( $i = 0 ; $i < $countFields; $i++){

						if($entity->get($sqlfieldvalue[$i]) !== $rowAssociate[$assocFieldsName[$i]]){
							$ismatch = false;
							break;
						}
					}
					if($ismatch){
						// At this point we found a result for the current entity.
						// We must add this result into the $field 
						$alreadyPresent = $entity->get($field->getName());
						$alreadyPresent[] = $rowAssociate;

						$entity->set($field->getName(), $alreadyPresent);

					}
				}
				/*
				$arrayIdEntitiesDest = $entitys[$rowAssociate[$fieldAssociateName]]->get($field->getName());
				
				$entityAssocKeys = $entityAssoc->getPk();
				if(count($entityAssocKeys) != 1){
					//Put the combinaison of key in another array
					$compound = array();
					foreach ($entityAssocKeys as $pk){
					   $compound[$pk->getName()] = $rowAssociate[get($pk->getName())];
					}
					$arrayIdEntitiesDest[] = $compound;
				} else {
					$arrayIdEntitiesDest[] = $rowAssociate[get($entityAssocKeys[0]->getName())];
				}

				$entitys[$rowAssociate[$fieldAssociateName]]->set($field->getName(),$arrayIdEntitiesDest);*/
			}

		  } 
		}
		
		return $entitys;
	}
  
	/**
	* Return a OrmEntity from its Id
	* 
	* @param OrmEntity an instance of the entity  
	* @param mixed the Id to find
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

		if($entityParam->hasCompositeKey()){
			throw new OrmIllegalArgumentException('You cannot used function findById/findByIds on Entity with a compound key. You should try findByExample functions');
		}
		
		foreach ($entityParam->getPk() as $pkname => $pk) {
			$example = new OrmExample();
			$example->addCriteria($pkname, OrmTypeCriteria::$EQ, $ids);
			return OrmCore::findByExample($entityParam, $example, $orderBy);
		}


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
	public static final function findByExample(OrmEntity $entityParam, OrmExample $example, OrmOrderBy $orderBy = null, OrmLimit $limit = null, $condition = 'AND') {

		$listeField = $entityParam->getFields();

		$criterias = $example->getCriterias();
		$select = "SELECT * FROM ".$entityParam->getDbname().' WHERE ';
						
		list($hql, $params) = OrmCore::_getHqlExample($listeField, $criterias, $condition);
				
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

				OrmTrace::debug("findByExample : ".$result->RecordCount()." resultat(s)");
				
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
	public static final function deleteByExample(OrmEntity &$entityParam, OrmExample $OrmExample, $condition = 'AND') {
		$listeField = $entityParam->getFields();

		$criterias = $OrmExample->getCriterias();
		$delete = "delete from ".$entityParam->getDbname().' WHERE ';
		
		list($hql, $params) = OrmCore::_getHqlExample($listeField, $criterias, $condition);
		
		$queryExample = $delete.$hql;
										
		//Execution
		$result = OrmDb::execute($queryExample,
									$params,
									"Database error during OrmCore::deleteByExample(OrmEntity &{$entityParam->getName()}, , OrmExample \$example)");
		  }
		  
	/**
	 * Inner function to factorize the custruction of the HQL for the *byExample functions
	 * @param : $listeField the list of Fields
	 * @param : $criterias the list of Criteria
	 *
	 * @return : List[$hql, $params]
	 **/
	private static function _getHqlExample($listeField, $criterias, $condition){
		
		if( $condition == 'AND' ){
			$hql = " 1 ";
		} else {
			$hql = " 0 ";
		  }

		$params = array();

		foreach($criterias as $criteria) {

			if($listeField[$criteria->fieldname] == null) {
				throw new Exception("Field '".$criteria->fieldname."' not defined in entity '".$entityParam->getName()."' while you're searching on it");
			}
		  $filterType = $listeField[$criteria->fieldname]->getType();
		  
			//1 parameter
		  if($criteria->typeCriteria == OrmTypeCriteria::$EQ || $criteria->typeCriteria == OrmTypeCriteria::$NEQ 
			|| $criteria->typeCriteria == OrmTypeCriteria::$GT || $criteria->typeCriteria == OrmTypeCriteria::$GTE 
			|| $criteria->typeCriteria == OrmTypeCriteria::$LT || $criteria->typeCriteria == OrmTypeCriteria::$LTE 
			|| $criteria->typeCriteria == OrmTypeCriteria::$BEFORE || $criteria->typeCriteria == OrmTypeCriteria::$AFTER
				|| $criteria->typeCriteria == OrmTypeCriteria::$LIKE || $criteria->typeCriteria == OrmTypeCriteria::$NLIKE) {
				$val = $criteria->paramsCriteria[0];
				
				$params[] = OrmCore::_fieldToDBValue($val, $filterType);
				$hql .= " {$condition} ".$criteria->fieldname.$criteria->typeCriteria.' ? ';
			continue;
		  }
		  
			//0 parameter
			if($criteria->typeCriteria == OrmTypeCriteria::$NULL || $criteria->typeCriteria == OrmTypeCriteria::$NNULL) {
				$hql .= " {$condition} ".$criteria->fieldname.$criteria->typeCriteria;
			continue;
		  }
		  
			//2 parameters
			if($criteria->typeCriteria == OrmTypeCriteria::$BETWEEN) {
			$params[] = OrmCore::_fieldToDBValue($criteria->paramsCriteria[0], $filterType); 
			$params[] = OrmCore::_fieldToDBValue($criteria->paramsCriteria[1], $filterType); 
				$hql .= " {$condition} ".$criteria->fieldname.$criteria->typeCriteria.' ? AND ?';
			continue;
		  }
				
			// N parameters
			if($criteria->typeCriteria == OrmTypeCriteria::$IN || $criteria->typeCriteria == OrmTypeCriteria::$NIN) {
				if(is_array($criteria->paramsCriteria) && count($criteria->paramsCriteria) > 0) {
						$hql .= " {$condition} ";
						$hql .= $criteria->fieldname.' '.sprintf($criteria->typeCriteria, implode(',', array_fill(0, count($criteria->paramsCriteria), '?'))).' ';
						foreach($criteria->paramsCriteria as $param) {
						$params[] = OrmCore::_fieldToDBValue($param, $filterType); 
						}
				} else if(is_array($criteria->paramsCriteria) && count($criteria->paramsCriteria) == 0) {
					$hql .= 'AND false '; // no value passed, so no result to be returned
				}
				continue;
			}
						
			//Other cases
			if($criteria->typeCriteria == OrmTypeCriteria::$EMPTY) {
				$hql .= " {$condition} ( ".$criteria->fieldname .' is null || ' . $criteria->fieldname . "= '')";
				continue;
					}
			if($criteria->typeCriteria == OrmTypeCriteria::$NEMPTY) {
				$hql .= " {$condition} ( ".$criteria->fieldname .' is not null && ' . $criteria->fieldname . "!= '')";
					continue;
				}                        
		  
			throw new Exception("The OrmCriteria $criteria->typeCriteria is not manage");
		}
										
		return array($hql, $params);
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
			$newEntity->set($field->getName(),OrmCore::_dbValueToField($row[$field->getName()], $field->getType()));
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
	private static final function _fieldToDBValue($data, $type) {
		if(is_null($data)){
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
	private static final function _dbValueToField($data, $type) {
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
	public static final function verifIntegrity(OrmEntity &$entityParam, $sid) {
		$listeEntitys = MyAutoload::getAllInstances($entityParam->getModuleName());

		foreach($listeEntitys as $key=>$anEntity) {
		  if($anEntity instanceOf OrmEntityAssociation)
			continue;
			
		  foreach($anEntity->getFields() as $field) {
			if($field->isAssociateKEY()) {
			  continue;
			}

			
			if(is_array($field->getKEYName())){

			}
			if($field->getKEYName() != null) {
			  $vals = explode('.',$field->getKEYName(),2);
			  
			  if(strtolower ($vals[0]) == strtolower ($entityParam->getName()))  {
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
