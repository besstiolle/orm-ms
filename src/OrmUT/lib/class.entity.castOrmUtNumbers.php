<?php
class CastOrmUTNumbers extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','CastOrmUTNumbers');
		
		$this->add(new OrmField('id'		
			, OrmCAST::$INTEGER
			, null
			, null
			, OrmKEY::$PK
		));
		
		$this->add(new OrmField('aInteger'		
			, OrmCAST::$INTEGER
		));
		
		$this->add(new OrmField('aIntegerNull'		
			, OrmCAST::$INTEGER
			, null
			, TRUE
		));
		
		$this->add(new OrmField('aNumeric'		
			, OrmCAST::$NUMERIC
		));
		
		$this->add(new OrmField('aNumericNull'		
			, OrmCAST::$NUMERIC
			, null
			, TRUE
		));
		
		$this->add(new OrmField('aDouble'		
			, OrmCAST::$DOUBLE
		));
		
		$this->add(new OrmField('aDoubleNull'		
			, OrmCAST::$DOUBLE
			, null
			, TRUE
		));
		
		$this->garnishAutoincrement();
		
	}	
}
?>