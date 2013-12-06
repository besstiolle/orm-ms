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
 *  CAST::$STRING a simple string
 *  CAST::$INTEGER an integer
 *  CAST::$DATE a field date
 *  CAST::$TIME a field time
 *  CAST::$TS a field timestamp
 *  CAST::$BUFFER a field with no limit of size (except the mysql natural limit)
 *  CAST::$NUMERIC a field for a real number (eg : with coma)
 *  CAST::$NONE a Transiant field : won't be persist into database
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
*/
class CAST
{
	public static $STRING = 0;
	public static $INTEGER = 1;
	public static $DATE = 2;
	public static $BUFFER = 3;
	public static $NUMERIC = 4;
	public static $TIME = 5;
	public static $TS = 6;
	public static $UUID = 8;
	
	public static $NONE = 99;
}

?>