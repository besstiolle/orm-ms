<h2>{$action} of a UrlSkeleton</h2>

{$error}
{$formStart}
	<label for='title'>Url : </label><input type='text' name='{$actionid}url' value='{$tool->securize($url->get('url'))}' /><br/>
	<label for='title'>Lang_iso : </label><input type='text' name='{$actionid}lang_iso' value='{$tool->securize($url->get('lang_iso'))}' /><br/>
	<label for='title'>Title : </label><input type='text' name='{$actionid}title' value='{$tool->securize($url->get('title'))}' /><br/>
	<label for='description'>Description : </label><textarea name='{$actionid}description' >{$tool->securize($url->get('description'))}</textarea><br/>
	{$submit} {$return}
</form>