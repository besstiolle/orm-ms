<?php
/**
 * Contains the interface IOrmSortable which allow to sort results for an entity
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 
/**
 * Interface wich allow to sort results for an entity
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
interface IOrmSortable
{
	
    /**
    * Function which compare two entities in parameters
    * 
    * 
    * @param OrmEntity the first entity with all values setted
    * @param OrmEntity the second entity with all values setted
	*
    * @return Integer : 0 if equals, 1 if the first entity is superior, else -1
    * 
    * @see OrmEntity
    */
	public static function compareTo(OrmEntity $entity1, OrmEntity $entity2);
}



?>