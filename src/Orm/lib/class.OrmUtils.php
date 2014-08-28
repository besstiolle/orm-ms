<?php
/**
 * Contains utilities
 *
 * @since 0.3.0
 * @author Heriquet
 **/


/**
 * Class contains some utilities
 * 
 * @since 0.3.0
 * @author Heriquet
 * @package Orm
*/
class OrmUtils {
	
	/**
	* Assigns all array values to the entity, 
	* especially for helping the developper to assign all form variables to the entity, 
	* or to load an entity with a OrmDb::execute custom query
	* Be carefull with this function
	* 
	* @param OrmEntity $entity an instance of the entity  
	* @param mixed[] $data array hashtable
	*/ 
	public static function arrayToEntity(OrmEntity $entity, $data) {
		foreach($entity->getFields() as $field) {
			if(isset($data[$field->getName()])) {
				$entity->set($field->getName(), $data[$field->getName()]);
			}
		}
	}

	/**
	 * Return a unique encoding for the list of key.
	 * For now simply an Json Encoding
	 *
	 * @param mixed[] $primaryKeysValue list of the values of each primarykey
	 *
	 * @return string securized hash for the entity (json)
	 */
	public static function generatePUID(array $primaryKeysValue){
		
		$puid = json_encode($primaryKeysValue);

		return $puid;
	}
}

?>