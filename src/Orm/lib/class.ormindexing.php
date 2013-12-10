<?php
/**
 * Contains all the CmsMadeSimple's indexing system
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/

/**
 *   Allow a quick and simple indexing system with modules like Search
 *                                                                                       
 *   The most of the work was made from Duketown's work - thank to him
 *   http://forum.cmsmadesimple.org/viewtopic.php?p=261508#p261508                                                                         
 *    
 * 
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/   
final class OrmIndexing
{	
	
	protected function __construct() {
	}
	
	public static final function getSearch()
	{
		$modops = cmsms()->GetModuleOperations();
		return $modops->GetSearchModule();
	}

	/**
    * vide l'indexage 
    * 
    * @param string le nom du module.
    */
	public static final	function DeleteAllWords(OrmEntity $entity)
	{	
		$searchmodule = OrmIndexing::getSearch();
		$searchmodule->DeleteWords($entity->getModuleName(), null, $entity->getName());
	}

   /**
    * vide l'indexage d'un élement en particulier
    * 
    * @param string le nom du module.
    */
	public static final	function DeleteWords(OrmEntity $entity, $sid = null)
	{	
		if($sid == null)
			$sid = $entity->get($entity->getPk()->getName());
		
		$searchmodule = OrmIndexing::getSearch();
		$searchmodule->DeleteWords($entity->getModuleName(), $sid, $entity->getName());
	}
	
	
	public static final	function AddWords($entity)
    {
		$searchmodule = OrmIndexing::getSearch();
		$fields = $entity->getFields();
		
		$content = '';
		foreach($fields as $field)
		{
			$content.= ' '.$entity->get($field->getName());
		}
		
		$sid = $entity->get($entity->getPk()->getName());
		
		//echo $sid.' '.$entity->getName().' '.$content;
		$searchmodule->AddWords($entity->getModuleName(), $sid,$entity->getName(),$content);
    }
	
	public static final	function UpdateWords($entity) {
		OrmIndexing::DeleteWords($entity);
		OrmIndexing::AddWords($entity);
	}

	public static function SearchReindex($moduleName) {
		$entitys = MyAutoload::getAllInstances($moduleName);
		foreach ($entitys as $entity) {
		
			if($entity->isIndexable()) {
			
				OrmIndexing::DeleteAllWords($entity);
				
				$liste = array();
				if(!$entity->isFieldByNameExists(Field_Active::$name)) {
					$liste = OrmCore::selectAll($entity);
				} else {
					$example = new OrmExample();
					$example->addCriteria(Field_Active::$name, OrmTypeCriteria::$EQ, array(1));
					$liste = OrmCore::selectByExample($entity, $example);
				}
				
				foreach($liste as $singleEntity) {
					OrmIndexing::AddWords($singleEntity);
				}
			}
		}
	}

	/**
	 * Called by module Search to display a result
	 */
	function SearchResult(&$module, $id, $returnid, $entityId, $attr = '')
    {	
		$result = array();
		
		/*$mle = MleForMmmfs::getRootUrl();
		if($mle == null)
		{
			$mle='fr';
		}*/
		
		
		$entitys = myAutoload::getAllInstances($module->getName());
		foreach ($entitys as $entity)
		{
			if($entity->isIndexable())
			{		

				if ($attr == $entity->getName())
				{
					$myEntity = OrmCore::selectById($entity, $entityId);
					
					//$currentLangISO = ($mle == 'fr'?'fr_FR':'en_US'); //TODO : se démerder pour récupérer la langue liée au $returnId;
					
					$prettyUrl = '';
					$link = $module->CreateLink($id, 'fiche', $returnid, 
									'', 
									array("eclass"=>$myEntity->getName(), "eclassId"=>$entityId),'',true,null,null,null,$prettyUrl);
				 				    
					$result[0] = $myEntity->getName();
					$result[1] = html_entity_decode($myEntity->getName().' #'.$entityId, ENT_QUOTES, 'UTF-8');
					$result[1] = html_entity_decode($result[1], ENT_QUOTES, 'UTF-8');
					
					
					$result[2] = $link;
				}
			}
		}
		
		return $result;
    }

	
}
?>