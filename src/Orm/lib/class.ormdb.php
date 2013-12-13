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
    */
	public static final function genID($seqname, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("gen Id({$seqname})");
		$result = OrmDB::$db->GenID($seqname, $parameters);
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
	
	public static final function executeSQLArray($sqlarray){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("executeSQLArray({$tableName})");
		
		$result = OrmDB::$dict->ExecuteSQLArray($sqlarray);
		
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The exception was thrown on : executeSQLArray({$sqlarray})");

			throw new Exception($errorMsg);
		}
		
		return $result;

	}
	
	public static final function createSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("createSequence({$seqName})");
		
		OrmDB::$db->CreateSequence($seqName);
	}
	
	public static final function dropSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("dropSequence({$seqName})");
		
		OrmDB::$db->DropSequence($seqName);
		
	}
	
	public static final function createIndex($tableName, $listFields){
		//Be sure we initiate the db connector;
		OrmDB::init();
		
		OrmTrace::debug("createIndex({$tableName}, {$listFields})");
				
		//Case : unique index on many fields
		if(is_array($listFields)) {
			$idxflds = implode(',', $listFields);
			$md5 = md5(serialize($listFields));
		} else {
			$idxflds = $listFields;
			$md5 = md5($listFields);
		}
		
		$sqlarray = OrmDB::$dict->CreateIndexSQL($md5, $tableName, $idxflds, OrmDB::$idxoptarrayUnique);
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
