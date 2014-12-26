<?php
/**
 * Contains the autoload system that will memorize all the entities used by all the modules
 *
 * @since 0.0.1
 * @author Bess
 **/
 
 /**
 * Static Class managing entities used in the application
 *	The main use is to not redeclarate X instances of same classes if it's not necessary.
 * All modules have their own namespace to avoid sharing entities between two modules
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
final class MyAutoload
{
	/**
	 * List of all instances
	 **/
	private static $instances;
	
	/**
	 * Private constructor
	 */
	protected function __construct(){}	
	
	/**
	 * Function called by the entities them-self during their _construct() function
	 * it will stock an instance of the entity (if not already existing) in his memory
     * 
	 * @param string $namespace namespace of the entity's module
	 * @param OrmEntity $instance an instance of the entity
	 */
	public final static function addInstance($namespace, OrmEntity $instance)
	{	
		$namespace = strtolower($namespace);
		$name = strtolower($instance->getName());
				
		if(!MyAutoload::isValideNamespace($namespace)){
			self::$instances[$namespace] = array();
		}
				
		if(isset(self::$instances[$namespace][$name])) {
			OrmTrace::debug("Instance ".$name." already in memory.");
			return;
		}
		
		OrmTrace::debug("Adding the instance ".$name." into the namespace ".$namespace);
		self::$instances[$namespace][$name] = $instance;
	}
	
	/**
	 * Return an instance of entity from the memory. Each instance is a clone to avoid using
	 * the same object 
     * 
	 * @param string $namespace namespace of the entity's module
	 * @param string $instanceName name of an instance of the entity
	 *
	 * @return OrmEntity an instance of entity
	 */
	public final static function getInstance($namespace, $instanceName)
	{
		$namespace = strtolower($namespace);
		$instanceName = strtolower($instanceName);
		if(!MyAutoload::isValideNamespace($namespace)){
			OrmTrace::error("The namespace '$namespace' doesn't existe into the Orm System");
			throw new OrmIllegalArgumentException("The namespace '$namespace' doesn't existe into the Orm System");
		}
		
		OrmTrace::debug("Asking an instance of ".$instanceName. " for namespace ".$namespace);
		if(MyAutoload::hasInstance($namespace, $instanceName))
		{
			OrmTrace::debug("Instance ".$instanceName." returned.");
			return clone self::$instances[$namespace][$instanceName];
		}
		
		OrmTrace::error("No instance $instanceName found in memory for namespace ".$namespace);
		throw new Exception("No instance $instanceName found in memory for namespace ".$namespace);
	}
	
	/**
	 * Return true if the instance exists in the memory for the same namespace
     * 
	 * @param string $namespace namespace of the entity's module
	 * @param string $instanceName name of an instance of the entity
	 *
	 * @return Boolean if the instance exists
	 */
	public final static function hasInstance($namespace, $instanceName){
		$namespace = strtolower($namespace);
		$instanceName = strtolower($instanceName);
		
		if(MyAutoload::isValideNamespace($namespace)){
			return false;
		}
		
		return isset(self::$instances[$namespace][$instanceName]);
	}
	
	/**
	 * Return all the instances of all the entities from a single namespace
     * 
	 * @param string $namespace namespace of the entity's module
	 *
	 * @return array<OrmEntity> an array of all the entities
	 */
	public final static function getAllInstances($namespace)
	{
		$namespace = strtolower($namespace);
		if(!MyAutoload::isValideNamespace($namespace)){
			OrmTrace::warn("The namespace '$namespace' doesn't exist into the Orm System, you may have forgot to write some entities ?");
			return null;
		}
		
		return self::$instances[$namespace];
	}
	
	/**
	 * Retourne true if the namespace is known in memory
     * 
	 * @param string $namespace namespace of the entity's module
	 *
	 * @return Boolean if the namespace exists
	 */
	public static function isValideNamespace($namespace) {
		return isset(self::$instances[strtolower($namespace)]);
	}
}
?>
