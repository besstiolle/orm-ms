<?php
class Country extends Entity
{
	public function __construct()
	{
		parent::__construct('poc','country');
		
		$this->add(new Field('country_id'		
			, CAST::$INTEGER
			, null	
			, null 		// is not nullable
			, KEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new Field('label'		
			, CAST::$STRING
			, 50	
		));
		
		$this->add(new Field('iso_code'				
			, CAST::$STRING	
			, 5	
		));
		
	}	
}
//Init the entity
new Country();
?>