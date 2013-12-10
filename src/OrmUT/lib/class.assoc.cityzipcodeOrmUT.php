<?php
class CityZipCodeOrmUT extends OrmEntityAssociation
{
	public function __construct()
	{
		parent::__construct('ormut', 'cityzipcode');
		
		$this->add(new OrmField('city_id'	
			, OrmCAST::$INTEGER
			, null
			, null  				// is not nullable
			, OrmKEY::$FK 				// is foreignKey
			, 'CityOrmUT.city_id'
			));
		
		$this->add(new OrmField('zipcode_id'		
			, OrmCAST::$INTEGER
			, null
			, null
			, OrmKEY::$FK
			, 'ZipCodeOrmUT.zipcode_id'	
			));
	}
}
?>