<?php
/**
 * Contains the class wich provide an dummy caching system
 *
 * @since 0.2.0
 * @author Bess
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
	
	/**
	 * @var OrmCacheScript $instance The current instance
	 **/
	private static $instance;
		
	/**
	 * Private constructor
	 */
	protected function __construct() {}
	
	/**
	 * Will return an instance of the cache class
	 *
	 * @return OrmCacheNone the cache class
	 */
	public static function getMyOwnInstance(){
		if(self::$instance == null){
			self::$instance = new OrmCacheNone();
		}
		return self::$instance;
	}
			
	/**
	 *	Set the cache for a sql request, its parameters and of course the result
     * 
	 * @param string $sql the sql query
	 * @param mixed[] $params array the parameters into a array. May be null
	 * @param mixed $value the result
	 */
	public function setCache($sql, $params = null, $value) {		
		//No processing		
	}
	
	/**
	 * Querying the cache for a sql request and its parameters
     * 
	 * @param string $sql the sql query
	 * @param mixed[] $params array the parameters into a array. May be null
	 *
	 * @return mixed the result
	 */
	public function getCache($sql, $params = null) {
		return null;
	}

	/**
	 * Return true if a cache exist for a sql request and its parameters
     * 
	 * @param string $sql the sql query
	 * @param mixed[] $params array the parameters into a array. May be null
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
	 * @param string $sql the sql query
	 * @param mixed[] $params array the parameters into a array. May be null
	 *
	 * @return string the hash
	 */	
	public function hash($sql, $params = null) {
		//No processing	
	}
}

?>
