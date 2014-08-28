<?php
/**
 * Contains OrmCriteria class
 * 
 * @since 0.0.1
 * @author Bess
 **/
 
 

/**
 * Backbone for OrmCriteria into the Orm system
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
class OrmCriteria
{
	/**
	 * Name of the field 
	 */
	public $fieldname;
	
	/**
	 * Type of Criteria
	 */
	public $typeCriteria;
	
	/**
	 * Parameters for the type of Criteria
	 */
	public $paramsCriteria;
	
	/**
	 * Boolean if we must ignore case
	 */
	public $ignoreCase;
	
    /**
    * Public Constructor
    * 
    * @param string $fieldname Name of the field 
    * @param OrmTypeCriteria $typeCriteria Type of Criteria
    * @param mixed[] $paramsCriteria array with all the parameters used for the parameter $typeCriteria
    * @param boolean $ignoreCase [Optional] if we must ignore the case (aze equals AZE) or not. Default value is "false"
    *
	* @return OrmCriteria a Criteria
	*
	* @see OrmTypeCriteria
    */
	public function __construct($fieldname, $typeCriteria, $paramsCriteria, $ignoreCase = false)
	{
		$this->fieldname = $fieldname;
		$this->typeCriteria = $typeCriteria;
		$this->paramsCriteria = $paramsCriteria;
		$this->ignoreCase = $ignoreCase;
	}
}

?>
