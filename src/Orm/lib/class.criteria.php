<?php
/**
 * Contains Critere class
 * 
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
 

/**
 * Backbone for Criteria into the Orm system
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
final class Critere
{
	/**
	 * Name of the field 
	 */
	public $fieldname;
	
	/**
	 * Type of Criteria
	 */
	public $typeCritere;
	
	/**
	 * Parameters for the type of Criteria
	 */
	public $paramsCritere;
	
	/**
	 * Boolean if we must ignore case
	 */
	public $ignoreCase;
	
    /**
    * Public Constructor
    * 
    * @param string Name of the field 
    * @param TypeCritere Type of Criteria
    * @param array all the parameters used for the parameter $typeCritere
    * @param boolean [Optionnal] if we must ignore the case (aze equals AZE) or not. Default value is "false"
    *
	* @return Critere a Criteria
	*
	* @see TypeCritere
    */
	public function __construct($fieldname, $typeCritere, $paramsCritere, $ignoreCase = false)
	{
		$this->fieldname = $fieldname;
		$this->typeCritere = $typeCritere;
		$this->paramsCritere = $paramsCritere;
		$this->ignoreCase = $ignoreCase;
	}
	
}

?>