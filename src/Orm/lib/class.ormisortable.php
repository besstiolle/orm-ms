<?php
/**
 * Contains the interface ISortable which allow to sort results for an entity
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
interface OrmISortable
{
	
    /**
    * Function which compare two entities in parameters
    * 
    * 
    * @param Entity the first entity with all values setted
    * @param Entity the second entity with all values setted
	*
    * @return Integer : 0 if equals, 1 if the first entity is superior, else -1
    * 
    * @see Entity
    */
	public static function compareTo(OrmEntity $entity1, OrmEntity $entity2);
}



?>