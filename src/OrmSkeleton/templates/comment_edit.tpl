
<h2>Comments for the Url {$url} ({$lang_iso})</h2>
<table class='pagetable' cellspacing='0'><tr>
		<th>id</th>
		<th>text</th>
		<th>&nbsp;</th>
	</tr>

{if $count == 0}
	<tr><td colspan='3'><center>no record in database</center></td></tr>
{/if}

{foreach $comments as $comment}
	
	{assign currentId $comment->get('comment_id')}

	{* We can easily get all the values with the $object->get('fieldname') syntax *}
	<tr>
		<td>{$tool->securize($currentId)}</td>
		<td>{$tool->securize($comment->get('text'))}</td>
		<td>{$delete.$currentId}</td>
	</tr>
{/foreach}

</table>
<p>There are {$count} CommentSkeleton(s) into the database for this URL / lang.</p>



<h2>Add a new CommentSkeleton</h2>

{$error}
{$formStart}
	<label for='text'>Text : </label>
	<textarea style='width: 20em;height: 6em;' name='{$actionid}text'>Type a message here</textarea>
	
	{$submit} {$return}
</form>