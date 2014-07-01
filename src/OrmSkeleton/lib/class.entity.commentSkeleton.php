<?php
/**
 * A complex example with a single primary key linked to an entity with a composite primary key.
 *  One comment is linked to a combinaison of 1 Url and 1 Lang_iso
 *  We also want to retrieve all the comments for 1 Url and 1 Lang_iso
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
			
		// We must create as many FK that their is PK in urlSkeleton
		// http://dev.mysql.com/doc/refman/5.6/en/create-table-foreign-keys.html
		$this->add(new OrmField('url'
			, OrmCAST::$INHERIT
            , null
            , null
			, OrmKEY::$FK
			, "urlSkeleton.url" 
		));

		$this->add(new OrmField('lang_iso'
			, OrmCAST::$INHERIT
            , null
            , null
			, OrmKEY::$FK
			, "urlSkeleton.lang_iso" 
		));
		
		$this->addAlias('myurl', ['url', 'lang_iso']);
	}	
}
?>