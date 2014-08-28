<?php
/**
 * Contains OrmEntityAssociation Class which extends OrmEntity class
 *
 * @since 0.0.1
 * @author Bess
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
    /**
     * Inner counter for the number of fields
     **/
	private $nbField = 0;

    /**
     * constructor protected to avoid a direct instantiation like "new OrmEntityAssociation()"
     * Each time a entity is constructed, we place a copy into the autoloader.
     * 
     * @param string $moduleName The name of the module who calling this method (so not "Orm")
     * @param string $name The name of the entity
     * @param string $prefixe [optional] Prefix to use into database for table. If not setted, it will use the name of your module
     * @param string $dbName [optional] The name of table for this entity. If not setted, it will use the name of your entity
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
    * @param OrmField $newField the Field to add.
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
