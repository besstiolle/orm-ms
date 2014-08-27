<?php
/**
 * Contains the class OrmDb
 * 
 * @since 0.1.2
 * @author Bess
 **/
 
 
/**
 * Interface between the functions of the Orm framework and Adodb
 *   
 * @since 0.1.2
 * @author Bess
 * @package Orm
*/
class OrmDb {  

	private static $db;
	private static $dict;
	
	private static $taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
	private static $idxoptarrayUnique = array('UNIQUE');

    /**
    * Protected constructor    
    */
	protected function __construct() {}
	
	protected static final function init(){
		if(OrmDb::$db != null){
			return;
		}
		OrmDb::$db = cmsms()->GetDb();
		OrmDb::$dict = NewDataDictionary( OrmDb::$db );
	}
      
    /**
    * will execute the Adodb "execute" function and add logs of everything
    *         
    * @param string the sql Query
    * @param array the list of parameters or null
    * @param string the message of error to display to the user
    * @return the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function execute($query, $parameters = null, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		OrmTrace::debug($query);
		if($parameters != null){
			OrmTrace::debug(" > Parameters : ".print_r($parameters, true));
		}
		$result = OrmDb::$db->Execute($query, $parameters);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The Query was : ".$query);
			if($parameters != null){
				OrmTrace::error(" > The Parameters was : ".print_r($parameters, true));
			}
			throw new OrmSqlException($errorMsg);
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
	* @exception OrmSqlException if the query failed
    */
	public static final function getOne($query, $parameters = null, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		OrmTrace::debug($query);
		if($parameters != null){
			OrmTrace::debug(" > Parameters : ".print_r($parameters, true));
		}
		$result = OrmDb::$db->GetOne($query, $parameters);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The Query was : ".$query);
			if($parameters != null){
				OrmTrace::error(" > The Parameters was : ".print_r($parameters, true));
			}
			throw new OrmSqlException($errorMsg);
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
	* @exception OrmSqlException if the query failed
    */
	public static final function genID($seqname, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::debug("gen Id({$seqname})");
		$result = OrmDb::$db->GenID($seqname);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The GenId was made on : ".$seqname);

			throw new OrmSqlException($errorMsg);
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
	* @exception OrmSqlException if the query failed
    */
	public static final function createTable($tableName, $hql) {
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::debug("createTable({$tableName}, {$hql})");
		
		$sqlarray = OrmDb::$dict->CreateTableSQL($tableName, 
												$hql,
												OrmDb::$taboptarray);
												
		$result = OrmDb::$dict->ExecuteSQLArray($sqlarray);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The CreateTable was made on : {$tableName} with {$hql} parameters");

			throw new OrmSqlException($errorMsg);
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
	* @exception OrmSqlException if the query failed
    */
	public static final function dropTable($tableName) {
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::debug("dropTable({$tableName})");
		
		$sqlarray = OrmDb::$dict->DropTableSQL($tableName);
		$result = OrmDb::$dict->executeSQLArray($sqlarray);
		
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The DropTable was made on : {$tableName}");

			throw new OrmSqlException($errorMsg);
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
		OrmDb::init();
		
		OrmTrace::debug("createSequence({$seqName})");
		
		OrmDb::$db->CreateSequence($seqName);
	}
	
	/**
    * will execute the Adodb "DropSequence" function and add logs of everything
    *         
    * @param string the table used for sequence 
    */
	public static final function dropSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::debug("dropSequence({$seqName})");
		
		OrmDb::$db->DropSequence($seqName);
		
	}
	
	/**
    * will execute the Adodb "CreateIndexSQL" function with or without UNIQUE parameter and add logs of everything
    *         
    * @param string the table used for sequence 
    * @param mixed the list of the FieldName (array) or a single fieldName (String)
    * @param boolean true if the index must be UNIQUE
    * @return the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function createIndex($tableName, $listFields, $isUnique = false){
		//Be sure we initiate the db connector;
		OrmDb::init();
		
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
			$sqlarray = OrmDb::$dict->CreateIndexSQL($md5, $tableName, $idxflds, OrmDb::$idxoptarrayUnique);
		} else {
			$sqlarray = OrmDb::$dict->CreateIndexSQL($md5, $tableName, $idxflds);
		}
		
		$result = OrmDb::$dict->executeSQLArray($sqlarray);
		
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The createIndex was made on : {$tableName} with the fields : {$listFields}");

			throw new OrmSqlException($errorMsg);
		}
		
		return $result;
	}
	
	
	
	
}

?>
