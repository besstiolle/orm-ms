<?php
/**
 * Contient les classe mères de toutes les entités et entités associatives
 * @package mmmfs
 **/
 
/**
 * Class abstract décrivant le comportement et les propriétés d'une Entité
 *	
 *
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
abstract class Entity 
{
	//nom de l'entite
	protected $name;

	//nom de la table
	private $dbname;
	
	//nom de la sequence ou null si pas de sequence
	private $seqname;
	
	//Liste des champs de l'entite
	private $fields = array();
	
	//Liste des valeurs correspondantes
	private $values = array();
	
	public static $_CONST_SEQ = '_seq';
	public static $_CONST_MOD = 'module';
	
	
	//Contient le nom du Field servant de clé
	private $pk;
	
	//Contient le nom du module en cours d'appel
	private $moduleName;
	
	
    /**
    * Constructeur semi-privé pour éviter d'instancer cette classe depuis le code par erreur
    * 
    *   A chaque construction, l'entité est placée dans l'Autoloader
    * 
    * @param string le nom d'un module de type Mmmfs
    * @param string le nom de l'entité
    * @param string [facultatif] Le préfixe à utiliser en base de donnée pour les tables. En général le nom de votre module
    * @param string [facultatif] Le nom de la table liée à cette entité. Si non renseignée on prendra le nom de l'entité
    * @return Entity l'entit servant de modèle
    * 
    * @see MyAutoload
    */
	protected function __construct($moduleName, $name, $prefixe = null, $dbName = null)
	{
		$this->moduleName = strtolower($moduleName);
		$this->name = strtolower($name);
		
		$this->dbname = $this->name;
		if(!empty($dbName))
		{
			$this->dbname = strtolower($dbName);
		}
		
		if(empty($prefixe))
		{
			$prefixe = $this->moduleName;
		} else {
			$prefixe = strtolower($prefixe);
		}
		
		$this->dbname = cms_db_prefix().Entity::$_CONST_MOD.'_'.$prefixe.'_'.$this->dbname;
		
		//On ajoute une instance de soit dans l'autoload
		myAutoload::addInstance($this->moduleName,$this);
	}
	
    /**
    *  doit être surchargée dans les classes héritant de Entity qui souhaitent
	*	initialiser les tables durant l'installation du module
    * 
    */
	public function initTable(){}
    	
    /**
    * Ajoute un nouveau champs Field à la liste déjà présente dans l'Entité
    * 
    * @param Field le champs à ajouter.
    */
	protected function add(Field $newField)
	{
		$this->fields[$newField->getName()] = $newField;

		//Ajout d'une sequence sur les cle
		if($newField->isPrimaryKEY())
		{
			if($this->pk != null)
				throw new Exception("Le programme ne gere pas les entites à multiples cles primaires (PK)");
				
			$this->pk = $newField->getName();
			$this->seqname = $this->dbname.Entity::$_CONST_SEQ;
		}
	}
	
    /**
    * Retourne le champs servant de Primary Key de l'entité
    * 
    * @return Field le champs PK si existant 
    * @exception si aucun champs PK n'existe
    */
	public function getPk()
	{
		if($this->pk == null)
			throw new Exception("la class ".$this->getName()." ne possede pas de cle primaire");
		
		return $this->fields[$this->pk];
	}
	
    /**
    * Retourne la liste des Fields de l'entité'
    * 
    * @return array<Field> un tableau contenant tous les Fields 
    * 
    */
	public function getFields()
	{
		return $this->fields;
	}
	
    /**
    * retourne un Field à partir du nom passé en paramètre
    * 
    * @param string $name
    * @return Field le Field correspondant
    * @exception si aucun Field ne correspond au paramètre passé
    */
	public function getFieldByName($name)
	{
		if(isset($this->fields[$name]))
			return $this->fields[$name];
		
		throw new Exception("le champs $name n'existe pas dans l'entit&eacute; ".$this->getName());
	}
	
	/**
    * retourne vrai si un Field à partir du nom passé en paramètre existe
    * 
    * @param string $name
    * @return Boolean si existant
    */
	public function isFieldByNameExists($name)
	{
		return isset($this->fields[$name]);
	}
	
    /**
    * Retourne le nom de la table en base de l'Entité
    * 
    * @return string le nom de la table
    * 
    */
	public function getDbname()
	{
		return $this->dbname;
	}
	
    /**
    * Retourne le nom de l'entité
    * 
    * @return string le nom de l'entité
    * 
    */
	public function getName()
	{
		return $this->name;
	}
	
    /**
    * Retourne le nom de la séquence si elle existe
    * 
    * @return string le nom de la séquence ou NULL si inexistante
    * 
    */
	public function getSeqname()
	{
		if(empty($this->seqname))
			return null;
		
		return $this->seqname;
	}
	
    /**
    * Retourne la valeur d'un Field de l'entité
    * 	
    * @param string le nom du champs
    * @return mixed la valeur contenu dans ce champs
    * @exception si aucun champs n'existe avec ce nom
    */
	public function get($fieldName)
	{
		$fieldnameSid = explode("_sid", $fieldName);
		$fieldnameSid = $fieldnameSid[0];
		if(!array_KEY_exists($fieldName,$this->fields) && !array_KEY_exists($fieldnameSid,$this->fields))
		{throw new Exception('fonction Get : cle '.$fieldName.' non trouvee dans l\'entite '.$this->getName());}
		
		if(!isset($this->values[$fieldName]))
			return null;
		
		return $this->values[$fieldName];
	}
	
   /**
    * Affecte une valeur à un Field de l'entité
    *     
    * @param string le nom du champs
    * @param mixed la valeur 
    * @exception si aucun champs n'existe avec ce nom
    */
	public function set($fieldName,$value)
	{
		$fieldnameSid = explode("_sid", $fieldName);
		$fieldnameSid = $fieldnameSid[0];
		if(!array_KEY_exists($fieldName,$this->fields) && !array_KEY_exists($fieldnameSid,$this->fields))
		{throw new Exception('fonction Set : cle '.$fieldName.' non trouvee dans l\'entite');}
		
		$this->values[$fieldName] = $value;
	}
	
    /**
    * Retourne l'ensemble des valeurs définie pour l'entité
    * 
    * le tableau est un tableau associatif définit comme tel :
    * 
    * <code>
    *  array(
    *        'nom_du_field' => valeur,
    *        'nom_du_field' => valeur,
    *        'nom_du_field' => valeur,
    *        'nom_du_field' => valeur
    *     )
    *  </code>
    * 
    * @return array un tableau associatif
    * 
    */
	public function getValues()
	{
		return $this->values;
	}
	
	
	public function initForeignKEY($fieldName, $sid = null)
	{			
		$field = $this->getFieldByName($fieldName);
		
		if($field->getKEYName() == '')
			throw new Exception("Le champs $fieldName ne possede aucune cle etrangere associee");
			
		$cle = explode('.',$field->getKEYName(),2);
		//Evaluation de la eclass en cours
		eval('$entity = new '.$cle[0].'();');
		
		if($sid == null)
		{
			$liste = Core::selectAll($entity);
		} else
		{
			$liste = Core::selectByIds($entity, array($sid));
		}
		
		return array($entity,$liste);
	
	}
	
    /**
    * Permet en surchargeant cette méthode depuis la classe héritant de Entity de faire 
    *   du pre-traitement des données avant sauvegarde en base.
    * 
    * @param array tableau contenant les valeurs à traiter
    * @param array éventuels paramètres supplémentaires déstiné à la fonction
    * @return mixed à définir selon le traitement 
    */
	public function processValueForSave($rows, $args = null){
	
		return $rows;
	}
	
  	
	/**
	 * Fonction utilitaire permettant de passer une chaine du type "blabla %nom% blabla %prenom% blabla" et de récupérer en sortie les correspondances avec les valeurs de l'entité courante
	 * exemple : "blabla Dupont blabla Jean blabla". Il faut évidement que les informations contenues entre %% soient le nom d'un FIELD de l'entité valide.
	 *
	 * @param string la chaine de caractères à traiter
	 * @param string le pattern de recherche. Par défaut traitera ce qui se trouve entre deux symboles pourcentage : %
	 */ 
	/*function processStringWithValues($string, $pattern = '/%(\w+)%/i')
	{
		$occu = preg_match_all($pattern, $string, $resultat);
		for($i = 0; $i < $occu; $i++)
		{
			$resultat[1][$i] = $this->get($resultat[1][$i]);
		}
		$string = str_replace($resultat[0], $resultat[1], $string, $occu);
		return $string;
	}*/
	
	/**
	 * Fonction utilitaire permettant de nettoyer la chaine de caractère en vue d'utilisation dans une url
	 *
	 * @param string la chaine à traiter
	 *
	 * @return string la chaine nettoyée
	 **/
	/*protected function processStringForUrl($texte)
	{	
		//Suppression des accents et autres conneries
		$texte = htmlentities($texte, ENT_QUOTES, 'UTF-8');
		
		$texte = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $texte);
		$texte = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $texte); // pour les ligatures e.g. '&oelig;'
		$texte = preg_replace('#&[^;]+;#', '', $texte); // supprime les autres caractères spéciaux
		$texte = preg_replace('# #', '-', $texte); // espace ->  tiret 6
		$texte = preg_replace('#[^a-zA-Z0-9_\-,]#', '', $texte); // supprime les caractères qui ne suiveraient pas la règle de reroute   
		return $texte;
	}*/
	
	/**
	 * Fait un appel à compareTo de l'entité pour trier le tableau. 
	 * Pour en bénéficier, l'entité doit implémenter l'interface ISortable
	 *
	 * Exemple de méthode compareTo() à coder dans votre entité de type client : 
	 *  <code>
	 *   * Effectue une comparaison sommaire (égalité absolue) entre deux entités. 
	 *	 * doit être redéfinie dans chaque entité avec laquelle nous souhaitons un vrai comparatif
	 *	 * par habitude : renvoi 1 si la première entité est supérieure, -1 si inférieure, 0 si égalité.
	 *	 *
	 *	 * @param Entity la première entité à comparer.
	 *	 * @param Entity la seconde entité à comparer.
	 *	 *
	 *	public static function compareTo(Entity $entity1, Entity $entity2)
	 *	{
	 *		$compare = strcmp($entity1->get('nom'), $entity2->get('nom'));
	 *      return $compare;
	 *	}
	 * 
	 *  </code>
	 *
	 * @param array<Entity> le tableau d'Entité
	 *
	 * @return array<Entity> le tableau d'Entité trié selon la méthode compareTo défini dans l'entité.
	 */
	public static function sort(Entity $entity, array $array)
	{
		
		//http://php.net/manual/fr/function.get-called-class.php
		//PHP 5.3.0 seulement
		usort($array, array(get_called_class(), "compareTo"));
		//usort($array, array(get_class($entity), "compareTo"));
		
		return $array;
	}
	
}

?>