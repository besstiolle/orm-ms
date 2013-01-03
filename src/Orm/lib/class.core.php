<?php
/**
 * Noyau du framework
 * 
 * @package mmmfs
 **/
 
 
/**
* Librairie de fonction pour le framework
* 
* Core est la classe qui fait l'interface entre les méthodes natives de CmsMadeSimple et les besoins communs
*  dans les modules utilisant le framework. 
* 
* @package mmmfs
*/
class Core 
{  
    /**
    * Constructeur protected, peut être surchargé par une autre classe.
    *     
    */
  protected function __construct() {}
      
    /**
    * Transforme les informations d'une entité en une série de commande utilisable par adodb (le moteur bdd de cmsmadesimple)
    *         
    * @param Entity l'entité 
    * @return la chaine à destination d'adodb.
    */
  public static final function getFieldsToHql(Entity &$entity)
  {    
    $hql = '';
    
    $listeField = $entity->getFields();
    
        //Pour chaque champs contenu dans l'entité
    foreach($listeField as $field)
    {
      //On ne cree pas les champs qui sont des liaisons externes sur des tables associatives
      if($field->isAssociateKEY())
        continue;
    
      if(!empty($hql))
      {
        $hql .= ' , ';
      }
      
      $hql .= ' '.$field->getName().' ';
      
      switch($field->getType())
      {
        case CAST::$STRING : $hql .= 'C'; 
          if($field->getSize() != "" )
          {$hql.= " (".$field->getSize().") ";} break;
        
        case CAST::$INTEGER : $hql .= 'I'; 
          if($field->getSize() != "" )
          {$hql.= " (".$field->getSize().") ";} break;
        
        case CAST::$NUMERIC : $hql .= 'N'; 
          if($field->getSize() != "" )
          {$hql.= " (".$field->getSize().") ";} break;
        
        case CAST::$BUFFER : $hql .= 'X'; break;

        case CAST::$DATE : $hql .= 'D'; break;

        case CAST::$TIME : $hql .= 'T'; break;   

        case CAST::$TS : $hql .= 'I (10) '; break; //workaround for the real timestamp missing in ADODBLITE
      }
      
      if($field->isPrimaryKEY())
      {
        $hql .= ' KEY ';
      }
            
    }
    
        //Trace de débug
    Trace::info($hql);
    
    return $hql;
  }
    /**
    * Créé une table en fonction des informations stockée dans une entité. 
    * 
    *  Créé également la séquence associée.
    *  
    *   Le besoin classique est la création d'une table de bdd à partir d'une entité
    *   exemple rapide d'une entité Client : 
    * <code>
    * 
    * class Client extends Entity
    * {
    *    public function __construct()
    *    {
    *        parent::__construct('client', 'monModule');
    *        
    *        $this->add(new Field('client_id'  , CAST::$INTEGER,null, null, KEY::$PK   , null    
    *                               , new mHTML_FIELD_IDENTIFIANT() , NULLABLE::$FALSE));
    * 
    *        $this->add(new Field('nom'        , CAST::$STRING ,  32, null, null        , null    
    *                               , new mHTML_FIELD_TEXT()        , NULLABLE::$FALSE));
    * 
    *        $this->add(new Field('prenom'     , CAST::$STRING ,  32, null, null        , null   
    *                                , new mHTML_FIELD_TEXT()        , NULLABLE::$FALSE));
    *    }
    * }
    * </code>
    * 
    *  Le meilleur moyen de générer la table 'client' est de faire appel à la méthode  createTable(). 
    * 
    * Exemple
    * 
    * <code>
    *   $client = new Client();
    *   Core::createTable($client);
    * </code>
    * 
    *  Ce simple code va automatiquement créer la table selon les paramètres définit dans l'entité Client soit
    *    3 colonnes dont 1 numérique avec une séquence et deux chaines de caractères
    * 
    * 
    *  A noter l'appel à la fonction initTable() de l'entité. Si elle est définit 
    *   dans l'entité passée en paramètre, cela permet à moindre coût d'initaliser la table 
    *   après création avec un jeu de valeur prédéfinit.
    * 
    * @param Entity l'entité servant de modèle.
    */
  public static final function createTable(Mmmfs &$module, Entity &$entityParam)
  {
    $gCms = cmsms();
    
    $db = $gCms->GetDb();
    $taboptarray = array( 'mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
    $dict = NewDataDictionary( $db );

        //Appel aux méthodes de l'API de adodb pour créer effectivement la table.
    $sqlarray = $dict->CreateTableSQL($entityParam->getDbname(), 
                                            Core::getFieldsToHql($entityParam),
                                            $taboptarray);
                                            
    $result = $dict->ExecuteSQLArray($sqlarray);
    
    if ($result === false)
    {
       echo "Database error durant la creation de la table pour l'entité " . $entityParam->getName() ;
       exit;
    }
       
    Trace::debug("createTable : ".print_r($sqlarray));
    
    //Optionnel : créera une séquence associee
    if($entityParam->getSeqname() != null){$db->CreateSequence($entityParam->getSeqname());}
    
    //On initialise la table.
    $entityParam->initTable($module);
  }
    
    
    /**
    * Supprime une table de la base de donnée
    * 
    * La fonction récupère le nom de la table à supprimer depuis le modèle
    *    de l'entité et effectue un "drop table" sql
    * 
    * Supprime la séquence associée si elle existe
    * 
    * @param Entity l'entité servant de modèle
    */
  public static final function dropTable(Mmmfs &$module, Entity &$entityParam)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    
    $dict = NewDataDictionary( $db );
    
    $sqlarray = $dict->DropTableSQL($entityParam->getDbname());
    $dict->ExecuteSQLArray($sqlarray);
    
    //Optionnel : supprimera une sequence associee
    if($entityParam->getSeqname() != null){$db->DropSequence($entityParam->getSeqname());}
  }  
  
    /**
    * Effectue une modification sur la table de l'entité passée en paramètre
    * 
    *   exemple : pour deux requêtes sur la table de l'entité Client :
    * 
    * <code>        
    *   ALTER TABLE ` table de l'entité Client ` ADD `newColumn` INT NOT NULL 
    *   ALTER TABLE ` table de l'entité Client ` DROP `oldColumn` 
    * </code>
    * 
    *  le code sera celui ci :
    * 
    * <code>
    *       $client = new Client();
    *       Core::alterTable($client, "ADD `newColumn` INT NOT NULL");
    *       Core::alterTable($client, "DROP `oldColumn`");
    * </code>
    *   
    * 
    * @param Entity l'entité servant de modèle
    * @param string la requête sql à passer. 
    */
  public static final function alterTable(Entity &$entityParam, $sql)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
        
    $queryAlter = "ALTER TABLE ".$entityParam->getDbname()." ".$sql;    
    $result = $db->Execute($queryAlter);
    if ($result === false){die("Database error durant l'alter de la table $entityParam->getDbname()!");}
  }
    
    /**
    * Effectue une série d'Insert en base
    * 
    *  Le second paramètre doit respecter ce schéma. 
    * 
    * Exemple d'une insertion de 3 lignes pour un client (client_id, nom, prenom) 
    *   avec prénom facultatif et client_id une clé primaire avec une séquence.
    * 
    * <code>
    *       $tableau = array();
    *       $tableau[] = array('prenom'=>'', 'nom'=>'Dupont');
    *       $tableau[] = array('nom'=>'Durant');
    *       $tableau[] = array('prenom'=>'John', 'nom'=>'Doe');
    * 
    *       $client = new Client(); 
    * 
    *       Core::insertEntity($client, $tableau);
    * </code>
    * 
    *   Notez que dans l'exemple on ne précise <b>JAMAIS</b> la clé primaire pour une insertion, elle sera 
    *       determinée par le système lui même. Dans le cas ou vous souhaitez insérer votre propre identifiant, 
    *       veillez toujours à vérifier que la séquence ne sera pas en décalage !
    *                                        
    * @param Entity l'entité servant de modèle
    * @param array le tableau contenant les données à insérer.
    * @return array la liste des Id créées.
    */
  public static final function insertEntity(Mmmfs &$module, Entity &$entityParam, $rows)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $listeField = $entityParam->getFields();
                
    $sqlReady = false;
    
    //Tableau de retour contiendra les clés crées
    $arrayKEY = array();
    
    $queryInsert = 'INSERT INTO '.$entityParam->getDbname().' (%s) values (%s)';
    
    $str1 = "";
    $str2 = "";
    foreach($listeField as $field)
    {
    
      if($field->isAssociateKEY())
        continue;
    
      if(!empty($str1))
      {
        $str1 .= ',';
        $str2 .= ',';
      }
      $str1 .= ' '.$field->getName().' ';
      $str2 .= '?';
    }
    
    foreach($rows as $row)
    {
      $params = array();
                   
      //On verifie que toutes les valeurs necessaires sont transmises
      foreach($listeField as $field)
      {
        if($field->isAssociateKEY())
          continue;
        
        //Champs vide mais pas une cle automatique et ni un champs spécial
        if(!$field->isPrimaryKEY() 
          && !$field->isNullable() 
          && (!isset($row[$field->getName()]) || (empty($row[$field->getName()]) && $row[$field->getName()] !== 0))
          && !($field instanceof Field_SPE))
        {
          throw new Exception('la valeur du champs '.$field->getName().' de la classe '.$entityParam->getName().' est manquante');
        }

         //On génère une nouvelle cléf pour toutes les clé primaire
        if($field->isPrimaryKEY() && empty($row[$field->getName()]))
        {
          //Nouvelle cle
          $row[$field->getName()] = $db->GenID($entityParam->getSeqname());
          $arrayKEY[] = $row[$field->getName()];
        }
        
        $val = null;
        if(isset($row[$field->getName()]))
        {
          $val = $row[$field->getName()];
        }

        $params[] = Core::FieldToDBValue($val, $field->getType());
        
      }
      
      $sqlReady = true;
      
      //Exécution
      $db->debug = true;
      
        Trace::debug("insertEntity : ".sprintf($queryInsert, $str1, $str2));
      
      $result = $db->Execute(sprintf($queryInsert, $str1, $str2), $params);
      if ($result === false)
      {
        echo print_r($params, true);
        echo "<br/><br/>";
        echo sprintf($queryInsert, $str1, $str2);
        echo "<br/><br/>";
        //TODO : réussir à afficher correctement les traces SQL
        Trace::error($db->ErrorMsg());
        die("Database error durant l'insert!".$db->ErrorMsg());
      }
      
      if($entityParam->isIndexable())
      {  
        // $modops = cmsms()->GetModuleOperations();
        // Indexing::setSearch($modops->GetSearchModule());
        Indexing::AddWords($module->getName(), Core::SelectById($entityParam,$arrayKEY[0]));
      }
    }
    
    return $arrayKEY;
    
  }

    
    /**
    *  Effectue une série d'Update en base
    * 
    *  Le second paramètre doit respecter ce schéma. 
    * 
    * Exemple d'une mise à jour de 3 lignes pour un client (client_id, nom, prenom) 
    *   avec prénom facultatif et client_id une clé primaire avec une séquence.
    * 
    * <code>
    *       $tableau = array();
    *       $tableau[] = array('client_id'=>1, 'prenom'=>null, 'nom'=>'Dupont');    <-- met à null le champs prenom
    *       $tableau[] = array('client_id'=>2, 'nom'=>'Durant');                    <-- met à jour uniquement le nom
    *       $tableau[] = array('client_id'=>3, 'prenom'=>'John', 'nom'=>'Doe');     <-- met à jour les deux champs
    * 
    *       $client = new Client(); 
    * 
    *       Core::updateEntity($client, $tableau);
    * </code>
    * 
    * @param Entity l'entité servant de modèle
    * @param array le tableau contenant les données à mettre à jour.
    */
  public static final function updateEntity(Mmmfs $module, Entity &$entityParam, array $rows)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $listeField = $entityParam->getFields();
   
    
    foreach($rows as $row)
    {
      $str = "";
      $where = '';
      $params = array();
      
      //Nettoyage des eventuelles valeurs pourries transmises
      foreach($row as $KEY=>$value)
      {
        if(empty($listeField[$KEY]))
        {
          unset($row[$KEY]);
        }
      }      
      
      $hasKEY = false;
      //On verifie que toutes les valeurs necessaires sont transmises
      foreach($listeField as $field)
      {
        //Si le champs vide est une cle : erreur
        if($field->isPrimaryKEY())
        {
          if(empty($row[$field->getName()]))
          {
            throw new Exception('l\'id n\'est pas fournie : '.$field->getName());
          } 
          
          $where = ' WHERE '.$field->getName().' = ?';
          $hasKEY = true;
          $KEY = $row[$field->getName()];
          
        }
        
        //Si n'est pas définit dans les lignes à mettre à jour, on ne met simplement pas à jour
        if(!isset($row[$field->getName()]))
        {
          continue;
        }
        
        if(empty($row[$field->getName()]) && $field->isNullable())
        {
                    //Nothing to do
        }
        
        //Champs associatif : on passe
        if(empty($row[$field->getName()]) && $field->isAssociateKEY())
        {
          continue;
        }
        
        if(!empty($str))
        {
          $str .= ',';
        }
        
        $str .= ' '.$field->getName().' = ? ';
        
        $params[] = Core::FieldToDBValue($row[$field->getName()], $field->getType());
        
      }
      
      if($hasKEY)
      {
        $params[] = $KEY;
      }
      

      $queryUpdate = 'UPDATE '.$entityParam->getDbname().' SET '.$str.$where;

      
      //Excecution
      $result = $db->Execute($queryUpdate, $params);
      if ($result === false){die("Database error durant l'update!");}
      if($entityParam->isIndexable())
      {  
        Indexing::UpdateWords($module->getName(), Core::SelectById($entityParam,$KEY));
      }
    }
  }
  
    
    /**
    * Effectue une série de DELETE dans la table de l'entité
    *   
    * Exemple d'une suppression unique : 
    * 
    * <code>
    *       $client = new Client(); 
    * 
    *       Core::deleteByIds($client, array(1);
    * </code>
    * 
    *  Exemple d'une suppression multiple : 
    * 
    * <code>
    *       $tableau = array();
    *       $tableau[] = 1;
    *       $tableau[] = 2;
    *       $tableau[] = 3;
    * 
    *       $client = new Client(); 
    * 
    *       Core::deleteByIds($client, $tableau);
    * </code>
    * 
    * @param Entity l'entité servant de modèle     
    * @param array un tableau contenant les ids à supprimer
    */
  public static final function deleteByIds(Mmmfs $module, Entity &$entityParam, $ids)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $listeField = $entityParam->getFields();
    
    foreach($listeField as $field)
    {
      if(!$field->isPrimaryKEY())
      { 
        continue;
      }
      $type = $field->getType();
      $name = $field->getName();
    }  
    
    $where = '';
    foreach($ids as $sid)
    {
      if(!empty($where))
      {
        $where .= ' OR ';
      }
      
      $where .= $name.' = ?';
      $params[] = Core::FieldToDBValue($sid, $type);  
    }
    
    
    $queryDelete = 'DELETE FROM '.$entityParam->getDbname().' WHERE '.$where;
    
    //Excecution
    $result = $db->Execute($queryDelete, $params);
    if ($result === false){die("Database error durant la suppression!");}
    
    if($entityParam->isIndexable())
    {  
      // $modops = cmsms()->GetModuleOperations();
      // if(method_exists($modops,"GetSearchModule"))
      // {
        // Indexing::setSearch($modops->GetSearchModule());
      // } else
      // {
        // die("ko");
      // }
      foreach($ids as $sid)
      {
        Indexing::DeleteWords($module->getName(), $entityParam, $sid);
      }
      
    }
  }
  
    
    /**
    * Retourne le nombre d'occurance dans la table représentant l'entité
    * 
    * @param Entity l'entité servant de modèle
    * @return int le nombre d'occurance présente en table   
    */
  public static final function countAll(Entity &$entityParam)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    
    $querySelect = 'Select count(*) FROM '.$entityParam->getDbname();
    
    Trace::debug("countAll : ".$querySelect);
      
    $compteur= $db->getOne($querySelect);
    if ($compteur === false){die("Database error durant la requete count(*)!");}
    
    return $compteur;
  }
  
    /**
    * Retourne l'intégralité des occurences de la table représentant l'entité
    * 
    * @param Entity l'entité servant de modèle
    * @return array<Entity> la liste des Entités 
    */
  public static final function selectAll(Entity &$entityParam)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    
    $querySelect = 'Select * FROM '.$entityParam->getDbname();
    
        //Si déjà présent en cache, on le retourne 
    if(Cache::isCache($querySelect))
    {
      return Cache::getCache($querySelect);
    }
      
    $result = $db->Execute($querySelect);
    if ($result === false){die("Database error durant la requete par Ids!");}
    
    $entitys = array();
    while ($row = $result->FetchRow())
    {
      $entitys[] = Core::rowToEntity($entityParam, $row);
    }
    
        //On repousse dans le cache le résultat avant de le retourner   
    Cache::setCache($querySelect, null, $entitys);
    
        return $entitys;
  }
  
    
    /**
    * Retourne l'entité trouvée en base à partir de son Id
    * 
    * @param Entity l'entité servant de modèle  
    * @param int l'Id recherché
    * @return Entity l'entité ayant l'Id passé en paramètre ou null
    */
  public static final function selectById(Entity &$entityParam,$id)
  {
    $liste = Core::selectByIds($entityParam, array($id));
        
        if(!isset($liste[0]))
            return null;
        
    return $liste[0];
  }
  
    /**
    * Retourne la liste des entités trouvées en base à partir de leur Ids
    * 
    * @param Entity l'entité servant de modèle  
    * @param array le tableau contenant les Ids recherchés
    * @return array<Entity> un tableau contenant toutes les entités trouvées ou vide si aucun résultat
    */
  public static final function selectByIds(Entity &$entityParam, $ids)
  {
    if(count($ids) == 0)
      return array();
        
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $listeField = $entityParam->getFields();
    
    $where = "";
        
    foreach($listeField as $field)
    {
    
      if(!$field->isPrimaryKEY())
      { 
        continue;
      }
      
      foreach($ids as $id)
      {
      
        if(!empty($where))
        {
          $where .= ' OR ';
        }
              
        $where .= $field->getName().' = ?';
        
        $params[] = Core::FieldToDBValue($id, $field->getType());
      }
    }
    
    $querySelect = 'Select * FROM '.$entityParam->getDbname().' WHERE '.$where;
    
        //Si déjà présent en cache, on le retourne
    if(Cache::isCache($querySelect,$params))
    {
      return Cache::getCache($querySelect,$params);
    }
    
    //Excecution
    $result = $db->Execute($querySelect, $params);
    if ($result === false){die("Database error durant la requete par Ids!");}
    
    $entitys = array();
    while ($row = $result->FetchRow())
    {
      $entitys[] = Core::rowToEntity($entityParam, $row);
    }
        
        //On repousse dans le cache le résultat avant de le retourner
    Cache::setCache($querySelect,$params, $entitys);
        
    return $entitys;
    
  }
  
    /**
    *  Permet de rechercher une liste d'entité à partir d'une série de critère de selection 
    * 
    * Exemple : rechercher les clients prénommé 'Roger' (casse insensible)
    * 
    *  <code>
    *       $client = new Client();
    * 
    *       $exemple = new Exemple();
    *       $exemple->addCritere('prenom', TypeCritere::$EQ, array('roger'), true);
    * 
    *       Core::selectByExemple($client, $exemple);
    * </code>
    * 
    *  Exemple : rechercher les clients ayant un Ids >= 90 
    * 
    * <code>
    *       $client = new Client();
    * 
    *       $exemple = new Exemple();
    *       $exemple->addCritere('client_id', TypeCritere::$GTE, array(90));
    * 
    *       Core::selectByExemple($client, $exemple);
    * </code>
    * 
    * NOTE : EQ => <b>EQ</b>uals, GTE => <b>G</b>reater <b>T</b>han or <b>E</b>quals (supérieur ou égal é)
    * 
  * NOTE 2 : Les critères s'ajoutent sans soucis pour cumuler les conditions de recherche
    * 
    * @param Entity l'entité servant de modèle 
    * @param Exemple l'Objet Exemple préalablement remplis
    * 
    * @see Exemple
    * @see TypeCritere
    */
  public static final function selectByExemple(Entity &$entityParam, Exemple $exemple)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $listeField = $entityParam->getFields();
    
    $criteres = $exemple->getCriteres();
    $select = "select * from ".$entityParam->getDbname();
    $hql = "";
    $params = array();
    //  die("spp,".count($criteres));
    foreach($criteres as $critere)
    {
      if(!empty($hql))
      {
        $hql .= ' AND ';
      }
      
      if(empty($hql))
      {
        $hql .= ' WHERE ';
      }

      $filterType =  $listeField[$critere->fieldname]->getType();
      
            //Critéres avec 1 seul paramètre
      if($critere->typeCritere == TypeCritere::$EQ || $critere->typeCritere == TypeCritere::$NEQ 
        || $critere->typeCritere == TypeCritere::$GT || $critere->typeCritere == TypeCritere::$GTE 
        || $critere->typeCritere == TypeCritere::$LT || $critere->typeCritere == TypeCritere::$LTE 
        || $critere->typeCritere == TypeCritere::$BEFORE || $critere->typeCritere == TypeCritere::$AFTER
        || $critere->typeCritere == TypeCritere::$LIKE || $critere->typeCritere == TypeCritere::$NLIKE)
      {  
        $val = $critere->paramsCritere[0];
        
        if($critere->typeCritere == TypeCritere::$LIKE || $critere->typeCritere == TypeCritere::$NLIKE)
        {
          $val.= '%';
        }
        
        $params[] = Core::FieldToDBValue($val, $filterType); 
        $hql .= $critere->fieldname.$critere->typeCritere.' ? ';
        continue;
      }
      
            //Sans paramètres
      if($critere->typeCritere == TypeCritere::$NULL || $critere->typeCritere == TypeCritere::$NNULL)
      {  
        $hql .= $critere->fieldname.$critere->typeCritere;
        continue;
      }
      
            //deux paramètres
      if($critere->typeCritere == TypeCritere::$BETWEEN)
      {  
        $params[] = Core::FieldToDBValue($critere->paramsCritere[0], $filterType); 
        $params[] = Core::FieldToDBValue($critere->paramsCritere[1], $filterType); 
        $hql .= $critere->fieldname.$critere->typeCritere.' ? AND ?';
        continue;
      }
      
            // N paramètres
      if($critere->typeCritere == TypeCritere::$IN || $critere->typeCritere == TypeCritere::$NIN)
      {
        $hql .= ' ( ';
        $second = false; 
        foreach($critere->paramsCritere as $param)
        {
          if($second)
          {
            $hql .= ' OR ';
          }
          
        //  echo "<br/>param : ".$param."* ".$filterType." * ".Core::FieldToDBValue($param, $filterType)."<br/>";
          $params[] = Core::FieldToDBValue($param, $filterType); 
          $hql .= $critere->fieldname.TypeCritere::$EQ.' ? ';
          
          $second = true;
        }
        $hql .= ' )';
        continue;
      }
      
      //Traitement spécifique
      if($critere->typeCritere == TypeCritere::$EMPTY)
      {
        $hql .= ' ( '.$critere->fieldname .' is null || ' . $critere->fieldname . "= '')";
        continue;
      }
      if($critere->typeCritere == TypeCritere::$NEMPTY)
      {
        $hql .= ' ( '.$critere->fieldname .' is not null && ' . $critere->fieldname . "!= '')";
        continue;
      }
                     
      throw new Exception("Le Critere $critere->typeCritere n'est pas encore pris en charge");
    }
    $queryExemple = $select.$hql;
    
    $debug = print_r($params, true);
    Trace::info("SelectByExemple : ".$queryExemple."   ".$debug);
    
    $result = $db->Execute($queryExemple, $params);
    
    if ($result === false){die($db->ErrorMsg().Trace::error("Database error durant la requete par Exemple!"));}
    
    Trace::info("SelectByExemple : ".$result->RecordCount()." resultat(s)");
    
    $entitys = array();
    while ($row = $result->FetchRow())
    {
      $entitys[] = Core::rowToEntity($entityParam, $row);
    }
    
    
    
    return $entitys;
    
  }
  
   /**
    *  Permet de rechercher une liste d'entité à partir d'une série de critère de selection 
    *       et de les supprimer de la table
    * 
    * Exemple : supprimer les clients prénommé 'Roger' (casse insensible)
    * 
    *  <code>
    *       $client = new Client();
    * 
    *       $exemple = new Exemple();
    *       $exemple->addCritere('prenom', TypeCritere::$EQ, array('roger'), true);
    * 
    *       Core::deleteByExemple($client, $exemple);
    * </code>
    * 
    *  Exemple : supprimer les clients ayant un Ids >= 90 
    * 
    * <code>
    *       $client = new Client();
    * 
    *       $exemple = new Exemple();
    *       $exemple->addCritere('client_id', TypeCritere::$GTE, array(90));
    * 
    *       Core::deleteByExemple($client, $exemple);
    * </code>
    * 
    * NOTE : EQ => <b>EQ</b>uals, GET => <b>G</b>retter or <b>E</b>quals <b>T</b>han (plus grand ou égal é)
    * 
    * 
    * @param Entity l'entité servant de modèle 
    * @param Exemple l'Objet Exemple préalablement remplis
    * 
    * @see Exemple
    * @see TypeCritere
    */
  public static final function deleteByExemple(Entity &$entityParam, Exemple $Exemple)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $listeField = $entityParam->getFields();
    
    $criteres = $Exemple->getCriteres();
    $delete = "delete from ".$entityParam->getDbname();
    $hql = "";
    $params = array();
    foreach($criteres as $critere)
    {
      if(!empty($hql))
      {
        $hql .= ' AND ';
      }
      
      if(empty($hql))
      {
        $hql .= ' WHERE ';
      }

      echo $critere->fieldname."-";

      $filterType = $listeField[$critere->fieldname]->getType();
      
            // 1 paramètre  
      if($critere->typeCritere == TypeCritere::$EQ || $critere->typeCritere == TypeCritere::$NEQ 
        || $critere->typeCritere == TypeCritere::$GT || $critere->typeCritere == TypeCritere::$GTE 
        || $critere->typeCritere == TypeCritere::$LT || $critere->typeCritere == TypeCritere::$LTE 
        || $critere->typeCritere == TypeCritere::$BEFORE || $critere->typeCritere == TypeCritere::$AFTER
        || $critere->typeCritere == TypeCritere::$LIKE || $critere->typeCritere == TypeCritere::$NLIKE)
      {  
        $params[] = Core::FieldToDBValue($critere->paramsCritere[0], $filterType); 
        $hql .= $critere->fieldname.$critere->typeCritere.' ? ';
        continue;
      }
      
            // 0 paramètre
      if($critere->typeCritere == TypeCritere::$NULL || $critere->typeCritere == TypeCritere::$NNULL)
      {  
        $hql .= $critere->fieldname.$critere->typeCritere;
        continue;
      }
      
            // 2 paramètres  
      if($critere->typeCritere == TypeCritere::$BETWEEN)
      {  
        $params[] = Core::FieldToDBValue($critere->paramsCritere[0], $filterType); 
        $params[] = Core::FieldToDBValue($critere->paramsCritere[1], $filterType); 
        $hql .= $critere->fieldname.$critere->typeCritere.' ? AND ?';
        continue;
      }
            
            // N paramètres
            if($critere->typeCritere == TypeCritere::$IN || $critere->typeCritere == TypeCritere::$NIN)
            {
                $hql .= ' ( ';
                $second = false; 
                foreach($critere->paramsCritere as $param)
                {
                    if($second)
                    {
                        $hql .= ' OR ';
                    }
                    $params[] = Core::FieldToDBValue($param, $filterType); 
                    $hql .= $critere->fieldname.TypeCritere::$EQ.' ? ';
                    
                    $second = true;
                }
                $hql .= ' )';
                continue;
            }                        
      
      throw new Exception("Le Critere $critere->typeCritere n'est pas encore pris en charge");
    }
    $queryExemple = $delete.$hql;
                                    
    
    $result = $db->Execute($queryExemple, $params);
    if ($result === false){die("Database error durant la requete par Exemple!");}
  }
      
    /**
    * transforme un tableau de valeur en une entité compléte
    * 
    *   Le tableau doit être sous la forme d'un tableau associatif à une seule dimension
    * 
    * Exemple :
    * 
    * <code>
    *       $tableau1 = array('client_id'=>1, 'nom'=>'Dupont');       
    *       $tableau2 = array('client_id'=>2, 'nom'=>'Durand', 'prenom'=>'Joe');       
    *   
    *       $client = new Client();
    * 
    *       $client1 = Core::rowToEntity($client, $tableau1);
    *       $client2 = Core::rowToEntity($client, $tableau2);
    * 
    *       echo $client1->get('prenom'); //retournera null
    *       echo $client2->get('Prenom'); //retournera Joe
    * 
    * </code>
    *         
    * @param Entity l'entité servant de modèle
    * @param array le tableau contenant les données
    */
  public static final function rowToEntity (Entity &$entityParam, $row)
  {
    
    Trace::debug("rowToEntity : ".print_r($row,true)."<br/><br/><br/>");
    $listeField = $entityParam->getFields();
    
    $newEntity = clone $entityParam;
    foreach($listeField as $field)
    {
      if(!$field->isAssociateKEY())
      {
        $newEntity->set($field->getName(),Core::dbValueToField($row[$field->getName()], $field->getType()));
      } 
    }
    return $newEntity;  
  }
  
    /**
    * Transforme une donnée issue de PHP en une donnée pour SQL
    * 
    * @param mixed la donnée issue de PHP
    * @param mixed un champs de la classe static CAST
    * 
    * @see CAST
    */
  public static final function FieldToDBValue($data, $type)
  {
    switch($type)
    {
      case CAST::$STRING : return $data;
      
      case CAST::$INTEGER : return $data;
      
      case CAST::$NUMERIC : return $data;
      
      case CAST::$BUFFER : return $data;
      
      case CAST::$DATE : return cmsms()->GetDb()->DBDate($data);       
      
      case CAST::$TIME : return cmsms()->GetDb()->DBDate($data);     

      case CAST::$TS : return $data;  
    }
  }
  
    /**
     * Transforme une donnée issue de SQL en une donnée pour PHP
    * 
    * @param mixed la donnée issue de la base de donnée
    * @param mixed un champs de la classe static CAST
    * 
    * @see CAST
    */
  public static final function dbValueToField($data, $type)
  {
    switch($type)
    {
      case CAST::$STRING : return $data;
      
      case CAST::$INTEGER : return $data;
      
      case CAST::$NUMERIC : return $data;
      
      case CAST::$BUFFER : return $data;
      
      case CAST::$DATE : return cmsms()->GetDb()->UnixTimeStamp($data);
      
      case CAST::$TIME : return cmsms()->GetDb()->UnixTimeStamp($data);

      case CAST::$TS : return $data;

    }
  }
    
    /**
    * Retourne les entités B pouvant être associé à une Entité A
    * 
    * Dans le cas de clé étrangère présente dans une entité, par exemple un article de blog et tous ses tags associés,
    *    il est intéressant de pouvoir récupérer l'intégralité des tags lié et/ou pouvant être lié à notre article de blog sélectionné
    * 
    * La fonction va parcourir L'entité Article , prendre le champs passé en AssociateKey et aller faire une requête de selection sur l'entité Tag
    * 
    * pour rappel une clé associée est configurée ainsi dans Mmmfs :
    * 
       * <code>
    *    class Article extends mEntity
    *    {
    * *       public function __construct()
    *        {
    *            parent::__construct('article','monprojet');
    *            
    *             $this->add(new Field('article_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('tags' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.article_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()  
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *    class Tag extends mEntity
    *    {
    *       public function __construct()
    *        {
    *            parent::__construct('tag','monprojet');
    *            
    *             $this->add(new Field('tag_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('articles' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.billet_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()    
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *   class ArticleBillet extends mEntityAssociation
    *   {
    *        public function __construct()
    *        {
    *            parent::__construct('articlebillet','monprojet');
    *            
    *            $this->add(new Field('article_id'
    *                                   , CAST::$INTEGER
    *                                   ,null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Article.tags'    
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *            $this->add(new Field('tag_id'        
    *                                   , CAST::$INTEGER
    *                                   , null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Tag.articles'  
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *
    *        }    
    *    }
    * </code>
    * 
    * @param Entity l'entité servant de modèle  
    * @param string le nom du champs de l'entité qui servira de point de départ pour la recherche
    * @return array<Entity> la liste des entités associées et pouvant être associée à ce champs.
    */
  public static final function getEntitysAssociable(Entity &$entityParam,$fieldname)
  {
    $field = $entityParam->getFieldByName($fieldname);
    if($field->getKEYName() == '')
        throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
        
    $cle = explode('.',$field->getKEYName(),2);
    
    eval('$entity = new '.$cle[0].'();');
                                       
    
    $listField = $entity->getFields();
    foreach($listField as $field)
    {
      if($field->getKEYName() == '')
        throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
            
      $cle = explode('.',$field->getKEYName(),2);
      
      if(strtolower($cle[0]) == $entityParam->getName())
        continue;
          
      
      //Evaluation de la eclass en cours
      eval('$entity = new '.$cle[0].'();');
      
      $liste = Core::selectAll($entity);
      
      return $liste;
    }    
        
  }

  /**
    * Retourne les entités B qui sont déjà associés à une Entité A
    * 
    * Dans le cas de clé étrangère présente dans une entité, par exemple un article de blog et tous ses tags associés,
    *    il est intéressant de pouvoir récupérer l'intégralité des tags liés à notre article de blog sélectionné
    * 
    * La fonction va parcourir L'entité Article , prendre le champs passé en AssociateKey et aller faire une requête de selection sur l'entité Tag
    *     en prenant en compte l'identifiant de l'article en cours
    * 
    * pour rappel une clé associée est configurée ainsi dans Mmmfs :
    * 
    * <code>
    *    class Article extends mEntity
    *    {
    * *       public function __construct()
    *        {
    *            parent::__construct('article','monprojet');
    *            
    *             $this->add(new Field('article_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('tags' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.article_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()  
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *    class Tag extends mEntity
    *    {
    *       public function __construct()
    *        {
    *            parent::__construct('tag','monprojet');
    *            
    *             $this->add(new Field('tag_id' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null 
    *                           , mKEY::$PK    
    *                           , null              
    *                           ,  new mHTML_FIELD_IDENTIFIANT()       
    *                           , NULLABLE::$FALSE)
    *             [...]
    *             $this->add(new Field('articles' 
    *                           , CAST::$INTEGER
    *                           , null
    *                           , null
    *                           , mKEY::$AK    
    *                           , 'ArticleBillet.billet_id' <-- lien vers l entité Associée ArticleBillet
    *                           , new mHTML_FIELD_ASSOCIATE()    
    *             [...]
    *        }
    *        
    *
    *    }
    * 
    *   class ArticleBillet extends mEntityAssociation
    *   {
    *        public function __construct()
    *        {
    *            parent::__construct('articlebillet','monprojet');
    *            
    *            $this->add(new Field('article_id'
    *                                   , CAST::$INTEGER
    *                                   ,null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Article.tags'    
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *            $this->add(new Field('tag_id'        
    *                                   , CAST::$INTEGER
    *                                   , null
    *                                   , null
    *                                   , mKEY::$FK
    *                                   , 'Tag.articles'  
    *                                   , new mHTML_FIELD_NONE()    
    *                                   , NULLABLE::$FALSE));
    *
    *        }    
    *    }
    * </code>
    * 
    * @param Entity l'entité servant de modèle  
    * @param string le nom du champs de l'entité qui servira de point de départ pour la recherche
    * @param mixed l'identifiant de l'entité en cours
    * @return array<Entity> la liste des entités effectivement associées à ce champs.
    */
  public static final function getEntitysAssocieesLiees(Entity &$entityParam, $fieldname, $entityId)
  {
    Trace::debug("getEntitysAssocieesLiees : ".$entityParam->getName()." ".$fieldname." ".$entityId);
    
    $field = $entityParam->getFieldByName($fieldname);
    
    if($field->getKEYName() == '')
        throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
      
    $cle = explode('.',$field->getKEYName(),2);
    
    eval('$entity = new '.$cle[0].'();');
                                                              
    $exemple = new Exemple();    
    $exemple->addCritere($cle[1],TypeCritere::$EQ,array($entityId));
    $assocs = Core::selectByExemple($entity, $exemple);
    
    $listField = $entity->getFields();
    foreach($listField as $field)
    {
      if($field->getKEYName() == null || $field->getKEYName() == '')
        throw new Exception("Le champs $fieldname ne possede aucune cle etrangere associee pour la class ".$entityParam->getName());
      
      $cle = explode('.',$field->getKEYName(),2);
      
      if(strtolower($cle[0]) == $entityParam->getName())
        continue;                              
                
      $ids = array();
      foreach($assocs as $assoc)
      {
        $ids[] = $assoc->get($field->getName());
      }                                                        
      
      $cle = explode('.',$field->getKEYName(),2);
            
      //Evaluation de la eclass en cours
      eval('$entity = new '.$cle[0].'();');
      
      $liste = Core::selectByIds($entity, $ids);
      
      Trace::debug("getEntitysAssocieesLiees : "."resultat : ".count($liste));
      
      return $liste;
    }    
  }
  
    /**
    * Vérifie dans toutes les entity existantes, qu'aucune ne possède encore une liaison vers 
    *   l'entité passée en paramètre avec l'Id passé en paramètre
    * 
    *  Cette fonction est utilisée avant une suppression où l'on souhaites s'assurer qu'aucune 
    *     autre entité n'est encore liée'
    * 
    * @param Mmmfs le module en cours
    * @param Entity l'entité servant de modèle
    * @param mixed l'identifiant de la ligne à vérifier.
    */
  public static final function verifIntegrity(Mmmfs $module, Entity &$entity, $sid)
  {
    $listeEntitys = MyAutoload::getAllInstances($module->getName());
  
    foreach($listeEntitys as $key=>$anEntity)
    {
      if($anEntity instanceOf EntityAssociation)
        continue;
        
      foreach($anEntity->getFields() as $field)
      {
        if($field->isAssociateKEY())
        {
          continue;
        }
        
        if($field->getKEYName() != null)
        {
          $vals = explode('.',$field->getKEYName(),2);
          
          if(strtolower ($vals[0]) == strtolower ($entity->getName()))
          {
            $Exemple = new Exemple();
            $Exemple->addCritere($field->getName(), TypeCritere::$EQ, array($sid));
            $entitys = Core::selectByExemple($anEntity, $Exemple);
            if(count($entitys) > 0)
            {
              return "La ligne &agrave; supprimer est encore utilis&eacute;e par &laquo; ".$anEntity->getName()." &raquo;";
            }
          }
        }
      }
    }
    
    return;
  
  }

    /**
    * Permet de réaliser des recherches en profondeur sur une succession de lien inter-entité. 
    * 
    *   Dans le cas ou une entité est linée à une seconde entité, qui a sont tour est liée à une 3eme, 
    *   il est possible de déterminer un chemin entre elles.
    * 
    * Exemple : 
    *   Une commande possède un lien vers un client via le numero de client
    *   Un client possède un lien vers une adresse via son id Adresse (ça permet de gérer des adresses multiples)
    *   Une adresse possède un lien vers une ville via son code postal
    * 
    *  Si je souhaites connaitre l'intégralité des commandes passée pour la ville de Lille, je pourrais faire :
    * 
    * <code>
    *  $villes = //Traitement de recherche d'une ville dont le libellé = Lille
    *  foreach($villes as $ville)
    *  {
    *       $adresses = //Traitement de recherche d'une adresse dont le code postal = $ville->get('codepostal')
    *       foreach($adresses as $adresse)
    *       {
    *           $clients = //Traitement de recherche d'un client possèdant l'ID adresse = $adresse->get('adresse_id')
    *           foreach($clients as $client)
    *           {
    *               $commandes =  //Traitement de recherche d'une commande possèdant le numeroclient = $client->get('numeroclient')   
    *           }                   
    *       }                  
    *  }
    * 
    *  </code>
    * 
    *  Cette fonction permet de simplifier énormement la chose puisqu'il suffit de faire : 
    * 
    * <code>
    *  $commandes = Core::makeDeepSearch(new Commande(), 'Commande.numeroclient.adresse_id.codepostal.libelle', array('Lille'));
    * </code>
    * 
    *  
    * 
    * @param Entity L'entité servant de point de départ à la recherche
    * @param string le chemin à parcourir, d'entité en entité séparé par un point.
    * @param array la liste des valeurs recherchées
    * 
    */
  public static final function makeDeepSearch(Entity $previousEntity, $cle, $values)
  {    
    TRACE::info("# : "."Start makeDeepSearch() ".$previousEntity->getName()."->".$cle);
    
    if($previousEntity == null)
    {
      
      $newCle = explode('.',$cle,2);
      $previousEntity = $newCle[0];
      $cle = $newCle[1];
      eval('$previousEntity = new '.$previousEntity.'();');
    }
    
    $newCle = explode('.',$cle,2);
    $fieldname = $newCle[0];
    
    //Test de sortie : on a un seul résultat dans $newCle : le champs final
    if(count($newCle) == 1)
    {
      TRACE::info("# : "." count(\$newCle) == 1 , donc sortie ");
      $Exemple = new Exemple;
      $Exemple->addCritere($fieldname, TypeCritere::$IN, $values);
      $entitys = Core::selectByExemple($previousEntity, $Exemple);
      TRACE::info("# : ".count($entitys)." R&eacute;sultat(s) retourn&eacute;s");
      return $entitys;
    } else
    {
      TRACE::info("# : "." poursuite ");
    }
    
    //Récupération de la clé distance pour une FK
    $field = $previousEntity->getFieldByName($fieldname);
    if($field->isForeignKEY() || $field->isAssociateKey())
    {
      $foreignKEY = explode('.',$field->getKEYName(),2);
      eval('$nextEntity = new '.$foreignKEY[0].'();');
    } 

    if($field->isAssociateKey())
    {
      $cle = explode('.',$newCle[1],2);
      $cle = $cle[1];
    } else
    {
      $cle = $newCle[1];
    }

    
    

    
    TRACE::info("# : "." make new recherche : ".$nextEntity->getName() ." , ". $cle);
    
    $entitys = Core::makeDeepSearch($nextEntity, $cle, $values);
    
    if(count($entitys) == 0)
    {
      return array();
    }
    
    if($nextEntity instanceof EntityAssociation)
    {  
      $fields = $nextEntity->getFields();
      $nomFieldSuivit = explode('.',$cle,2);
      $nomFieldSuivit = $nomFieldSuivit[0];
      $nomFieldRetour = "N/A";
      foreach($fields as $afield)
      {
        if($afield->getName() == $nomFieldSuivit)
        {
          continue;
        }
        $nomFieldRetour = $afield;
      }
      
      //die($nomFieldRetour->getName());
    }
    
    $ids = array();
    foreach($entitys as $anEntity)
    {
      TRACE::info("<br/>On a trouv&eacute;  : ".$anEntity->getName()."");
      if($anEntity instanceof EntityAssociation)
      {
        $value = $anEntity->get($nomFieldRetour->getName());
        $ids[] = $value;
        TRACE::info(" valeur assoc : ".$value." pour le champs ".$nomFieldRetour->getName());
      } else
      {
        $value = $anEntity->get($nextEntity->getPk()->getName());
        $ids[] = $value;
        TRACE::info(" valeur id : ".$value);
      }
      
    }
    
    
    $Exemple = new Exemple;
    if($nextEntity instanceof EntityAssociation)
    {
      $Exemple->addCritere($previousEntity->getPk()->getName(), TypeCritere::$IN, $ids);
    } else
    {
      $Exemple->addCritere($fieldname, TypeCritere::$IN, $ids);
    }
    $entitys = Core::selectByExemple($previousEntity, $Exemple);
    
    return $entitys;
  }
  
}

?>