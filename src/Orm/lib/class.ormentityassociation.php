<?php
/**
 * Contains OrmEntityAssociation Class which extends OrmEntity class
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
/**
 * Abstract Class which describes the properties for an Associative Entity
 *    
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
abstract class OrmEntityAssociation extends OrmEntity 
{
	private $nbField = 0;

    /**
     * constructor protected to avoid a direct instanciation like "new OrmEntityAssociation()"
     * Each time a entity is constructed, we place a copy into the autoloader.
     * 
     * @param string The name of the module who calling this method (so not "Orm")
     * @param string The name of the entity
     * @param string [optional] Prefix to use into database for table. If not setted, it will use the name of your module
     * @param string [optional] The name of table for this entity. If not setted, it will use the name of your entity
     *
     * @return OrmEntityAssociation the entity as a new instance
     * 
     * @see MyAutoload
     */
	protected function __construct($moduleName, $name,$prefixe = null, $dbName = null)
	{
		parent::__construct($moduleName, $name, $prefixe, $dbName);
	}
	
    /**
    * Add a new Field
    * 
    * Currently, the Orm System doen't allow more than two Field for an EntityAssociation
    * 
    * @param OrmField the Field to add.
	*
	* @exception OrmIllegalConfigurationException if we try to use more than two Fields in the entity
    */
	protected function add(OrmField $newField)
	{
		$this->nbField ++;
		
		if($this->nbField > 2)
			throw new OrmIllegalConfigurationException ("It's impossible to use more than two Field in the EntityAssociation ". $this->getName());
				
		parent::add($newField);
	}

}