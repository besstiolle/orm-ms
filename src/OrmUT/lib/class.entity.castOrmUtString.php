<?php
class CastOrmUTString extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','CastOrmUTString');
		
		$this->add(new OrmField('id'		
			, OrmCAST::$INTEGER
			, null
			, null
			, OrmKEY::$PK
		));
		
		
		$this->add(new OrmField('aString'		
			, OrmCAST::$STRING
			, 255
		));
		
		$this->add(new OrmField('aStringNull'		
			, OrmCAST::$STRING
			, 255
			, TRUE
		));
		
		$this->add(new OrmField('aBuffer'		
			, OrmCAST::$BUFFER
		));
		
		$this->add(new OrmField('aBufferNull'		
			, OrmCAST::$BUFFER
			, null
			, TRUE
		));
		
		
		$this->garnishAutoincrement();
		
	}	
}
?>