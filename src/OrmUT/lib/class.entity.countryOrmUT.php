<?php
class CountryOrmUT extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','country');
		
		$this->add(new OrmField('country_id'		
			, OrmCAST::$INTEGER
			, null	
			, null 		// is not nullable
			, OrmKEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new OrmField('label'		
			, OrmCAST::$STRING
			, 50	
		));
		
		$this->add(new OrmField('iso_code'				
			, OrmCAST::$STRING	
			, 5	
		));
	}	
}
?>