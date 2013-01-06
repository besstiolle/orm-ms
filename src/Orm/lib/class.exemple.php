<?php
/**
 * Contains Exemple Class
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
 
/**
 * Represents a group of differents Criteria (Criteres) to process a search "By Example" on a Entity in Database
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
final class Exemple 
{
	private $Criteres = array();
	
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
    * @param TypeCritere Type of Criteria
    * @param array all the parameters used for the parameter $typeCritere
    * @param boolean [Optionnal] if we must ignore the case (aze equals AZE) or not. Default value is "false"
    * 
    * @see TypeCritere
    */
	public function addCritere($fieldname, $typeCritere, $paramsCritere, $ignoreCase = false)
	{
		if(!is_array($paramsCritere))
		{
			throw new Exception("the parameter \$paramsCritere for the Criteria of the Field [".$fieldname."] must be an array");
		}
		
		$this->Criteres[] = new Critere($fieldname, $typeCritere, $paramsCritere, $ignoreCase);
	}

    /**
    * Return all the Criterias contained in the current Example Object
    * 
    * @return array<Critere> the list of Criterias contained in the current Example Object
    */
	public function getCriteres(){
		return $this->Criteres;
	}
}

?>