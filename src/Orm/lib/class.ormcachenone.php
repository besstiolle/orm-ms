<?php
/**
 * Contains the class wich provide an dummy caching system
 *
 * @since 0.2.0
 * @author Bess
 * @package Orm
 **/


/**
 *
 *  Static classe used to provide a zero-caching system. 
 *   It's a simple dummy implementation of the signature of caching
 *   wich will always say that there is no cache available, forcing 
 *   processing of every queries
 *
 * @since 0.2.0
 * @author Bess
 * @package Orm
 **/
class OrmCacheNone extends OrmCache{	
	
	private static $instance;
		
	/**
	 * Private constructor
	 */
	protected function __construct() {}
	
	public function getInstance(){
		if(self::$instance == null){
			self::$instance = new OrmCacheNone();
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
		//No processing		
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
		return false;
	}

	/**
	 * Empty the cache. Very important if between 2 querying, the system may insert/delete/update some data in the database
	 *  In the Orm system, we always drop the cache in the insert/delete/update function.
	 */	
	public function clearCache(){
		//No processing	
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
	public function hash($sql, $params = null) {
		//No processing	
	}
}

?>