<?php
/**
 * Contient l'interface de Tri iSortable qui permet de trier les résultats d'une entité
 * @package mmmfs
 **/
 
/**
 * Interface de tri d'une Entité
 *	
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
interface ISortable
{
	
    /**
    * Fonction qui compare deux entités passées en paramètre 
    * 
    * 
    * @param Entity la première entité avec ses valeurs renseignées
    * @param Entity la seconde entité avec ses valeurs renseignées
    * @return entier zéro si égalité, 1 si la première entité est supérieure, -1 si la seconde entité passée en paramètre est supérieure
    * 
    * @see Entity
    */
	public static function compareTo(Entity $entity1, Entity $entity2);
}



?>