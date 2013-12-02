<?php
class ZipCode extends Entity
{
	public function __construct()
	{
		parent::__construct('ormut','zipcode');
		
		$this->add(new Field('zipcode_id'		
			, CAST::$INTEGER
			, null	
			, null 		// is not nullable
			, KEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new Field('code'		
			, CAST::$STRING
			, 50	
		));
		
		$this->add(new Field('cities'				
			, CAST::$NONE
			, null
			, TRUE
			, KEY::$AK 
			, 'CityZipCode.city_id'
		));
	}	
}
?>