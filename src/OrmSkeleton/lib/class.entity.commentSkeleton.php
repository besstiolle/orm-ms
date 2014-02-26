<?php
/**
 * A complex example with a component key : an url of a website, a lang and the title/description in this lang.
 *  We don't want to create 2 entries for the same website for the same lang.
 *  We accept to create 2 entries for the same website if it's not with the same lang
 */ 
class CommentSkeleton extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormskeleton','commentSkeleton');
		
		$this->add(new OrmField('comment_id'	
			, OrmCAST::$INTEGER
			, null	
			, null 		
			, OrmKEY::$PK	
		));
        
		$this->add(new OrmField('text'	
			, OrmCAST::$BUFFER
		));
			
		$this->add(new OrmField('website'
			, OrmCAST::$INHERIT
            , null
            , null
			, OrmKEY::$FK
			, "urlSkeleton" 
			//, "urlSkeleton.lang_iso" 
		));
		
		
		// 1] we want to be sure that the couple url/lang_iso is Unique.
		//    we also want indexing the uuid
		//$this->addIndexes(array('url', 'lang_iso'), true);

	}	
}
?>