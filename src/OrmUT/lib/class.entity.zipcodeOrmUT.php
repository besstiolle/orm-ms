<?php
class ZipCodeOrmUT extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','ZipCodeOrmUT');
		
		$this->add(new OrmField('zipcode_id'		
			, OrmCAST::$INTEGER
			, null	
			, null 		// is not nullable
			, OrmKEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new OrmField('code'		
			, OrmCAST::$STRING
			, 50	
		));
		
		$this->add(new OrmField('cities'				
			, OrmCAST::$NONE
			, null
			, TRUE
			, OrmKEY::$AK 
			, 'CityZipCodeOrmUT.city_id'
		));
	}	
}
?>