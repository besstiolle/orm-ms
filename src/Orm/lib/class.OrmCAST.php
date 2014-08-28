<?php
/**
 * Contains the class with all the different type for the field into database
 *
 * @since 0.0.1
 * @author Bess
 **/


/**
 * Class Defines the type of the field, in entity but also in database
 * 
 *  OrmCAST::$STRING a simple string
 *  OrmCAST::$BUFFER a field with no limit of size (except the mysql natural limit)
 *
 *  OrmCAST::$INTEGER an integer
 *  OrmCAST::$NUMERIC a field for a real number (eg : with coma)
 *  OrmCAST::$DOUBLE a field for big number
 * 
 *  OrmCAST::$DATE a field date
 *  OrmCAST::$TIME a field time
 *  OrmCAST::$TS a field timestamp
 *  OrmCAST::$DATETIME a field dateTime
 *  OrmCAST::$UUID a field UUID
 *
 *  OrmCAST::$NONE a Transiant field : won't be persist into database
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
*/
class OrmCAST
{
	/**
	 * * Stored in database under the SQL type String (x)
	 **/
	public static $STRING = "STRING";

	/**
	 * * Stored in database under the SQL type BUFFER
	 **/
	public static $BUFFER = "BUFFER";
	
	/**
	 * * Stored in database under the SQL type INTEGER (x)
	 **/
	public static $INTEGER = "INTEGER";

	/**
	 * * Stored in database under the SQL type NUMBER (x)
	 **/
	public static $NUMERIC = "NUMERIC";

	/**
	 * * Stored in database under the SQL type DOUBLE (x)
	 **/
	public static $DOUBLE = "DOUBLE";
	
	/**
	 * * Stored in database under the SQL type DATE
	 **/
	public static $DATE = "DATE";

	/**
	 * * Stored in database under the SQL type TIME
	 **/
	public static $TIME = "TIME";

	/**
	 ** Stored in database under the SQL type INTEGER (?)
	 **/
	public static $TS = "TS";

	/**
	 ** Stored in database under the SQL type DATETIME
	 **/
	public static $DATETIME = "DATETIME";
	
	/**
	 * Stored in database under the type SQL String (32)
	 **/
	public static $UUID = "UUID";
	
	/**
	 * Will inherit from it's parents
	 **/
	public static $INHERIT = "INHERIT";

	/**
	 * Won't be stored in database
	 **/
	public static $NONE = "NONE";
}

?>
