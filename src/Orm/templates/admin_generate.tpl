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
	<input type='text' name='{$actionid}entityName' value='{$entityName}' placeholder='Name your entity [alpha only] (Optional)' size='40' />
	<input type='text' name='{$actionid}moduleName' value='{$moduleName}' placeholder='Name your module [alpha only] (Optional)' size='40'  />
	<input type='submit' name='generate' value='Generate PHP code' />
	<a class='ormbutton ui-state-default ui-corner-all' href='{$cancel}'>
		<span class="ui-icon  ui-icon-arrowreturnthick-1-w"></span>
		Back
	</a>

	{if isset($output)}<br/>
		<textarea>
{$output}
		</textarea>
	{/if}

{if $moduleName !== '' && $entityName !== ''}
	<hr/>
	Would you like to save the file into ./modules/{$moduleName}/lib/class.{$entityName}.php
	<input type='submit' name='{$actionid}persist' value='Save the result' />
{/if}

{if persist != null}
	<p>
	{if persist}
		File saved with success !
	{else}
		Oops, we didn't succeed to save the file... :(;
	{/if}
	</p>
{/if}

</form>

