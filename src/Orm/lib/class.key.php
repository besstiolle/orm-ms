<?php
/**
 * Contient la classe qui définit les différentes clé gérée
 * 
 * @package mmmfs
 **/

/**
 * Classe définissant les clés utilisable dans le framework
 * 
 *  KEY::$PK définit une clé primaire (identifiant technique) 
 *  KEY::$FK définit une clé étrangère (sert de liaison entre deux entités)
 *  KEY::$AK définit une clé associative (sert de liaison entre deux entités nécessitant une table d'association intermédiaire') 
 * 
 * @since 1.0
 * @author Bess
 * @package mmmfs
*/
class KEY
{
	public static $PK = 0x9901; // Primary KEY
	public static $FK = 0x9902; // Foreign KEY
	public static $AK = 0x9903; // Associate KEY (necessite une table intermediaire)
}

?>