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
		
		$dict = NewDataDictionary( OrmDb::$db );
		$sqlarray = $dict->CreateTableSQL($tableName, 
												$hql,
												OrmDb::$taboptarray);
												
		$result = $dict->ExecuteSQLArray($sqlarray);
		if ($result === false){
			OrmTrace::error($errorMsg);
			OrmTrace::error(" > Mysql said : ".OrmDB::$db->ErrorMsg());
			OrmTrace::error(" > The CreateTable was made on : {$tableName} with {$hql} parameters");

			throw new Exception($errorMsg);
		}
		
		return $result;
	}
	
	
	
	
}

?>
