<?php
class castOrmUtNumbersWithDefaultValues extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','castOrmUtNumbersWithDefaultValues');
		
		$this->add(new OrmField('id'		
			, OrmCAST::$INTEGER
			, null
			, null
			, OrmKEY::$PK
		));
		
		$this->add(new OrmField('aInteger'		
			, OrmCAST::$INTEGER
		));
		
		$this->add(new OrmField('aNumeric'		
			, OrmCAST::$NUMERIC
		));
		
		$this->add(new OrmField('aDouble'		
			, OrmCAST::$DOUBLE
		));
		
		$this->garnishAutoincrement();

		$this->garnishDefaultValue('aInteger',-123456789);
		$this->garnishDefaultValue('aNumeric',"-1225");
		$this->garnishDefaultValue('aDouble',"-129999.25");
		
	}	
}
?>