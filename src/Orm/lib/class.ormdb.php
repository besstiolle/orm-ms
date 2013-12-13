<?php
/**
 * Contains the class OrmDB
 * 
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
 
/**
 * Interface between the functions of the Orm framework and Adodb
 *   
 * @since 0.1.2
 * @author Bess
 * @package Orm
*/
class OrmDB {  

	private static $db;
	private static $dict;
	
	private static $taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
	private static $idxoptarrayUnique = array('UNIQUE');

    /**
    * Protected constructor    
    */
	protected function __construct() {}
	
	protected static final function init(){
		if(OrmDB::$db != null){
			return;
		}
		OrmDB::$db = cmsms()->GetDb();
		OrmDB::$dict = NewDataDictionary( OrmDB::$db );
	}
      
    /**
    * will execute the Adodb "execute" function and add logs of everything
    *         
    * @param string the sql Query
    * @param array the list of parameters or null
    * @param string the message of error to display to the user
    * @return the adodb result
	*
	* @exception Exception if the query failed
    */
	public static final function execute($query, $parameters = null, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDB::init();
		OrmTrace::debug($query);
		if($parameters != null){
			OrmTrace::debug(" > Parameters : ".print_r($parameters, true));
		}
		$result = OrmDB::$db->Execute($query, $parameters);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The Query was : ".$query);
			if($parameters != null){
				OrmTrace::error(" > The Parameters was : ".print_r($parameters, true));
			}
			throw new Exception($errorMsg);
		}
		
		return $result;
	}	
	
	 /**
    * will execute the Adodb "GetOne" function and add logs of everything
    *         
    * @param string the sql Query
    * @param array the list of parameters or null
    * @param string the message of error to display to the user
    * @return the adodb result
	*
	* @exception Exception if the query failed
    */
	public static final function getOne($query, $parameters = null, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDB::init();
		OrmTrace::debug($query);
		if($parameters != null){
			OrmTrace::debug(" > Parameters : ".print_r($parameters, true));
		}
		$result = OrmDB::$db->GetOne($query, $parameters);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The Query was : ".$query);
			if($parameters != null){
				OrmTrace::error(" > The Parameters was : ".print_r($parameters, true));
			}
			throw new Exception($errorMsg);
		}
		
		return $result;
	}	
	
	 /**
    * will execute the Adodb "GenID" function and add logs of everything
    *         
    * @param string the table used for sequence 
    * @param string the message of error to display to the user
    * @return the adodb result
	*
	* @exception Exception if the query failed
    */
	public static final function genID($seqname, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("gen Id({$seqname})");
		$result = OrmDB::$db->GenID($seqname);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The GenId was made on : ".$seqname);

			throw new Exception($errorMsg);
		}
		
		return $result;
	}	
	
	 /**
    * will execute the Adodb "CreateTableSQL" function and add logs of everything
    *         
    * @param string the table used for sequence 
    * @param string the hql information about the fields
    * @return the adodb result
	*
	* @exception Exception if the query failed
    */
	public static final function createTable($tableName, $hql) {
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("createTable({$tableName}, {$hql})");
		
		$sqlarray = OrmDB::$dict->CreateTableSQL($tableName, 
												$hql,
												OrmDb::$taboptarray);
												
		$result = OrmDB::$dict->ExecuteSQLArray($sqlarray);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The CreateTable was made on : {$tableName} with {$hql} parameters");

			throw new Exception($errorMsg);
		}
		
		return $result;
	}
	/**
    * will execute the Adodb "DropTableSQL" function and add logs of everything
    *         
    * @param string the table used for sequence 
    * @param string the hql information about the fields
    * @return the adodb result
	*
	* @exception Exception if the query failed
    */
	public static final function dropTable($tableName) {
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("dropTable({$tableName})");
		
		$sqlarray = OrmDB::$dict->DropTableSQL($tableName);
		$result = OrmDB::$dict->executeSQLArray($sqlarray);
		
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The DropTable was made on : {$tableName}");

			throw new Exception($errorMsg);
		}
		
		return $result;
	}
	
	/**
    * will execute the Adodb "CreateSequence" function and add logs of everything
    *         
    * @param string the table used for sequence 
    */
	public static final function createSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("createSequence({$seqName})");
		
		OrmDB::$db->CreateSequence($seqName);
	}
	
	/**
    * will execute the Adodb "DropSequence" function and add logs of everything
    *         
    * @param string the table used for sequence 
    */
	public static final function dropSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("dropSequence({$seqName})");
		
		OrmDB::$db->DropSequence($seqName);
		
	}
	
	/**
    * will execute the Adodb "CreateIndexSQL" function with or without UNIQUE parameter and add logs of everything
    *         
    * @param string the table used for sequence 
    * @param mixed the list of the FieldName (array) or a single fieldName (String)
    * @param boolean true if the index must be UNIQUE
    * @return the adodb result
	*
	* @exception Exception if the query failed
    */
	public static final function createIndex($tableName, $listFields, $isUnique = false){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("createIndex({$tableName}, {".implode(',',$listFields)."}, {$isUnique})");
				
		//Case : unique index on many fields
		if(is_array($listFields)) {
			$idxflds = implode(',', $listFields);
			$md5 = md5(serialize($listFields));
		} else {
			$idxflds = $listFields;
			$md5 = md5($listFields);
		}
		if($isUnique){
			$sqlarray = OrmDB::$dict->CreateIndexSQL($md5, $tableName, $idxflds, OrmDB::$idxoptarrayUnique);
		} else {
			$sqlarray = OrmDB::$dict->CreateIndexSQL($md5, $tableName, $idxflds);
			//die(implode(",",$sqlarray));
		}
		
		$result = OrmDB::$dict->executeSQLArray($sqlarray);
		
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The createIndex was made on : {$tableName} with the fields : {$listFields}");

			throw new Exception($errorMsg);
		}
		
		return $result;
	}
	
	
	
	
}

?>
