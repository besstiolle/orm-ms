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

	/**
	 * Contains the adodb engine
	 */
	private static $db;

	/**
	 * Contains the adodb dictionnary
	 */
	private static $dict;
	
	/**
	 * Contains the default value for database and table creation for cmsms
	 */
	private static $taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');

	/**
	 * Contains words for the UNIQUE key
	 */
	private static $idxoptarrayUnique = array('UNIQUE');

	/**
	 * Contains the last SQL queries
	 **/
	private static $bufferQueries = array();

	/**
	 * The size of the buffer.
	 **/
	private static $bufferLength = 5;

    /**
    * Protected constructor    
    */
	protected function __construct() {}
	
	/**
	 * Allow initializing the OrmDb fields
	 */
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
    * @param string $query the sql Query
    * @param mixed[] $parameters array the list of parameters or null
    * @param string $errorMsg the message of error to display to the user
    * @return mixed the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function execute($query, $parameters = null, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		OrmTrace::sql($query);
		if($parameters != null){
			OrmTrace::sql(" > Parameters : ".print_r($parameters, true));
		}
		$result = OrmDb::$db->Execute($query, $parameters);
		//Push Query in buffer
		OrmDb::pushQueries();

		if ($result === false || !empty(OrmDb::$db->ErrorMsg())) {
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
    * @param string $query the sql Query
    * @param mixed[] $parameters array the list of parameters or null
    * @param string $errorMsg the message of error to display to the user
    * @return mixed the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function getOne($query, $parameters = null, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		OrmTrace::sql($query);
		if($parameters != null){
			OrmTrace::sql(" > Parameters : ".print_r($parameters, true));
		}
		$result = OrmDb::$db->GetOne($query, $parameters);
		//Push Query in buffer
		OrmDb::pushQueries();

		if ($result === false || !empty(OrmDb::$db->ErrorMsg())) {
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
    * @param string $seqname the table used for sequence 
    * @param string $errorMsg the message of error to display to the user
    *
    * @return mixed the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function genID($seqname, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::sql("gen Id({$seqname})");
		$result = OrmDb::$db->GenID($seqname);
		//Push Query in buffer
		OrmDb::pushQueries();

		if ($result === false || !empty(OrmDb::$db->ErrorMsg())) {
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
    * @param string $tableName the table used for sequence 
    * @param string $hql the hql information about the fields
    *
    * @return mixed the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function createTable($tableName, $hql, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::sql("createTable({$tableName}, \"{$hql}\")");
		
		//Push Query in buffer
		OrmDb::pushQueries();
		$sqlarray = OrmDb::$dict->CreateTableSQL($tableName, 
												$hql,
												OrmDb::$taboptarray);
												
		$result = OrmDb::$dict->ExecuteSQLArray($sqlarray);
		//Push Query in buffer
		OrmDb::pushQueries();

		if ($result === false || !empty(OrmDb::$db->ErrorMsg())) {
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
    * @param string $tableName the table used for sequence 
	* 
    * @return mixed the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function dropTable($tableName, $errorMsg = "Database error") {
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::sql("dropTable({$tableName})");
		
		$sqlarray = OrmDb::$dict->DropTableSQL($tableName);
		//Push Query in buffer
		OrmDb::pushQueries();

		$result = OrmDb::$dict->executeSQLArray($sqlarray);
		//Push Query in buffer
		OrmDb::pushQueries();
		
		if ($result === false || !empty(OrmDb::$db->ErrorMsg())) {
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
    * @param string $seqName the table used for sequence 
    */
	public static final function createSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::sql("createSequence({$seqName})");
		
		OrmDb::$db->CreateSequence($seqName);
		//Push Query in buffer
		OrmDb::pushQueries();
	}
	
	/**
    * will execute the Adodb "DropSequence" function and add logs of everything
    *         
    * @param string $seqName the table used for sequence 
    */
	public static final function dropSequence($seqName){
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::sql("dropSequence({$seqName})");
		
		OrmDb::$db->DropSequence($seqName);
		//Push Query in buffer
		OrmDb::pushQueries();
		
	}
	
	/**
    * will execute the Adodb "CreateIndexSQL" function with or without UNIQUE parameter and add logs of everything
    *         
    * @param string $tableName the table used for sequence 
    * @param mixed $listFields the list of the FieldName (array) or a single fieldName (String)
    * @param boolean $isUnique true if the index must be UNIQUE
    *
    * @return mixed the adodb result
	*
	* @exception OrmSqlException if the query failed
    */
	public static final function createIndex($tableName, $listFields, $isUnique = false, $errorMsg = "Database error"){
		//Be sure we initiate the db connector;
		OrmDb::init();
		
		OrmTrace::sql("createIndex({$tableName}, {".implode(',',$listFields)."}, {$isUnique})");
				
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
			//Push Query in buffer
			OrmDb::pushQueries();
		} else {
			//Push Query in buffer
			OrmDb::pushQueries();
			$sqlarray = OrmDb::$dict->CreateIndexSQL($md5, $tableName, $idxflds);
		}
		
		$result = OrmDb::$dict->executeSQLArray($sqlarray);
		//Push Query in buffer
		OrmDb::pushQueries();
		
		if ($result === false || !empty(OrmDb::$db->ErrorMsg())) {
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDb::$db->ErrorMsg());
			OrmTrace::error(" > The createIndex was made on : {$tableName} with the fields : {$listFields}");

			throw new OrmSqlException($errorMsg);
		}
		
		return $result;
	}

	/**
    * Will return the last SQL Queries executed
    *
    * @return string[] the last SQL Queries executed
    */
	public static final function getBufferQueries(){
		return OrmDb::$bufferQueries;
	}

	/**
    * will resize the buffer for the SQL Queries
    *         
    * @param int $newLength the new size
    */
	public static final function setBufferLength($newLength){
		OrmDb::$bufferLength = $newLength;
		OrmDb::resizeBuffer();
	}

	/**
    * will push the last query in the buffer
    */	
	private static final function pushQueries(){
		if(OrmDb::$bufferLength == 0){
			return;
		}
		OrmDb::$bufferQueries[] = OrmDb::$db->sql;
		OrmDb::resizeBuffer();

		OrmTrace::sql("[MYSQL] ".OrmDb::$db->sql);
	}

	/**
    * inner function to resize the buffer
    */	
	private static final function resizeBuffer(){
		if(OrmDb::$bufferLength == 0){
			OrmDb::$bufferQueries = array();
		}
		$currentCount = count(OrmDb::$bufferQueries);
		if($currentCount > OrmDb::$bufferLength) {
			OrmDb::$bufferQueries = array_slice (OrmDb::$bufferQueries, - OrmDb::$bufferLength);
		}
	}
}

?>
