<table class='pagetable' cellspacing='0'>
	<tr>
		<th>&nbsp;</th>
		<th>label</th>
		<th>cities</th>
		<th>&nbsp;</th>
	</tr>
{if $count == 0}
	<tr><td colspan='4'><center>no record in database</center></td></tr>
{/if}

{* Iterate over each one *}
{foreach $all as $country}
{assign currentId $country->get('country_id')}
	
	{* We can easily get all the values with the $object->get('fieldname') syntax *}
	<tr>
			<td>{$tool->securize($currentId)}</td>
			<td>{$tool->securize($country->get('labelCountry'))}</td>
			<td>{$tool->securize($citiesLabel.$currentId)}</td>
			<td>{$delete.$currentId} &nbsp;-&nbsp; {$edit.$currentId}</td>
		</tr>
	
{/foreach}

</table>
<p>There are {$count} CountrySkeleton(s) into the database. Would you like to <b>{$add}</b> another one ?</p>
