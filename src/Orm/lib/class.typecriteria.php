<?php
/**
 * Contains the TypeCriteria class
 * 
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 

/**
 * Enum for the different types of TypeCriteria
 * 
 * 
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
class TypeCriteria
{
    /**
    * Is equals to
    * 
    * @var string
    */
	public static $EQ = ' = ';
    
    /**
    * Is different Of
    * 
    * @var string
    */
	public static $NEQ = ' != ';
    
    /**
    * is strictly greater  than
    * 
    * @var string
    */
	public static $GT = ' > ';
    
    /**
    * is greater  or equals to
    * 
    * @var string
    */
	public static $GTE = ' >= ';
    
    /**
    * is strictly lesser than
    * 
    * @var string
    */
	public static $LT = ' < ';
    
    /**
    * is lesser or equals to
    * 
    * @var string
    */
	public static $LTE = ' <= ';
	
	/**
    * is NULL or is Empty
    * 
    * @var string
    */
	public static $EMPTY = 'is empty()';
	
    /**
    * is Not NULL and is Not Empty
    * 
    * @var string
    */
	public static $NEMPTY = 'is not empty()';
    
    /**
    * is NULL
    * 
    * @var string
    */
	public static $NULL = ' is null ';
    
    /**
    * is Not NULL
    * 
    * @var string
    */
	public static $NNULL = ' is not null';
    
    /**
    * is before (a Date)
    * 
    * @var string
    */
	public static $BEFORE = ' before ';
    
    /**
    * is after (a Date)
    * 
    * @var string
    */
	public static $AFTER = ' after ';
    
    /**
    * is between (a Date)
    * 
    * @var string
    */
	public static $BETWEEN = ' after ';
     
    /**
    * is contained into the array
    * 
    * /!\ need at last 2 values in the array
    * 
    * @var string
    */
	public static $IN = 'in (%a)';
    
    /**
    * is not contained into the array
    *                                                                   
    * /!\ need at last 2 values in the array
    * 
    * @var string
    */
	public static $NIN = 'not in (%a)';
    
    /**
    * contains the string
    * 
    * /!\ don't forget to add the wildcard '%'
    * 
    * @var string
    */
	public static $LIKE = ' like ';
    
    /**
    * doesn't contain the string
    * 
    * /!\ don't forget to add the wildcard '%'
    * 
    * @var string
    */
	public static $NLIKE = ' not like ';
}
?>