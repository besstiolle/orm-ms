<?php
/**
 * Contains Example Class
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
 
/**
 * Represents a group of differents OrmCriteria (Criterias) to process a search "By Example" on a OrmEntity in Database
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
final class OrmExample 
{
	private $Criterias = array();
	
    /**
    * Public Constructor
    * 
    */
	public function __construct()
	{
	}
	
    
    /**
    * Add a new Criteria on the existing list
    * 
    * @param string Name of the field 
    * @param TypeCriteria Type of Criteria
    * @param array all the parameters used for the parameter $typeCriteria
    * @param boolean [Optionnal] if we must ignore the case (aze equals AZE) or not. Default value is "false"
    * 
    * @see TypeCriteria
    */
	public function addCriteria($fieldname, $typeCriteria, $paramsCriteria, $ignoreCase = false)
	{
		if(!is_array($paramsCriteria))
		{
			throw new Exception("the parameter \$paramsCriteria for the Criteria of the Field [".$fieldname."] must be an array");
		}
		
		$this->Criterias[] = new OrmCriteria($fieldname, $typeCriteria, $paramsCriteria, $ignoreCase);
	}

    /**
    * Return all the Criterias contained in the current Example Object
    * 
    * @return array<Criteria> the list of Criterias contained in the current Example Object
    */
	public function getCriterias(){
		return $this->Criterias;
	}
}

?>