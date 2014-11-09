
<table class='pagetable' cellspacing='0'>
	<tr>
		<th>&nbsp;</th>
		<th>label</th>
		<th>country</th>
		<th>&nbsp;</th>
	</tr>
{if $count == 0}
	<tr><td colspan='4'><center>no record in database</center></td></tr>
{/if}

{* Iterate over each one *}
{foreach $all as $city}

	{assign currentId $city->get('city_id')}
	
	{* We can easily get all the values with the $object->get('fieldname') syntax *}
	<tr>
			<td>{$tool->securize($currentId)}</td>
			<td>{$tool->securize($city->get('labelCity'))}</td>
			<td>{$tool->securize($city->get('country')->get('labelCountry'))}</td>
			<td>{$delete.$currentId} &nbsp;-&nbsp; {$edit.$currentId}</td>
		</tr>
{/foreach}

</table>
<p>There are {$count} CitySkeleton(s) into the database. Would you like to <b>{$add}</b> another one ?</p>
