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
  public static final function getFieldsToHql(Entity &$entity)
  {    
    $hql = '';
    
    $listeField = $entity->getFields();
    
        //Pour chaque champs contenu dans l'entité
    foreach($listeField as $field)
    {
      //On ne cree pas les champs qui sont des liaisons externes sur des tables associatives
      if($field->isAssociateKEY())
        continue;
    
      if(!empty($hql))
      {
        $hql .= ' , ';
      }
      
      $hql .= ' '.$field->getName().' ';
      
      switch($field->getType())
      {
        case CAST::$STRING : $hql .= 'C'; 
          if($field->getSize() != "" )
          {$hql.= " (".$field->getSize().") ";} break;
        
        case CAST::$INTEGER : $hql .= 'I'; 
          if($field->getSize() != "" )
          {$hql.= " (".$field->getSize().") ";} break;
        
        case CAST::$NUMERIC : $hql .= 'N'; 
          if($field->getSize() != "" )
          {$hql.= " (".$field->getSize().") ";} break;
        
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
    
        //Trace de débug
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
    *   $customer = MyAutoload.getInstance($this->GetName(), 'customer');
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
    *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
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
    * Insert data into database. The third parameter must follow this scheme
    * 
    * Example for 3 new Customers : customer_id, name, lastName (optionnal) 
    *   
    * 
    * <code>
    *       $myArray = array();
    *       $myArray[] = array('lastName'=>'', 'name'=>'Smith');
    *       $myArray[] = array('name'=>'Durant');
    *       $myArray[] = array('lastName'=>'John', 'name'=>'Doe');
    * 
    *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
    * 
    *       Core::insertEntity($this, $customer, $myArray);
    * </code>
    * 
    *  Important : you must not set the primaryKey value. It will be calculate by the system it-self
    *  
	* @param Orm the module which extends the Orm module                                      
    * @param Entity an instance of the entity
    * @param array the array with all the values in differents associative array
	*
    * @return array the list of the new Ids (customer_id in my example)
    */
	public static final function insertEntity(Orm &$module, Entity &$entityParam, $rows)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();
					
		$sqlReady = false;

		//Tableau de retour contiendra les clés crées
		$arrayKEY = array();

		$queryInsert = 'INSERT INTO '.$entityParam->getDbname().' (%s) values (%s)';

		$str1 = "";
		$str2 = "";
		foreach($listeField as $field)
		{

		  if($field->isAssociateKEY())
			continue;

		  if(!empty($str1))
		  {
			$str1 .= ',';
			$str2 .= ',';
		  }
		  $str1 .= ' '.$field->getName().' ';
		  $str2 .= '?';
		}

		foreach($rows as $row)
		{
		  $params = array();
					   
		  //On verifie que toutes les valeurs necessaires sont transmises
		  foreach($listeField as $field)
		  {
			if($field->isAssociateKEY())
			  continue;
			
			//Champs vide mais pas une cle automatique et ni un champs spécial
			if(!$field->isPrimaryKEY() 
			  && !$field->isNullable() 
			  && (!isset($row[$field->getName()]) || (empty($row[$field->getName()]) && $row[$field->getName()] !== 0))
			  && !($field instanceof Field_SPE))
			{
			  throw new Exception('la valeur du champs '.$field->getName().' de la classe '.$entityParam->getName().' est manquante');
			}

			 //On génère une nouvelle cléf pour toutes les clé primaire
			if($field->isPrimaryKEY() && empty($row[$field->getName()]))
			{
			  //Nouvelle cle
			  $row[$field->getName()] = $db->GenID($entityParam->getSeqname());
			  $arrayKEY[] = $row[$field->getName()];
			}
			
			$val = null;
			if(isset($row[$field->getName()]))
			{
			  $val = $row[$field->getName()];
			}

			$params[] = Core::FieldToDBValue($val, $field->getType());
			
		  }
		  
		  $sqlReady = true;
		  
		  //Exécution
		  $db->debug = true;
		  
			Trace::debug("insertEntity : ".sprintf($queryInsert, $str1, $str2));
		  
		  $result = $db->Execute(sprintf($queryInsert, $str1, $str2), $params);
		  if ($result === false)
		  {
			Trace::error(print_r($params, true).'<br/>');
			Trace::error(sprintf($queryInsert, $str1, $str2).'<br/>');
			Trace::error("Database error durant l'insert!".$db->ErrorMsg());
			throw new Exception("Database error durant l'insert!".$db->ErrorMsg());
		  }
		  
		  if($entityParam->isIndexable())
		  {  
			// $modops = cmsms()->GetModuleOperations();
			// Indexing::setSearch($modops->GetSearchModule());
			Indexing::AddWords($module->getName(), Core::SelectById($entityParam,$arrayKEY[0]));
		  }
		}

		return $arrayKEY;

	}
   
    /**
    *  Update data into database. The third parameter must follow this scheme
    * 
    * Example for 3 new Customers : customer_id, name, lastName (optionnal) 
    * 
    * <code>
    *       $myArray = array();
    *       $myArray[] = array('customer_id'=>1, 'lastName'=>null, 'name'=>'Dupont');    <-- update Name value, erase lastName value in database
    *       $myArray[] = array('customer_id'=>2, 'name'=>'Durant');                      <-- update Name value
    *       $myArray[] = array('customer_id'=>3, 'lastName'=>'John', 'name'=>'Doe');     <-- update Name value and lastName value
    * 
    *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
    * 
    *       Core::updateEntity($this, $customer, $myArray);
    * </code>
    * 
	* @param Orm the module which extends the Orm module                                      
    * @param Entity an instance of the entity
    * @param array the array with all the values in differents associative array
    */
	public static final function updateEntity(Orm $module, Entity &$entityParam, array $rows)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();


		foreach($rows as $row)
		{
		  $str = "";
		  $where = '';
		  $params = array();
		  
		  //Nettoyage des eventuelles valeurs pourries transmises
		  foreach($row as $KEY=>$value)
		  {
			if(empty($listeField[$KEY]))
			{
			  unset($row[$KEY]);
			}
		  }      
		  
		  $hasKEY = false;
		  //On verifie que toutes les valeurs necessaires sont transmises
		  foreach($listeField as $field)
		  {
			//Si le champs vide est une cle : erreur
			if($field->isPrimaryKEY())
			{
			  if(empty($row[$field->getName()]))
			  {
				throw new Exception('l\'id n\'est pas fournie : '.$field->getName());
			  } 
			  
			  $where = ' WHERE '.$field->getName().' = ?';
			  $hasKEY = true;
			  $KEY = $row[$field->getName()];
			  
			}
			
			//Si n'est pas définit dans les lignes à mettre à jour, on ne met simplement pas à jour
			if(!isset($row[$field->getName()]))
			{
			  continue;
			}
			
			if(empty($row[$field->getName()]) && $field->isNullable())
			{
						//Nothing to do
			}
			
			//Champs associatif : on passe
			if(empty($row[$field->getName()]) && $field->isAssociateKEY())
			{
			  continue;
			}
			
			if(!empty($str))
			{
			  $str .= ',';
			}
			
			$str .= ' '.$field->getName().' = ? ';
			
			$params[] = Core::FieldToDBValue($row[$field->getName()], $field->getType());
			
		  }
		  
		  if($hasKEY)
		  {
			$params[] = $KEY;
		  }
		  

		  $queryUpdate = 'UPDATE '.$entityParam->getDbname().' SET '.$str.$where;

		  
		  //Excecution
		  $result = $db->Execute($queryUpdate, $params);
		  if ($result === false){die("Database error durant l'update!");}
		  if($entityParam->isIndexable())
		  {  
			Indexing::UpdateWords($module->getName(), Core::SelectById($entityParam,$KEY));
		  }
		}
	}
  
    /**
    * Delete information into database
    *   
    * Example for a single deletion : 
    * 
    * <code>
    *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
    * 
    *       Core::deleteByIds($this, $customer, array(1);
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
    *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
    * 
    *       Core::deleteByIds($this,$customer, $myArray);
    * </code>
    * 
	* @param Orm the module which extends the Orm module                                      
    * @param Entity an instance of the entity    
    * @param array all the ids to delete ($customer_id in my example)
    */
	public static final function deleteByIds(Orm $module, Entity &$entityParam, $ids)
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
			Indexing::DeleteWords($module->getName(), $entityParam, $sid);
		  }
		  
		}
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
     *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
     * 
     *       $exemple = new Exemple();
     *       $exemple->addCritere('lastName', TypeCritere::$EQ, array('roger'), true);
     * 
     *       Core::selectByExemple($customer, $exemple);
     * </code>
     * 
     *  Example : find the customers with Id >= 90
     * 
     * <code>
     *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
     * 
     *       $exemple = new Exemple();
     *       $exemple->addCritere('customer_id', TypeCritere::$GTE, array(90));
     * 
     *       Core::selectByExemple($customer, $exemple);
     * </code>
     * 
     * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals
     * 
     * NOTE 2 : you can add as many Criterias as you want in an Example Object
     * 
     * @param Entity an instance of the entity
     * @param Exemple the Object Exemple with some Criterias inside
     * 
     * @see Exemple
     * @see TypeCritere
     */
	public static final function selectByExemple(Entity &$entityParam, Exemple $exemple)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();

		$criteres = $exemple->getCriteres();
		$select = "select * from ".$entityParam->getDbname();
		$hql = "";
		$params = array();
		//  die("spp,".count($criteres));
		foreach($criteres as $critere)
		{
		  if(!empty($hql))
		  {
			$hql .= ' AND ';
		  }
		  
		  if(empty($hql))
		  {
			$hql .= ' WHERE ';
		  }

		  $filterType =  $listeField[$critere->fieldname]->getType();
		  
				//Critéres avec 1 seul paramètre
		  if($critere->typeCritere == TypeCritere::$EQ || $critere->typeCritere == TypeCritere::$NEQ 
			|| $critere->typeCritere == TypeCritere::$GT || $critere->typeCritere == TypeCritere::$GTE 
			|| $critere->typeCritere == TypeCritere::$LT || $critere->typeCritere == TypeCritere::$LTE 
			|| $critere->typeCritere == TypeCritere::$BEFORE || $critere->typeCritere == TypeCritere::$AFTER
			|| $critere->typeCritere == TypeCritere::$LIKE || $critere->typeCritere == TypeCritere::$NLIKE)
		  {  
			$val = $critere->paramsCritere[0];
			
			if($critere->typeCritere == TypeCritere::$LIKE || $critere->typeCritere == TypeCritere::$NLIKE)
			{
			  $val.= '%';
			}
			
			$params[] = Core::FieldToDBValue($val, $filterType); 
			$hql .= $critere->fieldname.$critere->typeCritere.' ? ';
			continue;
		  }
		  
				//Sans paramètres
		  if($critere->typeCritere == TypeCritere::$NULL || $critere->typeCritere == TypeCritere::$NNULL)
		  {  
			$hql .= $critere->fieldname.$critere->typeCritere;
			continue;
		  }
		  
				//deux paramètres
		  if($critere->typeCritere == TypeCritere::$BETWEEN)
		  {  
			$params[] = Core::FieldToDBValue($critere->paramsCritere[0], $filterType); 
			$params[] = Core::FieldToDBValue($critere->paramsCritere[1], $filterType); 
			$hql .= $critere->fieldname.$critere->typeCritere.' ? AND ?';
			continue;
		  }
		  
				// N paramètres
		  if($critere->typeCritere == TypeCritere::$IN || $critere->typeCritere == TypeCritere::$NIN)
		  {
			$hql .= ' ( ';
			$second = false; 
			foreach($critere->paramsCritere as $param)
			{
			  if($second)
			  {
				$hql .= ' OR ';
			  }
			  
			  $params[] = Core::FieldToDBValue($param, $filterType); 
			  $hql .= $critere->fieldname.TypeCritere::$EQ.' ? ';
			  
			  $second = true;
			}
			$hql .= ' )';
			continue;
		  }
		  
		  //Traitement spécifique
		  if($critere->typeCritere == TypeCritere::$EMPTY)
		  {
			$hql .= ' ( '.$critere->fieldname .' is null || ' . $critere->fieldname . "= '')";
			continue;
		  }
		  if($critere->typeCritere == TypeCritere::$NEMPTY)
		  {
			$hql .= ' ( '.$critere->fieldname .' is not null && ' . $critere->fieldname . "!= '')";
			continue;
		  }
						 
		  throw new Exception("Le Critere $critere->typeCritere n'est pas encore pris en charge");
		}
		$queryExemple = $select.$hql;

		Trace::info("SelectByExemple : ".$queryExemple."   ".print_r($params, true));

		$result = $db->Execute($queryExemple, $params);

		if ($result === false){die($db->ErrorMsg().Trace::error("Database error durant la requete par Exemple!"));}

		Trace::info("SelectByExemple : ".$result->RecordCount()." resultat(s)");

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
     *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
     * 
     *       $exemple = new Exemple();
     *       $exemple->addCritere('lastName', TypeCritere::$EQ, array('roger'), true);
     * 
     *       Core::deleteByExemple($customer, $exemple);
     * </code>
     * 
     *  Example : delete the customers with Id >= 90
     * 
     * <code>
     *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
     * 
     *       $exemple = new Exemple();
     *       $exemple->addCritere('customer_id', TypeCritere::$GTE, array(90));
     * 
     *       Core::deleteByExemple($customer, $exemple);
     * </code>
     * 
     * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals
     * 
     * NOTE 2 : you can add as many Criterias as you want in an Example Object
     * 
     * @param Entity an instance of the entity
     * @param Exemple the Object Exemple with some Criterias inside
     * 
     * @see Exemple
     * @see TypeCritere
     */
	public static final function deleteByExemple(Entity &$entityParam, Exemple $Exemple)
	{

		$db = cmsms()->GetDb();
		$listeField = $entityParam->getFields();

		$criteres = $Exemple->getCriteres();
		$delete = "delete from ".$entityParam->getDbname();
		$hql = "";
		$params = array();
		foreach($criteres as $critere)
		{
		  if(!empty($hql))
		  {
			$hql .= ' AND ';
		  }
		  
		  if(empty($hql))
		  {
			$hql .= ' WHERE ';
		  }

		  $filterType = $listeField[$critere->fieldname]->getType();
		  
				// 1 paramètre  
		  if($critere->typeCritere == TypeCritere::$EQ || $critere->typeCritere == TypeCritere::$NEQ 
			|| $critere->typeCritere == TypeCritere::$GT || $critere->typeCritere == TypeCritere::$GTE 
			|| $critere->typeCritere == TypeCritere::$LT || $critere->typeCritere == TypeCritere::$LTE 
			|| $critere->typeCritere == TypeCritere::$BEFORE || $critere->typeCritere == TypeCritere::$AFTER
			|| $critere->typeCritere == TypeCritere::$LIKE || $critere->typeCritere == TypeCritere::$NLIKE)
		  {  
			$params[] = Core::FieldToDBValue($critere->paramsCritere[0], $filterType); 
			$hql .= $critere->fieldname.$critere->typeCritere.' ? ';
			continue;
		  }
		  
				// 0 paramètre
		  if($critere->typeCritere == TypeCritere::$NULL || $critere->typeCritere == TypeCritere::$NNULL)
		  {  
			$hql .= $critere->fieldname.$critere->typeCritere;
			continue;
		  }
		  
				// 2 paramètres  
		  if($critere->typeCritere == TypeCritere::$BETWEEN)
		  {  
			$params[] = Core::FieldToDBValue($critere->paramsCritere[0], $filterType); 
			$params[] = Core::FieldToDBValue($critere->paramsCritere[1], $filterType); 
			$hql .= $critere->fieldname.$critere->typeCritere.' ? AND ?';
			continue;
		  }
				
				// N paramètres
				if($critere->typeCritere == TypeCritere::$IN || $critere->typeCritere == TypeCritere::$NIN)
				{
					$hql .= ' ( ';
					$second = false; 
					foreach($critere->paramsCritere as $param)
					{
						if($second)
						{
							$hql .= ' OR ';
						}
						$params[] = Core::FieldToDBValue($param, $filterType); 
						$hql .= $critere->fieldname.TypeCritere::$EQ.' ? ';
						
						$second = true;
					}
					$hql .= ' )';
					continue;
				}                        
		  
		  throw new Exception("Le Critere $critere->typeCritere n'est pas encore pris en charge");
		}
		$queryExemple = $delete.$hql;
										

		$result = $db->Execute($queryExemple, $params);
		if ($result === false){die("Database error durant la requete par Exemple!");}
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
     *       $customer = MyAutoload.getInstance($this->GetName(), 'customer');
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
	public static final function FieldToDBValue($data, CAST $type)
	{
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
	public static final function dbValueToField($data, $type)
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
    * Retourne les entités B pouvant être associé à une Entité A
    * 
    * Dans le cas de clé étrangère présente dans une entité, par exemple un article de blog et tous ses tags associés,
    *    il est intéressant de pouvoir récupérer l'intégralité des tags lié et/ou pouvant être lié à notre article de blog sélectionné
    * 
    * La fonction va parcourir L'entité Article , prendre le champs passé en AssociateKey et aller faire une requête de selection sur l'entité Tag
    * 
    * pour rappel une clé associée est configurée ainsi dans Orm :
    * 
       * <code>
    *    class Article extends mEntity
    *    {
    * *       public function __construct()
    *        {
    *            parent::__construct('article','monprojet');
    *            
    *             $this->add(new Field('article_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('tags' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.article_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()  
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *    class Tag extends mEntity
    *    {
    *       public function __construct()
    *        {
    *            parent::__construct('tag','monprojet');
    *            
    *             $this->add(new Field('tag_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('articles' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.billet_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()    
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *   class ArticleBillet extends mEntityAssociation
    *   {
    *        public function __construct()
    *        {
    *            parent::__construct('articlebillet','monprojet');
    *            
    *            $this->add(new Field('article_id'
    *                                   , CAST::$INTEGER
    *                                   ,null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Article.tags'    
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *            $this->add(new Field('tag_id'        
    *                                   , CAST::$INTEGER
    *                                   , null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Tag.articles'  
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *
    *        }    
    *    }
    * </code>
    * 
    * @param Entity an instance of the entity 
    * @param string le nom du champs de l'entité qui servira de point de départ pour la recherche
    * @return array<Entity> la liste des entités associées et pouvant être associée à ce champs.
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
    * Retourne les entités B qui sont déjà associés à une Entité A
    * 
    * Dans le cas de clé étrangère présente dans une entité, par exemple un article de blog et tous ses tags associés,
    *    il est intéressant de pouvoir récupérer l'intégralité des tags liés à notre article de blog sélectionné
    * 
    * La fonction va parcourir L'entité Article , prendre le champs passé en AssociateKey et aller faire une requête de selection sur l'entité Tag
    *     en prenant en compte l'identifiant de l'article en cours
    * 
    * pour rappel une clé associée est configurée ainsi dans Orm :
    * 
    * <code>
    *    class Article extends mEntity
    *    {
    * *       public function __construct()
    *        {
    *            parent::__construct('article','monprojet');
    *            
    *             $this->add(new Field('article_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('tags' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.article_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()  
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *    class Tag extends mEntity
    *    {
    *       public function __construct()
    *        {
    *            parent::__construct('tag','monprojet');
    *            
    *             $this->add(new Field('tag_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('articles' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.billet_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()    
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *   class ArticleBillet extends mEntityAssociation
    *   {
    *        public function __construct()
    *        {
    *            parent::__construct('articlebillet','monprojet');
    *            
    *            $this->add(new Field('article_id'
    *                                   , CAST::$INTEGER
    *                                   ,null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Article.tags'    
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *            $this->add(new Field('tag_id'        
    *                                   , CAST::$INTEGER
    *                                   , null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Tag.articles'  
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *
    *        }    
    *    }
    * </code>
    * 
    * @param Entity an instance of the entity 
    * @param string le nom du champs de l'entité qui servira de point de départ pour la recherche
    * @param mixed l'identifiant de l'entité en cours
    * @return array<Entity> la liste des entités effectivement associées à ce champs.
    */
  public static final function getEntitysAssocieesLiees(Entity &$entityParam, $fieldname, $entityId)
  {
    Trace::debug("getEntitysAssocieesLiees : ".$entityParam->getName()." ".$fieldname." ".$entityId);
    
    $field = $entityParam->getFieldByName($fieldname);
    
    if($field->getKEYName() == '')
        throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
      
    $cle = explode('.',$field->getKEYName(),2);
    
    eval('$entity = new '.$cle[0].'();');
                                                              
    $exemple = new Exemple();    
    $exemple->addCritere($cle[1],TypeCritere::$EQ,array($entityId));
    $assocs = Core::selectByExemple($entity, $exemple);
    
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
    * Vérifie dans toutes les entity existantes, qu'aucune ne possède encore une liaison vers 
    *   l'entité passée en paramètre avec l'Id passé en paramètre
    * 
    *  Cette fonction est utilisée avant une suppression où l'on souhaites s'assurer qu'aucune 
    *     autre entité n'est encore liée'
    * 
    * @param Orm le module en cours
    * @param Entity l'entité servant de modèle
    * @param mixed l'identifiant de la ligne à vérifier.
    */
  public static final function verifIntegrity(Orm $module, Entity &$entity, $sid)
  {
    $listeEntitys = MyAutoload::getAllInstances($module->getName());
  
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
            $Exemple = new Exemple();
            $Exemple->addCritere($field->getName(), TypeCritere::$EQ, array($sid));
            $entitys = Core::selectByExemple($anEntity, $Exemple);
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
    * Permet de réaliser des recherches en profondeur sur une succession de lien inter-entité. 
    * 
    *   Dans le cas ou une entité est linée à une seconde entité, qui a sont tour est liée à une 3eme, 
    *   il est possible de déterminer un chemin entre elles.
    * 
    * Exemple : 
    *   Une commande possède un lien vers un client via le numero de client
    *   Un client possède un lien vers une adresse via son id Adresse (ça permet de gérer des adresses multiples)
    *   Une adresse possède un lien vers une ville via son code postal
    * 
    *  Si je souhaites connaitre l'intégralité des commandes passée pour la ville de Lille, je pourrais faire :
    * 
    * <code>
    *  $villes = //Traitement de recherche d'une ville dont le libellé = Lille
    *  foreach($villes as $ville)
    *  {
    *       $adresses = //Traitement de recherche d'une adresse dont le code postal = $ville->get('codepostal')
    *       foreach($adresses as $adresse)
    *       {
    *           $customers = //Traitement de recherche d'un client possèdant l'ID adresse = $adresse->get('adresse_id')
    *           foreach($customers as $customer)
    *           {
    *               $commandes =  //Traitement de recherche d'une commande possèdant le numeroclient = $customer->get('numeroclient')   
    *           }                   
    *       }                  
    *  }
    * 
    *  </code>
    * 
    *  Cette fonction permet de simplifier énormement la chose puisqu'il suffit de faire : 
    * 
    * <code>
    *  $commandes = Core::makeDeepSearch(new Commande(), 'Commande.numeroclient.adresse_id.codepostal.libelle', array('Lille'));
    * </code>
    * 
    *  
    * 
    * @param Entity L'entité servant de point de départ à la recherche
    * @param string le chemin à parcourir, d'entité en entité séparé par un point.
    * @param array la liste des valeurs recherchées
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
      $Exemple = new Exemple;
      $Exemple->addCritere($fieldname, TypeCritere::$IN, $values);
      $entitys = Core::selectByExemple($previousEntity, $Exemple);
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
      
      //die($nomFieldRetour->getName());
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
    
    
    $Exemple = new Exemple;
    if($nextEntity instanceof EntityAssociation)
    {
      $Exemple->addCritere($previousEntity->getPk()->getName(), TypeCritere::$IN, $ids);
    } else
    {
      $Exemple->addCritere($fieldname, TypeCritere::$IN, $ids);
    }
    $entitys = Core::selectByExemple($previousEntity, $Exemple);
    
    return $entitys;
  }
  
}

?>
