<?php
class CompositeKeyOrmUT extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormut','CompositeKeyOrmUT');
		
		$this->add(new OrmField('name'		
			, OrmCAST::$STRING
			, 2
			, null
			, OrmKEY::$PK
		));
		
		$this->add(new OrmField('surname'		
			, OrmCAST::$STRING
			, 5
			, null
			, OrmKEY::$PK
		));

		$this->add(new OrmField('description'		
			, OrmCAST::$STRING
			,255
		));
		

	}	
}
?>