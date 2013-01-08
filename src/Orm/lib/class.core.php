<?php
/**
 * Contains the Core Class
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
	protected static final function getFieldsToHql(Entity &$entity)
	{    
		$hql = '';

		$listeField = $entity->getFields();

		//Pour chaque champs contenu dans l'entité
		foreach($listeField as $field) {
			//On ne cree pas les champs qui sont des liaisons externes sur des tables associatives
			if($field->isAssociateKEY()) {
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

				case CAST::$TS : $hql .= 'I (10) '; break; //workaround for the real timestamp missing in ADODBLITE
			}

			if($field->isPrimaryKEY())
			{
				$hql .= ' KEY ';
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
	public static final function createTable(Entity &$entityParam)
	{
		$db = cmsms()->GetDb();
		$taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
		$dict = NewDataDictionary( $db );
		$hql = Core::getFieldsToHql($entityParam);

		//Appel aux méthodes de l'API de adodb pour créer effectivement la table.
		$sqlarray = $dict->CreateTableSQL($entityParam->getDbname(), 
												$hql,
												$taboptarray);
												
		$result = $dict->ExecuteSQLArray($sqlarray);

		if ($result === false)
		{
			Trace::error($hql.'<br/>');
			Trace::error("Database error durant durant la creation de la table pour l'entité " . $entityParam->getName().$db->ErrorMsg());
			throw new Exception("Database error durant durant la creation de la table pour l'entité " . $entityParam->getName().$db->ErrorMsg());
		}
		   
		Trace::debug("createTable : ".print_r($sqlarray, true).'<br/>');

		//Optionnel : créera une séquence associee
		if($entityParam->getSeqname() != null){$db->CreateSequence($entityParam->getSeqname());}

		//On initialise la table.
		$entityParam->initTable();
	}
    
    /**
    * Drop the table for the Entity in parameters
    *  Will also drop the sequence if it's needed
    * 
    * @param Entity an instance of the entity
    */
	public static final function dropTable(Entity &$entityParam)
	{

		$db = cmsms()->GetDb();

		$dict = NewDataDictionary( $db );

		$sqlarray = $dict->DropTableSQL($entityParam->getDbname());
		$dict->ExecuteSQLArray($sqlarray);

		//Optionnel : supprimera une sequence associee
		if($entityParam->getSeqname() != null){$db->DropSequence($entityParam->getSeqname());}
	}  
  
    /**
    * Will modifie the table of the Entity in parameters with the SQL query in parameters
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
	public static final function alterTable(Entity &$entityParam, $sql)
	{
		$db = cmsms()->GetDb();
			
		$queryAlter = "ALTER TABLE ".$entityParam->getDbname()." ".$sql;    
		$result = $db->Execute($queryAlter);
		if ($result === false){die("Database error durant l'alter de la table $entityParam->getDbname()!");}
	}
    
    /**
     * Insert data into database.
     * 
     * Example for a new Customer : customer_id, name, lastName (optionnal) 
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
	public static final function insertEntity(Entity &$entityParam)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
		$listeValues = $entityParam->getValues();

		$queryInsert = 'INSERT INTO '.$entityParam->getDbname().' (%s) values (%s)';

		$str1 = "";
		$str2 = "";

		$params = array();
			   
		//On verifie que toutes les valeurs necessaires sont transmises
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
			Indexing::AddWords($entityParam->getModuleName(), Core::SelectById($entityParam,$arrayKEY[0]));
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
     *       $customer = Core::selectById($customer, 1);
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
	public static final function updateEntity(Entity &$entityParam)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
		$values = $entityParam->getValues();

		$str = "";
		$where = '';
		$params = array();
		$hasKey = false;
		  
		//On verifie que toutes les valeurs necessaires sont transmises
		foreach($listeField as $field) {
		
			//If it's not set
			if(empty($values[$field->getName()])) {
			
				//If it's a primaryKey we throw a exception
				if($field->isPrimaryKEY()) {
					throw new IllegalArgumentException('the primaryKey '.$field->getName().' is missing for the entity : '.$entityParam->getName());
				}
				
				//an empty associatif field : no problem, we can pass
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


		//Excecution
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
	public static final function deleteByIds(Entity &$entityParam, $ids)
	{

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

		//Excecution
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
	public static final function countAll(Entity &$entityParam)
	{

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
	public static final function selectAll(Entity &$entityParam)
	{

		$db = cmsms()->GetDb();

		$querySelect = 'Select * FROM '.$entityParam->getDbname();

			//Si déjà présent en cache, on le retourne 
		if(Cache::isCache($querySelect))
		{
		  return Cache::getCache($querySelect);
		}
		  
		$result = $db->Execute($querySelect);
		if ($result === false){die("Database error durant la requete par Ids!");}

		$entitys = array();
		while ($row = $result->FetchRow())
		{
		  $entitys[] = Core::rowToEntity($entityParam, $row);
		}

			//On repousse dans le cache le résultat avant de le retourner   
		Cache::setCache($querySelect, null, $entitys);

		return $entitys;
	}
  
    /**
    * Return a Entity from its Id
    * 
    * @param Entity an instance of the entity  
    * @param int the Id to find
    * @return Entity the Entity found or NULL
    */
	public static final function selectById(Entity &$entityParam,$id)
	{
		$liste = Core::selectByIds($entityParam, array($id));
			
			if(!isset($liste[0]))
				return null;
			
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
	public static final function selectByIds(Entity &$entityParam, $ids)
	{
		if(count($ids) == 0)
		  return array();
			

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();

		$where = "";
			
		foreach($listeField as $field)
		{

		  if(!$field->isPrimaryKEY())
		  { 
			continue;
		  }
		  
		  foreach($ids as $id)
		  {
		  
			if(!empty($where))
			{
			  $where .= ' OR ';
			}
				  
			$where .= $field->getName().' = ?';
			
			$params[] = Core::FieldToDBValue($id, $field->getType());
		  }
		}

		$querySelect = 'Select * FROM '.$entityParam->getDbname().' WHERE '.$where;

			//Si déjà présent en cache, on le retourne
		if(Cache::isCache($querySelect,$params))
		{
		  return Cache::getCache($querySelect,$params);
		}

		//Excecution
		$result = $db->Execute($querySelect, $params);
		if ($result === false){die("Database error durant la requete par Ids!");}

		$entitys = array();
		while ($row = $result->FetchRow())
		{
		  $entitys[] = Core::rowToEntity($entityParam, $row);
		}
			
			//On repousse dans le cache le résultat avant de le retourner
		Cache::setCache($querySelect,$params, $entitys);
			
		return $entitys;

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
     *       Core::selectByExample($customer, $example);
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
     *       Core::selectByExample($customer, $example);
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
	public static final function selectByExample(Entity &$entityParam, Example $example)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();

		$criterias = $example->getCriterias();
		$select = "select * from ".$entityParam->getDbname();
		$hql = "";
		$params = array();
		//  die("spp,".count($criterias));
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

		  $filterType =  $listeField[$criteria->fieldname]->getType();
		  
				//Critéres avec 1 seul paramètre
		  if($criteria->typeCriteria == TypeCriteria::$EQ || $criteria->typeCriteria == TypeCriteria::$NEQ 
			|| $criteria->typeCriteria == TypeCriteria::$GT || $criteria->typeCriteria == TypeCriteria::$GTE 
			|| $criteria->typeCriteria == TypeCriteria::$LT || $criteria->typeCriteria == TypeCriteria::$LTE 
			|| $criteria->typeCriteria == TypeCriteria::$BEFORE || $criteria->typeCriteria == TypeCriteria::$AFTER
			|| $criteria->typeCriteria == TypeCriteria::$LIKE || $criteria->typeCriteria == TypeCriteria::$NLIKE)
		  {  
			$val = $criteria->paramsCriteria[0];
			
			if($criteria->typeCriteria == TypeCriteria::$LIKE || $criteria->typeCriteria == TypeCriteria::$NLIKE)
			{
			  $val.= '%';
			}
			
			$params[] = Core::FieldToDBValue($val, $filterType); 
			$hql .= $criteria->fieldname.$criteria->typeCriteria.' ? ';
			continue;
		  }
		  
				//Sans paramètres
		  if($criteria->typeCriteria == TypeCriteria::$NULL || $criteria->typeCriteria == TypeCriteria::$NNULL)
		  {  
			$hql .= $criteria->fieldname.$criteria->typeCriteria;
			continue;
		  }
		  
				//deux paramètres
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
		  
		  //Traitement spécifique
		  if($criteria->typeCriteria == TypeCriteria::$EMPTY)
		  {
			$hql .= ' ( '.$criteria->fieldname .' is null || ' . $criteria->fieldname . "= '')";
			continue;
		  }
		  if($criteria->typeCriteria == TypeCriteria::$NEMPTY)
		  {
			$hql .= ' ( '.$criteria->fieldname .' is not null && ' . $criteria->fieldname . "!= '')";
			continue;
		  }
						 
		  throw new Exception("Le Criteria $criteria->typeCriteria n'est pas encore pris en charge");
		}
		$queryExample = $select.$hql;

		Trace::info("SelectByExample : ".$queryExample."   ".print_r($params, true));

		$result = $db->Execute($queryExample, $params);

		if ($result === false){die($db->ErrorMsg().Trace::error("Database error durant la requete par Example!"));}

		Trace::info("SelectByExample : ".$result->RecordCount()." resultat(s)");

		$entitys = array();
		while ($row = $result->FetchRow())
		{
		  $entitys[] = Core::rowToEntity($entityParam, $row);
		}

		return $entitys;

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
	public static final function deleteByExample(Entity &$entityParam, Example $Example)
	{

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
	public static final function rowToEntity (Entity &$entityParam, $row)
	{

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
	protected static final function FieldToDBValue($data, $type)
	{
		if($data == null){
			return null;
		}
		
		switch($type)
		{
		  case CAST::$STRING : return $data;
		  
		  case CAST::$INTEGER : return $data;
		  
		  case CAST::$NUMERIC : return $data;
		  
		  case CAST::$BUFFER : return $data;
		  
		  case CAST::$DATE : return cmsms()->GetDb()->DBDate($data);       
		  
		  case CAST::$TIME : return cmsms()->GetDb()->DBDate($data);     

		  case CAST::$TS : return $data;  
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
	protected static final function dbValueToField($data, $type)
	{
		switch($type)
		{
		  case CAST::$STRING : return $data;
		  
		  case CAST::$INTEGER : return $data;
		  
		  case CAST::$NUMERIC : return $data;
		  
		  case CAST::$BUFFER : return $data;
		  
		  case CAST::$DATE : return cmsms()->GetDb()->UnixTimeStamp($data);
		  
		  case CAST::$TIME : return cmsms()->GetDb()->UnixTimeStamp($data);

		  case CAST::$TS : return $data;

		}
	}
    
    /**
     * Return the entities 'B' which could be associate to an Entity 'A' (but currently are not associate)
     * 
     * Example : Which Tag can i associate to my Blog that are not already linked ?
     * 
     * For memory, an correct AssociateKey system in Orm is 3 Entities like that : 
 	 *  1 blog can be linked to 0/n Tag
 	 *  1 tag can ben linked to 0/n Blog 
	 *  So we've got : Blog (Entity) <-> Blog2Tag (EntityAssociation) <-> Tag (Entity) 
     * 
     * <code>
     *    class Blog extends Entity
     *    {
     *        public function __construct()
     *        {
     *            parent::__construct('myModule','blog');
     *            
     *             $this->add(new Field('blog_id' 
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null 
     * 						, KEY::$PK
	 *						));
     *             $this->add(new Field('tags' 
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null
     *                       , KEY::$AK    
     *                       , 'Blog2Tag.tag_id' 	<-- link to the associate Entity : 'Blog2Tag'  with its property 'tag_id'
	 *						));
     *        }
     *    }
     * 
     *    class Tag extends Entity
     *    {
     *       public function __construct()
     *        {
     *            parent::__construct('myModule','tag');
     *            
     *             $this->add(new Field('tag_id' 
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null 
     * 						, KEY::$PK
	 *						));
     *             $this->add(new Field('blogs' 
     *                        , CAST::$INTEGER
     *                        , null
     *                        , null
     *                        , KEY::$AK    
     *                        , 'Blog2Tag.blog_id' <-- link to the associate Entity : 'Blog2Tag'  with its property 'blog_id'
	 *						));
     *        }
     *        
     *
     *    }
     * 
     *   class Blog2Tag extends EntityAssociation
     *   {
     *        public function __construct()
     *        {
     *            parent::__construct('myModule','blog2tag');
     *            
     *            $this->add(new Field('blog_id'
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null
     *                       , KEY::$FK
     *                       , 'Blog.tags'
	 *						));
     *            $this->add(new Field('tag_id'        
     *                        , CAST::$INTEGER
     *                        , null
     *                        , null
     *                        , KEY::$FK
     *                        , 'Tag.articles' 
	 *						));
     *
     *        }    
     *    }
     * </code>
	 *
	 * And now the code to find the potentials Tag for my Blog : 
	 *
	 * <code>
	 *   $blog = MyAutoload::getInstance($this->GetName(), 'blog');
	 *   $tags = Core::getEntitysAssociable($blog,'tags');
	 * </code>
     * 
     * @param Entity an instance of the entity 
     * @param string the field's name of the Entity which will be used to start the research
	 *
     * @return array<Entity> a list of the Entities linked to the entity in the parameters by the fieldName in the Parameters
     */
	public static final function getEntitysAssociable(Entity &$entityParam,$fieldname)
	{
		$field = $entityParam->getFieldByName($fieldname);
		if($field->getKEYName() == '')
			throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
			
		$cle = explode('.',$field->getKEYName(),2);

		eval('$entity = new '.$cle[0].'();');
										   

		$listField = $entity->getFields();
		foreach($listField as $field)
		{
		  if($field->getKEYName() == '')
			throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
				
		  $cle = explode('.',$field->getKEYName(),2);
		  
		  if(strtolower($cle[0]) == $entityParam->getName())
			continue;
			  
		  
		  //Evaluation de la eclass en cours
		  eval('$entity = new '.$cle[0].'();');
		  
		  $liste = Core::selectAll($entity);
		  
		  return $liste;
		} 
	}

    /**
     * Return the entities 'B' which are already associate to an Entity 'A' 
     * 
     * Example : Which Tag are already associate to my Blog ?
     * 
     * For memory, an correct AssociateKey system in Orm is 3 Entities like that : 
 	 *  1 blog can be linked to 0/n Tag
 	 *  1 tag can ben linked to 0/n Blog 
	 *  So we've got : Blog (Entity) <-> Blog2Tag (EntityAssociation) <-> Tag (Entity) 
     * 
     * <code>
     *    class Blog extends Entity
     *    {
     *        public function __construct()
     *        {
     *            parent::__construct('myModule','blog');
     *            
     *             $this->add(new Field('blog_id' 
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null 
     * 						, KEY::$PK
	 *						));
     *             $this->add(new Field('tags' 
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null
     *                       , KEY::$AK    
     *                       , 'Blog2Tag.tag_id' 	<-- link to the associate Entity : 'Blog2Tag'  with its property 'tag_id'
	 *						));
     *        }
     *    }
     * 
     *    class Tag extends Entity
     *    {
     *       public function __construct()
     *        {
     *            parent::__construct('myModule','tag');
     *            
     *             $this->add(new Field('tag_id' 
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null 
     * 						, KEY::$PK
	 *						));
     *             $this->add(new Field('blogs' 
     *                        , CAST::$INTEGER
     *                        , null
     *                        , null
     *                        , KEY::$AK    
     *                        , 'Blog2Tag.blog_id' <-- link to the associate Entity : 'Blog2Tag'  with its property 'blog_id'
	 *						));
     *        }
     *        
     *
     *    }
     * 
     *   class Blog2Tag extends EntityAssociation
     *   {
     *        public function __construct()
     *        {
     *            parent::__construct('myModule','blog2tag');
     *            
     *            $this->add(new Field('blog_id'
     *                       , CAST::$INTEGER
     *                       , null
     *                       , null
     *                       , KEY::$FK
     *                       , 'Blog.tags'
	 *						));
     *            $this->add(new Field('tag_id'        
     *                        , CAST::$INTEGER
     *                        , null
     *                        , null
     *                        , KEY::$FK
     *                        , 'Tag.articles' 
	 *						));
     *
     *        }    
     *    }
     * </code>
	 *
	 * And now the code to find the Tag already linked with my Blog #45: 
	 *
	 * <code>
	 *   $blog = MyAutoload::getInstance($this->GetName(), 'blog');
	 *   $tags = Core::getEntitysAssocieesLiees($blog,'tags', 45);
	 * </code>
     * 
     * @param Entity an instance of the entity 
     * @param string the field's name of the Entity which will be used to start the research
	 * @param mixed entityId the id of the Blog to start the research
	 *
     * @return array<Entity> a list of the Entities linked to the entity in the parameters by the fieldName in the Parameters
     */
	public static final function getEntitysAssocieesLiees(Entity &$entityParam, $fieldname, $entityId)
	{
		Trace::debug("getEntitysAssocieesLiees : ".$entityParam->getName()." ".$fieldname." ".$entityId);

		$field = $entityParam->getFieldByName($fieldname);

		if($field->getKEYName() == '')
			throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
		  
		$cle = explode('.',$field->getKEYName(),2);

		eval('$entity = new '.$cle[0].'();');
																  
		$example = new Example();    
		$example->addCriteria($cle[1],TypeCriteria::$EQ,array($entityId));
		$assocs = Core::selectByExample($entity, $example);

		$listField = $entity->getFields();
		foreach($listField as $field)
		{
		  if($field->getKEYName() == null || $field->getKEYName() == '')
			throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
		  
		  $cle = explode('.',$field->getKEYName(),2);
		  
		  if(strtolower($cle[0]) == $entityParam->getName())
			continue;                              
					
		  $ids = array();
		  foreach($assocs as $assoc)
		  {
			$ids[] = $assoc->get($field->getName());
		  }                                                        
		  
		  $cle = explode('.',$field->getKEYName(),2);
				
		  //Evaluation de la eclass en cours
		  eval('$entity = new '.$cle[0].'();');
		  
		  $liste = Core::selectByIds($entity, $ids);
		  
		  Trace::debug("getEntitysAssocieesLiees : "."resultat : ".count($liste));
		  
		  return $liste;
		}    
	}
  
    /**
     * Verify in all type of entities if anyone still has a link with the Entity passed in parameters (ForeignKEy and AssociateKey)
     * 
     *  This function is used by the delete* functions to avoid orphelins data in database
     * 
	 * @param Orm the module which extends the Orm module                                      
     * @param Entity an instance of the entity
     * @param mixed the id of the Entity to verify
	 *
	 * @return a message if a link is still present. nothing if the integrity is ok
     */
	public static final function verifIntegrity(Entity &$entity, $sid)
	{
		$listeEntitys = MyAutoload::getAllInstances($entity->getModuleName());

		foreach($listeEntitys as $key=>$anEntity)
		{
		  if($anEntity instanceOf EntityAssociation)
			continue;
			
		  foreach($anEntity->getFields() as $field)
		  {
			if($field->isAssociateKEY())
			{
			  continue;
			}
			
			if($field->getKEYName() != null)
			{
			  $vals = explode('.',$field->getKEYName(),2);
			  
			  if(strtolower ($vals[0]) == strtolower ($entity->getName()))
			  {
				$Example = new Example();
				$Example->addCriteria($field->getName(), TypeCriteria::$EQ, array($sid));
				$entitys = Core::selectByExample($anEntity, $Example);
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
     *   A Customer has a link to an Adress (Customer.adresse_id)
     *   An Adress has a link to a city (Adress.city_id)
	 *   A city has a ZipCode (maybe shared by differents cities)
     * 
     *  If i want the Orders for Customers from the city with zipcode equals to "01234" or "4567" I could write some shitty code !
     * 
     * <code>
     *  $cities = //Find my cities with ZipCode "01234" or "4567"
     *  foreach($cities as $city)
     *  {
     *       $adresses = //Find the adress for the city $city
     *       foreach($adresses as $adress)
     *       {
     *           $customers = //Find the Customers for the adress $adress
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
     *   $orders = Core::makeDeepSearch(order, 'Order.customer_id.adress_id.city_id.zipcode', array('01234', '4567'));
     * </code>
     * 
     * @param Entity The entity i want to have at the end
     * @param string the path to fallow. Must be ended with the name of the Field to make the comparaison
     * @param array the array of value to make the comparaison
     * 
     */
	public static final function makeDeepSearch(Entity $previousEntity, $cle, $values)
	{    
		TRACE::info("# : "."Start makeDeepSearch() ".$previousEntity->getName()."->".$cle);

		if($previousEntity == null)
		{
		  
		  $newCle = explode('.',$cle,2);
		  $previousEntity = $newCle[0];
		  $cle = $newCle[1];
		  eval('$previousEntity = new '.$previousEntity.'();');
		}

		$newCle = explode('.',$cle,2);
		$fieldname = $newCle[0];

		//Test de sortie : on a un seul résultat dans $newCle : le champs final
		if(count($newCle) == 1)
		{
		  TRACE::info("# : "." count(\$newCle) == 1 , donc sortie ");
		  $Example = new Example;
		  $Example->addCriteria($fieldname, TypeCriteria::$IN, $values);
		  $entitys = Core::selectByExample($previousEntity, $Example);
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
		  eval('$nextEntity = new '.$foreignKEY[0].'();');
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
		$entitys = Core::selectByExample($previousEntity, $Example);

		return $entitys;
	}
  
}

?>
