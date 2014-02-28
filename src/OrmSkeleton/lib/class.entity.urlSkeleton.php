<?php
/**
 * A complex example with a component key : an url of a website, a lang and the title/description in this lang.
 *  We don't want to create 2 entries for the same website for the same lang.
 *  We accept to create 2 entries for the same website if it's not with the same lang
 */ 
class UrlSkeleton extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormskeleton','urlskeleton');
		
		$this->add(new OrmField('url'	
			, OrmCAST::$STRING
			, 255	
			, null 		
			, OrmKEY::$PK	
		));
        
		$this->add(new OrmField('lang_iso'	
			, OrmCAST::$STRING
			, 10	
			, null 		
			, OrmKEY::$PK	
		));
		
		$this->add(new OrmField('title'	
			, OrmCAST::$STRING
			, 50	
		));
		
		$this->add(new OrmField('description'	
			, OrmCAST::$BUFFER
            , null
            , TRUE
		));
		
		
		$this->add(new OrmField('comments'	
			, OrmCAST::$NONE
            , null
			, TRUE 		
			, OrmKEY::$AK
			, 'CommentSkeleton'
		));
		
		
		// 1] we want to be sure that the couple url/lang_iso is Unique.
		//    we also want indexing the uuid
		//$this->addIndexes(array('url', 'lang_iso'), true);

	}	
}
?>