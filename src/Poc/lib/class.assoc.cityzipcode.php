<?php
class CityZipCode extends EntityAssociation
{
	public function __construct()
	{
		parent::__construct('poc', 'cityzipcode');
		
		$this->add(new Field('city_id'	
			, CAST::$INTEGER
			, null
			, null  				// is not nullable
			, KEY::$FK 				// is foreignKey
			, 'City.city_id'
			));
		
		$this->add(new Field('zipcode_id'		
			, CAST::$INTEGER
			, null
			, null
			, KEY::$FK
			, 'ZipCode.zipcode_id'	
			));
	}
}
?>