<?php
/**
 * Contains utilities
 *
 * @since 0.3.0
 * @author Heriquet
 * @package Orm
 **/


/**
 * Class contains some utilities
 * 
 * @since 0.3.0
 * @author Heriquet
 * @package Orm
*/
class OrmUtils
{
	/*
	* Assigns all array values to the entity, 
	* especially for helping the developper to assign all form variables to the entity, 
	* or to load an entity with a OrmDb::execute custom query
	* Be carefull with this function
	* 
	* @param OrmEntity an instance of the entity  
	* @param data array hashtable
	*/ 
	public static function arrayToEntity(OrmEntity &$entity, &$data) {
		foreach($entity->getFields() as $field) {
			if(isset($data[$field->getName()])) {
				$entity->set($field->getName(), $data[$field->getName()]);
			}
		}
	}
}

?>