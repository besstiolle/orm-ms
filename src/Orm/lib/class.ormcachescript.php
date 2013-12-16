<?php
/**
 * Contains the class wich provide a mini caching system of Orm to avoid multiples sql requests
 *
 * @since 0.2.0
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
 *  	if(OrmCache::getInstance()->isCache($querySelect)) {
 *  		return OrmCache::getInstance()->getCache($querySelect);
 *  	}
 *  	
 *  	//So we need to execute the query
 *  	$result = $db->Execute($querySelect);
 *  	if ($result === false){die("Database error!");}
 *  	
 *  	$entitys = array();
 *  	while ($row = $result->FetchRow()) {
 *  		$entitys[] = OrmCore::rowToEntity($entity, $row);
 *  	}
 *  	
 *  	//Don't forget to push the result into the caching system for the next call
 *  	OrmCache::getInstance()->setCache($querySelect, null, $entitys);
 
 *  	return $entitys;
 *	</code>
 *
 *  The cache is only available for the current call of the php script 
 *  And will be unavailable for the next call.
 *
 *
 * @since 0.2.0
 * @author Bess
 * @package Orm
 **/
class OrmCacheScript extends OrmCache {	
	
	private static $instance;
	private static $cache;
		
	/**
	 * Private constructor
	 */
	protected function __construct() {
		self::$cache = array();
	}
	
	public function getInstance(){
		if(self::$instance == null){
			self::$instance = new OrmCacheScript();
		}
		return self::$instance;
	}
	
	/**
	 *	Set the cache for a sql request, its parameters and of course the result
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 * @param object the result
	 */
	public function setCache($sql, $params = null, $value) {		
		self::$cache[self::hash($sql,$params)] = $value;
		
	}
	
	/**
	 * Querying the cache for a sql request and its parameters
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return object the result
	 */
	public function getCache($sql, $params = null) {
		if(self::isCache($sql, $params)) {
			return self::$cache[self::hash($sql,$params)];
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
	public function isCache($sql, $params = null) {
		return array_KEY_exists(self::hash($sql,$params),self::$cache);
	}

	/**
	 * Empty the cache. Very important if between 2 querying, the system may insert/delete/update some data in the database
	 *  In the Orm system, we always drop the cache in the insert/delete/update function.
	 */	
	public function clearCache() {
		self::$cache=null;
	}

}

?>