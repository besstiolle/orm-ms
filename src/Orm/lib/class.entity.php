<?php
/**
 * Contient les classe m�res de toutes les entit�s et entit�s associatives
 * @package mmmfs
 **/
 
/**
 * Class abstract d�crivant le comportement et les propri�t�s d'une Entit�
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
	
	
	//Contient le nom du Field servant de cl�
	private $pk;
	
	//Contient le nom du module en cours d'appel
	private $moduleName;
	
	
    /**
    * Constructeur semi-priv� pour �viter d'instancer cette classe depuis le code par erreur
    * 
    *   A chaque construction, l'entit� est plac�e dans l'Autoloader
    * 
    * @param string le nom d'un module de type Mmmfs
    * @param string le nom de l'entit�
    * @param string [facultatif] Le pr�fixe � utiliser en base de donn�e pour les tables. En g�n�ral le nom de votre module
    * @param string [facultatif] Le nom de la table li�e � cette entit�. Si non renseign�e on prendra le nom de l'entit�
    * @return Entity l'entit servant de mod�le
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
    *  doit �tre surcharg�e dans les classes h�ritant de Entity qui souhaitent
	*	initialiser les tables durant l'installation du module
    * 
    */
	public function initTable(){}
    	
    /**
    * Ajoute un nouveau champs Field � la liste d�j� pr�sente dans l'Entit�
    * 
    * @param Field le champs � ajouter.
    */
	protected function add(Field $newField)
	{
		$this->fields[$newField->getName()] = $newField;

		//Ajout d'une sequence sur les cle
		if($newField->isPrimaryKEY())
		{
			if($this->pk != null)
				throw new Exception("Le programme ne gere pas les entites � multiples cles primaires (PK)");
				
			$this->pk = $newField->getName();
			$this->seqname = $this->dbname.Entity::$_CONST_SEQ;
		}
	}
	
    /**
    * Retourne le champs servant de Primary Key de l'entit�
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
    * Retourne la liste des Fields de l'entit�'
    * 
    * @return array<Field> un tableau contenant tous les Fields 
    * 
    */
	public function getFields()
	{
		return $this->fields;
	}
	
    /**
    * retourne un Field � partir du nom pass� en param�tre
    * 
    * @param string $name
    * @return Field le Field correspondant
    * @exception si aucun Field ne correspond au param�tre pass�
    */
	public function getFieldByName($name)
	{
		if(isset($this->fields[$name]))
			return $this->fields[$name];
		
		throw new Exception("le champs $name n'existe pas dans l'entit&eacute; ".$this->getName());
	}
	
	/**
    * retourne vrai si un Field � partir du nom pass� en param�tre existe
    * 
    * @param string $name
    * @return Boolean si existant
    */
	public function isFieldByNameExists($name)
	{
		return isset($this->fields[$name]);
	}
	
    /**
    * Retourne le nom de la table en base de l'Entit�
    * 
    * @return string le nom de la table
    * 
    */
	public function getDbname()
	{
		return $this->dbname;
	}
	
    /**
    * Retourne le nom de l'entit�
    * 
    * @return string le nom de l'entit�
    * 
    */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 *  Return the name of the current module
	 *  
	 *  @Return string the name of the current module.
	 **/
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
    /**
    * Retourne le nom de la s�quence si elle existe
    * 
    * @return string le nom de la s�quence ou NULL si inexistante
    * 
    */
	public function getSeqname()
	{
		if(empty($this->seqname))
			return null;
		
		return $this->seqname;
	}
	
    /**
    * Retourne la valeur d'un Field de l'entit�
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
    * Affecte une valeur � un Field de l'entit�
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
    * Retourne l'ensemble des valeurs d�finie pour l'entit�
    * 
    * le tableau est un tableau associatif d�finit comme tel :
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
    * Permet en surchargeant cette m�thode depuis la classe h�ritant de Entity de faire 
    *   du pre-traitement des donn�es avant sauvegarde en base.
    * 
    * @param array tableau contenant les valeurs � traiter
    * @param array �ventuels param�tres suppl�mentaires d�stin� � la fonction
    * @return mixed � d�finir selon le traitement 
    */
	public function processValueForSave($rows, $args = null){
	
		return $rows;
	}
	
  	
	/**
	 * Fonction utilitaire permettant de passer une chaine du type "blabla %nom% blabla %prenom% blabla" et de r�cup�rer en sortie les correspondances avec les valeurs de l'entit� courante
	 * exemple : "blabla Dupont blabla Jean blabla". Il faut �videment que les informations contenues entre %% soient le nom d'un FIELD de l'entit� valide.
	 *
	 * @param string la chaine de caract�res � traiter
	 * @param string le pattern de recherche. Par d�faut traitera ce qui se trouve entre deux symboles pourcentage : %
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
	 * Fonction utilitaire permettant de nettoyer la chaine de caract�re en vue d'utilisation dans une url
	 *
	 * @param string la chaine a traiter
	 *
	 * @return string la chaine nettoyee
	 **/
	/*protected function processStringForUrl($texte)
	{	
		//Suppression des accents et autres conneries
		$texte = htmlentities($texte, ENT_QUOTES, 'UTF-8');
		
		$texte = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $texte);
		$texte = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $texte); // pour les ligatures e.g. '&oelig;'
		$texte = preg_replace('#&[^;]+;#', '', $texte); // supprime les autres caract�res sp�ciaux
		$texte = preg_replace('# #', '-', $texte); // espace ->  tiret 6
		$texte = preg_replace('#[^a-zA-Z0-9_\-,]#', '', $texte); // supprime les caract�res qui ne suiveraient pas la r�gle de reroute   
		return $texte;
	}*/
	
	/**
	 * Fait un appel � compareTo de l'entit� pour trier le tableau. 
	 * Pour en b�n�ficier, l'entit� doit impl�menter l'interface ISortable
	 *
	 * Exemple de m�thode compareTo() � coder dans votre entit� de type client : 
	 *  <code>
	 *   * Effectue une comparaison sommaire (�galit� absolue) entre deux entit�s. 
	 *	 * doit �tre red�finie dans chaque entit� avec laquelle nous souhaitons un vrai comparatif
	 *	 * par habitude : renvoi 1 si la premi�re entit� est sup�rieure, -1 si inf�rieure, 0 si �galit�.
	 *	 *
	 *	 * @param Entity la premi�re entit� � comparer.
	 *	 * @param Entity la seconde entit� � comparer.
	 *	 *
	 *	public static function compareTo(Entity $entity1, Entity $entity2)
	 *	{
	 *		$compare = strcmp($entity1->get('nom'), $entity2->get('nom'));
	 *      return $compare;
	 *	}
	 * 
	 *  </code>
	 *
	 * @param array<Entity> le tableau d'Entit�
	 *
	 * @return array<Entity> le tableau d'Entit� tri� selon la m�thode compareTo d�fini dans l'entit�.
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
