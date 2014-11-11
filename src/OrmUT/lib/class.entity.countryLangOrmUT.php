<?php
class CountryLangOrmUT extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','CountryLangOrmUT');
		
		$this->add(new OrmField('lang_id'		
			, OrmCAST::$INTEGER
			, null	
			, null 		// is not nullable
			, OrmKEY::$PK	// is a primary key (no auto-incremented)
		));
		
		$this->add(new OrmField('label'		
			, OrmCAST::$STRING
			, 50	
		));
		
		$this->add(new OrmField('country'				
			, OrmCAST::$INTEGER	
			, null
			, null
			, OrmKEY::$FK
			, "CountryOrmUT.country_id"	

		));
	}	
}
?>