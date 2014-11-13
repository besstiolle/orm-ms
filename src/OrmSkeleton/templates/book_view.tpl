
<table class='pagetable' cellspacing='0'>
	<tr>
		<th>&nbsp;</th>
		<th>title</th>
		<th>description</th>
		<th>&nbsp;</th>
	</tr>
{if $count == 0}
	<tr><td colspan='4'><center>no record in database</center></td></tr>
{/if}

{* Iterate over each one *}
{foreach $all as $book}

	{assign currentId $book->get('book_id')}
	
	{* We can easily get all the values with the $object->get('fieldname') syntax *}
	<tr>
			<td>{$tool->securize($currentId)}</td>
			<td>{$tool->securize($book->get('title'))}</td>
			<td>{$tool->securize($book->get('description'))}</td>
			<td>{$delete.$currentId} &nbsp;-&nbsp; {$edit.$currentId}</td>
		</tr>
{/foreach}

</table>
<p>There are {$count} BookSkeleton(s) into the database. (Only 2 displayed as we set a 2 limit.) 
   Would you like to <b>{$add}</b> another one ?</p>
