<?php
	/**
	 * Here you will provide some informations about this new Entity {if $entityName !== ''}{$entityName}{/if} 
	 * {if $entityName === ''} 
	 * Don't forget to find a good name for this new Entity !{/if} 
	 **/ 
	class {if $entityName !== ''}{$entityName}{else}RenameThisEntity{/if} extends OrmEntity {
		public function __construct() {
			parent::__construct({if $moduleName !== ''}'{$moduleName}'{else}'yourmodulename'{/if},{if $entityName !== ''}'{$entityName}'{else}'renamethisentity'{/if});

{foreach $output as $key => $values}
			$this->add(new OrmField('{$key}'	
				, OrmCAST::{$values.type}
				, {$values.size}	
				, {$values.nullable} 
				, {$values.key} 
			));
{/foreach}

{foreach $output as $key => $values}{foreach $values.extra as $extra}
			{$extra}
{/foreach}{/foreach}
		}

		/**
		 * When you declare this function, the framework will try to execute this function as soon as the table is created.
		 * So it's the best place to initiate your tables with some data !
		 */
		public function initTable(){
			
		}
	}
?>