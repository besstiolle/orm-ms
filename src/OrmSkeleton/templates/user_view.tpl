
<table class='pagetable' cellspacing='0'>
	<tr>
		<th>&nbsp;</th>
		<th>login</th>
		<th>name</th>
		<th>date_creation</th>
		<th>hour_last_modification</th>
		<th>&nbsp;</th>
	</tr>
{if $count == 0}
	<tr><td colspan='6'><center>no record in database</center></td></tr>
{/if}

{* Iterate over each one *}
{foreach $all as $user}

	{assign currentId $user->get('user_id')}
	
	{* We can easily get all the values with the $object->get('fieldname') syntax *}
	<tr>
			<td>{$tool->securize($currentId)}</td>
			<td>{$tool->securize($user->get('login'))}</td>
			<td>{$tool->securize($user->get('name'))}</td>
			<td>{$tool->securize($user->get('date_creation'))|date_format:'%Y/%m/%e'}</td>
			<td>{$tool->securize($user->get('hour_last_modification'))}</td>
			<td>{$delete.$currentId} &nbsp;-&nbsp; {$edit.$currentId}</td>
		</tr>
{/foreach}

</table>
<p>There are {$count} UserSkeleton(s) into the database. Would you like to <b>{$add}</b> another one ?</p>