<?php
/**
 * Contient la classe qui gère le cache de mmmfs
 * 
 * @package mmmfs
 **/


/**
 * Gestion d'un mini cache interne
 *
 *  Class static gérant toute la partie cache des accès à la base de donnée. 
 *	Utilisée pour éviter de trop nombreux appels en base sur les listing d'entité chargées
 *  Exemple : 
 *  <code>
 *  	//Appel à cmsms pour récupérer le connecteur de bdd
 *  	$gCms = cmsms();
 *  	$db = $gCms->GetDb();
 *  	
 *  	//Définition d'une entité client
 *  	$entity = new Client();
 *  	
 *  	//Selection de tous les clients
 *  	$querySelect = 'Select * FROM '.$entity->getDbname();
 *  	
 *  	//Si requête précédement executée : on retourne directement du cache le résultat
 *  	if(Cache::isCache($querySelect))
 *  	{
 *  		return Cache::getCache($querySelect);
 *  	}
 *  	
 *  	//On execute la requête
 *  	$result = $db->Execute($querySelect);
 *  	if ($result === false){die("Database error!");}
 *  	
 *  	$entitys = array();
 *  	while ($row = $result->FetchRow())
 *  	{
 *  		$entitys[] = Core::rowToEntity($entity, $row);
 *  	}
 *  	
 *  	//On repousse dans le cache le résultat pour un prochain passage
 *  	Cache::setCache($querySelect, null, $entitys);
 *  	return $entitys;
 *	</code>
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
final class Cache 
{	
	/**
	 * Variable contenant l'ensemble des résultats des requêtes passées
	 **/
	private static $cache;
		
	/**
	 * Constructeur privé
	 */
	protected function __construct() {}
			
	/**
	 *	Définit le cache pour une requête donnée, une liste de paramètre et évidement le résultat obtenu
     * 
	 * @param string la requête sql
	 * @param array la liste des paramètres dans un tableau ou null
	 * @param object la valeur à mémoriser (array ou string ou integer ou ...)
	 */
	public static final function setCache($sql, $params = null, $value)
	{		
		if(!isset(self::$cache))
		{
			self::$cache = array();
		}
		
		self::$cache[Cache::hash($sql,$params)] = $value;
		
	}
	
	/**
	 * Demande le cache pour une requete donnée et une liste de paramètre
     * 
	 * @param string la requête sql
	 * @param array la liste des paramètres dans un tableau ou null
	 * @return object la valeur contenu dans le cache (array ou string ou integer ou ...)
	 */
	public static final function getCache($sql, $params = null)
	{
		if(Cache::isCache($sql, $params))
		{
			return self::$cache[Cache::hash($sql,$params)];
		}
		
		return null;
	}

	/**
	 * Renvoi vrai si un cache existe pour une requête et une liste de paramètre
     * 
	 * @param string la requête sql
	 * @param array la liste des paramètres dans un tableau ou null
	 * @return boolean vrai si un cache existe
	 */	
	public static final function isCache($sql, $params = null)
	{
		return isset(self::$cache) && array_KEY_exists(Cache::hash($sql,$params),self::$cache);
	}

	/**
	 * Vide le cache. Indispensable si entre des lectures de donnée une mise à jour est faite par le traitement.
	 *  Par sécurité le processus vide intégralement le cache
	 */	
	public static final function clearCache()
	{
		unset(self::$cache);
	}

	/**
	 * Retourne une signature unique de la combinaison de la requête SQL et de la liste des paramètres.
	 *  le Hash généré est utilisé dans la class pour récupérer ou pour définir le cache.
     * 
	 * @param string la requête sql
	 * @param array la liste des paramètres dans un tableau ou null
	 * @return string le hashage
	 */	
	public static final function hash($sql, $params = null)
	{
		if($params == null)
		{return md5($sql);}
		
		$p = print_r($params, true);

		return md5($sql.$p);
	}

		
}

?>