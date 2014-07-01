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
			, OrmCAST::$STRING
            , 255
            , null
			, OrmKEY::$FK
			, "urlSkeleton.url" 
		));

		$this->add(new OrmField('lang_iso'
			, OrmCAST::$STRING
            , 10
            , null
			, OrmKEY::$FK
			, "urlSkeleton.lang_iso" 
		));
		
		//Will provide a result for $comment->get('myurl') 
		//			=> array(url => 'www.website.fr', 'lang_iso' => 'en_US', 'title' => 'my title', 'description' => 'xx')
		$this->addAlias('myurl', ['url', 'lang_iso']);
	}	

		/**
	 * When you declare this function, the framework will try to execute this function as soon as the table is created.
	 * So it's the best place to initiate your tables with some data !
	 */
	public function initTable(){
		$comment = new CommentSkeleton();
		$comment->set('url', 'http://www.furie.be');
		$comment->set('lang_iso', 'en_US');
		$comment->set('text', 'The best website on earth (IMHO)');
		$comment->save();
	}
}
?>