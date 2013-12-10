<?php
/**
 * A very simple example of how you can define a "User" without any relation to another Object.
 */ 
class UserSkeleton extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormskeleton','userskeleton');
		
		// A primary key, very useful for most of your definitions
		$this->add(new OrmField('user_id'	
			// This parameter is simply a integer. You can choose between a lot of possibility. 
			//  Take a look inside the class OrmCAST for all the possibilities.
			, OrmCAST::$INTEGER 
			, null	
			, null 		// is required ! 
			, OrmKEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new OrmField('login'		
			// Will be transformed into a varchar(50) no-nullable into your database.
			, OrmCAST::$STRING
			, 50	
		));
		
		$this->add(new OrmField('name'		
			, OrmCAST::$STRING
			, 50	
		));
				
		$this->add(new OrmField('description'		
			// This time we choose a buffer, a bloc of text without limit of size.
			, OrmCAST::$BUFFER
			, null
			// When it setted to "TRUE" (or "true"), the field won't be required
			, true 
		));
			
		$this->add(new OrmField('date_creation'		
			// Another field without any size specified
			, OrmCAST::$DATE
		));
			
		$this->add(new OrmField('hour_last_modification'		
			// Another field without any size specified
			, OrmCAST::$TIME
		));
	}	
}
?>