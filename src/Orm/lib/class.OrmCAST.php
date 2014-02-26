<?php
/**
 * Contains the class with all the different type for the field into database
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
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
	public static $STRING = 0;
	public static $BUFFER = 1;
	
	public static $INTEGER = 2;
	public static $NUMERIC = 3;
	public static $DOUBLE = 4;
	
	public static $DATE = 5;
	public static $TIME = 6;
	public static $TS = 7;
	public static $DATETIME = 8;
	
	public static $UUID = 9;
		  
	public static $INHERIT = 98;
	public static $NONE = 99;
}

?>
