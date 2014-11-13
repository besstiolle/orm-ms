<h2>{$action} of a CountrySkeleton</h2>

{$error}
{$formStart}
	<input type='hidden' name='{$actionid}country_id' value='{$tool->securize($country->get('country_id'))}' />
	<label for='labelCountry'>Label of the Country : </label><input type='text' name='{$actionid}labelCountry' value='{$tool->securize($country->get('labelCountry'))}' /><br/>
	{$submit} {$return}
</form>