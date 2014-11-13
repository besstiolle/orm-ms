<h2>{$action} of a CitySkeleton</h2>

{$error}
{$formStart}
	<input type='hidden' name='{$actionid}city_id' value='{$tool->securize($city->get('city_id'))}' />

	<label for='labelCity'>label of the city : </label><input type='text' name='{$actionid}labelCity' value='{$tool->securize($city->get('labelCity'))}' /><br/>
	<label for='country'>country : </label>{$selectCountries}<br/>
	{$submit} {$return}
</form>