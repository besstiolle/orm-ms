<?php
/**
 * Contient la classe qui gère les différents type de Typage en base de donée
 * 
 * @package mmmfs
 **/


/**
 * Classe définissant les typage des champs
 * 
 *  CAST::$STRING une chaine de caractère 
 *  CAST::$INTEGER un entier
 *  CAST::$DATE une date 
 *  CAST::$TIME une zone time
 *  CAST::$TS un timestamp
 *  CAST::$BUFFER une zone de texte non limité en taille
 *  CAST::$NUMERIC un nombre réél (virgule)
 *  CAST::$NONE ne pas stocker en base (dans le cas d'une clée associative)    
 * 
 * @since 1.0
 * @author Bess
 * @package mmmfs
*/
class CAST
{
	public static $STRING = 0;
	public static $INTEGER = 1;
	public static $DATE = 2;
	public static $BUFFER = 3;
	public static $NUMERIC = 4;
	public static $TIME = 5;
	public static $TS = 6;
	public static $NONE = 9;
}

?>