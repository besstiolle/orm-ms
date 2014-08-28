<?php
/**
 * Contains OrmExample Class
 *
 * @since 0.0.1
 * @author Bess
 **/
 
 
/**
 * Represents a group of different OrmCriteria (Criterias) to process a search "By Example" on a OrmEntity in Database
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
class OrmExample {

    /**
    * The inner list of Criterias
    **/
	private $criterias = array();
	
    /**
    * Public Constructor
    */
	public function __construct() {
	}
	
    
    /**
    * Add a new Criteria on the existing list
    * 
    * @param string $fieldname Name of the field 
    * @param OrmTypeCriteria $typeCriteria Type of Criteria
    * @param mixed[] $paramsCriteria all the parameters used for the parameter $typeCriteria
    * @param boolean $ignoreCase [Optional] if we must ignore the case (aze equals AZE) or not. Default value is "false"
    * 
    * @see OrmTypeCriteria
    */
	public function addCriteria($fieldname, OrmTypeCriteria $typeCriteria, $paramsCriteria, $ignoreCase = false) {
		if(!is_array($paramsCriteria)) {
			throw new Exception("the parameter \$paramsCriteria for the Criteria of the Field [".$fieldname."] must be an array");
		}
		
		$this->criterias[] = new OrmCriteria($fieldname, $typeCriteria, $paramsCriteria, $ignoreCase);
	}

    /**
    * Return all the Criterias contained in the current Example Object
    * 
    * @return Criteria[] the list of Criterias contained in the current Example Object
    */
	public function getCriterias(){
		return $this->criterias;
	}
}

?>
