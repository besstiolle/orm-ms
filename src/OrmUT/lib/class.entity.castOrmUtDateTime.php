<?php
class CastOrmUTDateTime extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','CastOrmUTDateTime');
		
		$this->add(new OrmField('id'		
			, OrmCAST::$INTEGER
			, null
			, null
			, OrmKEY::$PK
		));
		
		$this->add(new OrmField('aDate'		
			, OrmCAST::$DATE
		));
		
		$this->add(new OrmField('aDateNull'		
			, OrmCAST::$DATE
			, null
			, TRUE
		));
		
		$this->add(new OrmField('aTime'		
			, OrmCAST::$TIME
		));
		
		$this->add(new OrmField('aTimeNull'		
			, OrmCAST::$TIME
			, null
			, TRUE
		));
		
		$this->add(new OrmField('aTS'		
			, OrmCAST::$TS
		));
		
		$this->add(new OrmField('aTSNull'		
			, OrmCAST::$TS
			, null
			, TRUE
		));
		
		$this->add(new OrmField('aDateTime'		
			, OrmCAST::$DATETIME
		));
		
		$this->add(new OrmField('aDateTimeNull'		
			, OrmCAST::$DATETIME
			, null
			, TRUE
		));
		
		$this->garnishAutoincrement();
		
	}	
}
?>