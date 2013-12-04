<?php
/**
 * A very simple example of how you can define a "User" without any relation to another Object.
 */ 
class UserSkeleton extends Entity
{
	public function __construct()
	{
		parent::__construct('ormskeleton','userskeleton');
		
		// A primary key, very useful for most of your definitions
		$this->add(new Field('user_id'	
			// This parameter is simply a integer. You can choose between a lot of possibility. 
			//  Take a look inside the class CAST for all the possibilities.
			, CAST::$INTEGER 
			, null	
			, null 		// is required ! 
			, KEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new Field('login'		
			// Will be transformed into a varchar(50) no-nullable into your database.
			, CAST::$STRING
			, 50	
		));
		
		$this->add(new Field('name'		
			, CAST::$STRING
			, 50	
		));
				
		$this->add(new Field('description'		
			// This time we choose a buffer, a bloc of text without limit of size.
			, CAST::$BUFFER
			, null
			// When it setted to "TRUE" (or "true"), the field won't be required
			, true 
		));
			
		$this->add(new Field('date_creation'		
			// Another field without any size specified
			, CAST::$DATE
		));
			
		$this->add(new Field('hour_last_modification'		
			// Another field without any size specified
			, CAST::$TIME
		));
	}	
}
?>