<?php
/**
 * Contains the class wich provide a mini caching system of Orm to avoid multiples sql requests
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/


/**
 *
 *  Static classe used to provide a very simple caching system. You can push the result of a request into it and asking later
 *	to collect the result.
 *
 *  Example : 
 *  <code>
 *  	//Call to cmsms to get the database connector
 *  	$db = cmsms()->GetDb();
 *  	
 *  	//Defines a new Customer entity
 *  	$entity = MyAutoload::getInstance('myModule', 'customer');
 *  	
 *  	//Select all Customers
 *  	$querySelect = 'Select * FROM '.$entity->getDbname();
 *  	
 *  	//If the caching system already know the answer : we return the result immediately
 *  	if(OrmCache::isCache($querySelect))
 *  	{
 *  		return OrmCache::getCache($querySelect);
 *  	}
 *  	
 *  	//So we need to execute the query
 *  	$result = $db->Execute($querySelect);
 *  	if ($result === false){die("Database error!");}
 *  	
 *  	$entitys = array();
 *  	while ($row = $result->FetchRow())
 *  	{
 *  		$entitys[] = OrmCore::rowToEntity($entity, $row);
 *  	}
 *  	
 *  	//Don't forget to push the result into the caching system for the next call
 *  	OrmCache::setCache($querySelect, null, $entitys);
 
 *  	return $entitys;
 *	</code>
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
/*final*/ class OrmCache
{	
	/**
	 * Contains all the result for the past requetes
	 **/
	private static $cache;
		
	/**
	 * Private constructor
	 */
	protected function __construct() {}
			
	/**
	 *	Set the cache for a sql request, its parameters and of course the result
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 * @param object the result
	 */
	public static final function setCache($sql, $params = null, $value)
	{		
		if(!isset(self::$cache))
		{
			self::$cache = array();
		}
		
		self::$cache[OrmCache::hash($sql,$params)] = $value;
		
	}
	
	/**
	 * Querying the cache for a sql request and its parameters
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return object the result
	 */
	public static final function getCache($sql, $params = null)
	{
		if(OrmCache::isCache($sql, $params))
		{
			return self::$cache[OrmCache::hash($sql,$params)];
		}
		
		return null;
	}

	/**
	 * Return true if a cache exist for a sql request and its parameters
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return boolean true if the cache exists
	 */	
	public static final function isCache($sql, $params = null)
	{
		return isset(self::$cache) && array_KEY_exists(OrmCache::hash($sql,$params),self::$cache);
	}

	/**
	 * Empty the cache. Very important if between 2 quering, the system may insert/delete/update some data in the database
	 *  In the Orm system, we always drop the cache in the insert/delete/update function.
	 */	
	public static final function clearCache()
	{
		self::$cache=null;
	}

	/**
	 * Return a unique hash for a sql request and its parameters
	 *  this hash is used to Defines a unique entry into the cache
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return string the hash
	 */	
	public static final function hash($sql, $params = null)
	{
		if($params == null)
		{return md5($sql);}
		
		$p = print_r($params, true);

		return md5($sql.$p);
	}
}

?>