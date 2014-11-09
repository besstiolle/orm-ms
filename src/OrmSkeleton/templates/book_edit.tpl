<h2>{$action} of a BookSkeleton</h2>

{$error}
{$formStart}
	<input type='hidden' name='{$actionid}book_id' value='{$tool->securize($book->get('book_id'))}' />
	<label for='title'>Title : </label><input type='text' name='{$actionid}title' value='{$tool->securize($book->get('title'))}' /><br/>
	<label for='description'>Description : </label><textarea name='{$actionid}description' >{$tool->securize($book->get('description'))}</textarea><br/>
	<label for='uuid'>UUID : </label><input type='text' name='{$actionid}uuid' value='{$tool->securize($book->get('uuid'))}' /> example : {$uuid}<br/>
	{$submit} {$return} 
</form>