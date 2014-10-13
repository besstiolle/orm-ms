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


	public static function isAnEmptyField(OrmField $field, $value){
		if($value === NULL){
			return true;
		}
		if(trim($value) === "") {
			$type = $field->getType();

			if($type === OrmCAST::$STRING || $type === OrmCAST::$BUFFER) {
				return false;	
			}

			return true;
		}

		return false;
	}

	public static function isAValidFormat(OrmField $field, $value) {
		$type = $field->getType();
		
		if($type === OrmCAST::$INTEGER) {

			//Pattern
			$pattern = '#^\-?\d+$#';
			if ( !preg_match($pattern, $value)){
				return false;
			}

			//Size min / max
			if ( $value < -9223372036854775807 ||  $value > 9223372036854775807){
				return false;
			}
		}

		if($type === OrmCAST::$UUID) {

			//Pattern
			$pattern = "/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i";
			if ( !preg_match($pattern, $value)){
				return false;
			}
			
		}

		return true;
	}
}

?>