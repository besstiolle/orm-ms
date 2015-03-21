<?php
/**
 * Contains the class wich provide a persisted caching system of Orm to avoid multiples sql requests
 *
 * @since 0.4.0
 * @author Bess
 **/

/**
 *
 *  Static classe used to provide a very simple persisted caching system. You can push the result of a request into it and asking later
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
 *
 *  	return $entitys;
 *	</code>
 *
 *  The cache is available for an amount of time 
 *  And will be clean the time after.
 *
 *
 * @since 0.4.0
 * @author Bess
 * @package Orm
 **/
class OrmCacheFile extends OrmCache {	
	
	/**
	 * @var OrmCacheFile $instance The current instance
	 **/
	private static $instance;

	/**
	 * @var mixed[] $cache an array containing all the data cached 
	 **/
	private static $cache;


	/**
	 * @var string $filename the file in tmp directory wich will contains cache data 
	 **/
	private static $filename;
		
	/**
	 * Private constructor
	 *
	 * @param mixed[] $parameters the parameter to the constructor
	 */
	protected function __construct($parameters) {
		self::$cache = array();
		$config = cmsms()->GetConfig();
		self::$filename = $config['root_path'].'/tmp/cache/cache_orm';
		if(file_exists(self::$filename) && is_readable(self::$filename)){
			$content = file_get_contents(self::$filename);
			self::$cache = unserialize($content);
		} else {
			self::saveCache();
		}
	}
	
	/**
	 * Will return an instance of the cache class
	 *
	 * @param mixed[] $parameters the parameter to the constructor
	 *
	 * @return OrmCacheFile the cache class
	 */
	public static function getMyOwnInstance($parameters){
		if(self::$instance == null){
			self::$instance = new OrmCacheFile($parameters);
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
		/*echo '# '.$sql.'<br/>';
		echo '# '.var_dump($params).'<br/>';
		echo '# '.self::hash($sql,$params).'<br/>';*/
		self::$cache[self::hash($sql,$params)] = $value;
		self::saveCache();
		
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
		if(self::isCache($sql, $params)) {
			return self::$cache[self::hash($sql,$params)];
		}
		
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
		/*echo '| '.$sql.'<br/>';
		echo '| '.var_dump($params).'<br/>';
		echo '| '.self::hash($sql,$params).'<br/>';*/
		return array_KEY_exists(self::hash($sql,$params),self::$cache);
	}

	/**
	 * Empty the cache. Very important if between 2 querying, the system may insert/delete/update some data in the database
	 *  In the Orm system, we always drop the cache in the insert/delete/update function.
	 */	
	public function clearCache() {
		self::$cache = array();
		self::saveCache();
	}

	private function saveCache(){
		if(FALSE === file_put_contents(self::$filename ,serialize(self::$cache) )){
			echo "<h3>Orm can't write into the cache file : ".$filename."</h3>";
		}
	}

}

?>
