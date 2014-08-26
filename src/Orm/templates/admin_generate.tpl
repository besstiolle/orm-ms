<style>
	.ormbutton {
		clear: both;
		text-align: left
	}
	a.ormbutton{
		display: inline-block;
		position: relative;
		margin: 10px 0;
		line-height: 26px;
		color: #232323;
		text-decoration: none;
		padding: 1px 8px 2px 20px;
	}
	a.ormbutton .ui-icon {
		position: absolute;
		left: 0;
		top: 6px;
	}
	a.ormbutton:hover {
		color: #fff
	}
</style>

{$formStart}
	{$dropdown}
	<input type='submit' value='Generate PHP code' />
	<a class='ormbutton ui-state-default ui-corner-all' href='{$cancel}'>
		<span class="ui-icon  ui-icon-arrowreturnthick-1-w"></span>
		Back
	</a>

	{if isset($output)}<br/>
		<textarea>
<?php
	/**
	 * Here you will provide some informations about this new Entity
	 *
	 * Don't forget to find a good name for this new Entity !
	 **/ 
	class RenameThisEntity extends OrmEntity {
		public function __construct() {
			parent::__construct('yourmodulename','renamethisentity');

{foreach $output as $key => $values}
			$this->add(new OrmField('{$key}'	
				, OrmCAST::$INTEGER 
				, null	
				, null 		
				, OrmKEY::$PK	
			));

{/foreach}

		}	
	}
?>

		</textarea>
	{/if}
</form>