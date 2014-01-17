<?php
/**
 * Contains the class Id
 *
 * @since 0.3.0
 * @author Heriquet
 * @package Orm
 **/

 
/**
 *   Represent an Entity's primary key and provides services
 *
 * @since 0.3.0
 * @author Heriquet
 * @package Orm
 **/
class OrmId 
{	
	/**
	 * list : fields making up the id
	 * */
	private $fields = array();
	
    /**
    * public constructor
    * 	
    * @return OrmId the OrmId Object
    * 
    */
	public function __construct() {

	}

	public function addField(OrmField $field){
		$fields[$field->getName()] = $field;
	}
	
	public function getName(){
		$name = '';
		$glue = '|';
		foreach($this->pk as $elt){
			if(!empty($name)){
				$name .= $glue;
			}
			$name .= $elt;
		}
		return $name;
	}
	
	public function isEmpty(){
		return empty($fields);		
	}
	
	public function getFields(){
		return $this->fields;
	}
}

?>