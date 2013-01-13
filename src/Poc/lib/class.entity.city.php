<?php
class City extends Entity
{
	public function __construct()
	{
		parent::__construct('poc','city');
		
		$this->add(new Field('city_id'		
			, CAST::$INTEGER
			, null	
			, null 		// is not nullable
			, KEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new Field('label'		
			, CAST::$STRING
			, 50	
		));
		
		$this->add(new Field('zipcodes'				
			, CAST::$NONE
			, null
			, TRUE
			, KEY::$AK 
			, 'CityZipCode.zipcode_id'
		));
	}	
}
?>