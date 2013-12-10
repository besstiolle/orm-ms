<?php
class CityOrmUT extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','city');
		
		$this->add(new OrmField('city_id'		
			, OrmCAST::$INTEGER
			, null	
			, null 		// is not nullable
			, OrmKEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new OrmField('label'		
			, OrmCAST::$STRING
			, 50	
		));
		
		$this->add(new OrmField('zipcodes'				
			, OrmCAST::$NONE
			, null
			, TRUE
			, OrmKEY::$AK 
			, 'CityZipCodeOrmUT.zipcode_id'
		));
	}	
}
?>