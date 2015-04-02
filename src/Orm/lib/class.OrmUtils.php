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
	 * Will return a array with values from a list of OrmEntity
	 * checking also each child node performing the processing recursivly
	 *
	 * @param OrmEntity[] $entities a list of OrmEntity
	 * @return mixed[] $result the array with basic values
	 *
	 **/
	public static function entitiesToAbsoluteArray($entities){
		if(is_object($entities) && is_subclass_of($entities, 'OrmEntity')){
			return OrmUtils::entitiyToAbsoluteArray($entities);
		} else if (!is_array($entities)) {
			throw new IllegalArgumentException("function OrmUtils::entitiesToAbsoluteArray($entities) wait a Array of OrmEntity as parameter", 1);
		}
		$result = array();
		foreach ($entities as $entity) {
			$result[] = OrmUtils::entitiyToAbsoluteArray($entity);
		}
		return $result;
	}

	/**
	 * Will return a array with values from an OrmEntity
	 * checking also each child node performing the processing recursivly
	 *
	 * @param OrmEntity $entity an OrmEntity
	 * @return mixed[] $result the array with basic values
	 *
	 **/
	public static function entitiyToAbsoluteArray($entity){
		if(is_array($entity)){
			return OrmUtils::entitiesToAbsoluteArray($entity);
		} else if (!is_object($entity) || !is_subclass_of($entity, 'OrmEntity') ) {
			throw new IllegalArgumentException(" function OrmUtils::entitiyToAbsoluteArray($entity) wait a OrmEntity as parameter", 1);
		}

		$result = $entity->getValues();
		foreach($result as $key => $value) {
			if(is_object($value) && is_subclass_of($value, 'OrmEntity')){
				$result[$key] = OrmUtils::entitiyToAbsoluteArray($value);
			} else if (is_array($value)){
				$result[$key] = OrmUtils::entitiesToAbsoluteArray($value);
			} else {
				//Nothing
			}
		}
		return $result;
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

	/**
	 * Return true if the Field is empty ( if integer & equals to 0, it won't be empty )
	 *
	 * @param OrmField $field the field
	 * @param mixed $value the value 
	 *
	 * @return boolean true if the field is empty
	 **/
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
	/**
	 * Will return true if the Field respect the format 
	 * and the min/max value of the Field
	 *
	 * @param OrmField $field the field
	 * @param mixed $value the value 
	 *
	 * @return boolean true if the field is valide
	 **/
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