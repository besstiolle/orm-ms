<h2>{$action} of a UserSkeleton</h2>

{$error}
{$formStart}
	<input type='hidden' name='{$actionid}user_id' value='{$tool->securize($user->get('user_id'))}' />
	{if $user->get('date_creation') != ''}
		Created : {$user->get('date_creation')|date_format:'%Y/%m/%e'} 
	{/if}<br/>
	<label for='login'>Login : </label><input type='text' name='{$actionid}login' value='{$tool->securize($user->get('login'))}' /><br/>
	<label for='name'>Name : </label><input type='text' name='{$actionid}name' value='{$tool->securize($user->get('name'))}' /><br/>
	<label for='description'>Description : </label><textarea name='{$actionid}description' >{$tool->securize($user->get('description'))}</textarea><br/>
	{$submit} {$return}
</form>