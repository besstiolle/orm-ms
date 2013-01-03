<?php
/**
 * Contient toutes les fonctionnalités relatives aux recherches par Critères
 * 
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
 

/**
* Enum des différents TypeCritere envisageable
* 
* utiliser ainsi :
* 
* <code>
* TypeCritere::$EQ
* </code>
* 
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
class TypeCritere
{
    /**
    * Est égal à
    * 
    * @var string
    */
	public static $EQ = ' = ';
    
    /**
    * Est différent de 
    * 
    * @var string
    */
	public static $NEQ = ' != ';
    
    /**
    * est strictement supérieur à
    * 
    * @var string
    */
	public static $GT = ' > ';
    
    /**
    * est superieur ou égal à
    * 
    * @var string
    */
	public static $GTE = ' >= ';
    
    /**
    * est strictement inferieur à
    * 
    * @var string
    */
	public static $LT = ' < ';
    
    /**
    * est inferieur ou égal à 
    * 
    * @var string
    */
	public static $LTE = ' <= ';
	
	/**
    * est null ou vide
    * 
    * @var string
    */
	public static $EMPTY = 'is empty()';
	
    /**
    * est non null et non vide
    * 
    * @var string
    */
	public static $NEMPTY = 'is not empty()';
    
    /**
    * est null
    * 
    * @var string
    */
	public static $NULL = ' is null ';
    
    /**
    * n'est pas null
    * 
    * @var string
    */
	public static $NNULL = ' is not null';
    
    /**
    * est avant (Date)
    * 
    * @var string
    */
	public static $BEFORE = ' before ';
    
    /**
    * est après (Date)
    * 
    * @var string
    */
	public static $AFTER = ' after ';
    
    /**
    * est entre (Date)
    * 
    * @var string
    */
	public static $BETWEEN = ' after ';
     
    /**
    * est contenu dans la liste suivante
    * 
    * /!\ nécessite au minimum deux valeurs dans le tableau de paramètre
    * 
    * @var string
    */
	public static $IN = 'in (%a)';
    
    /**
    * n'est pas contenu dans la liste suivante
    *                                                                   
    * /!\ nécessite au minimum deux valeurs dans le tableau de paramètre
    * 
    * @var string
    */
	public static $NIN = 'not in (%a)';
    
    /**
    * contient la chaine suivante
    * 
    * /!\ vous devez ajouter vous même les caractères génériques '%'
    * 
    * @var string
    */
	public static $LIKE = ' like ';
    
    /**
    * contient la chaine suivante
    * 
    * /!\ vous devez ajouter vous même les caractères génériques '%'
    * 
    * @var string
    */
	public static $NLIKE = ' not like ';
}
?>