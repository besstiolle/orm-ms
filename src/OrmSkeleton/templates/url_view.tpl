
<table class='pagetable' cellspacing='0'>
	<tr>
		<th>url</th>
		<th>lang_iso</th>
		<th>title</th>
		<th>description</th>
		<th>number comments</th>
		<th>&nbsp;</th>
	</tr>
{if $count == 0}
	<tr><td colspan='6'><center>no record in database</center></td></tr>
{/if}

{* Iterate over each one *}
{foreach $all as $url}

	{assign currentUrl $url->get('url')}
	{assign currentLangIso $url->get('lang_iso')}
	
	{* We can easily get all the values with the $object->get('fieldname') syntax *}
	<tr>
			<td>{$tool->securize($currentUrl)}</td>
			<td>{$tool->securize($currentLangIso)}</td>
			<td>{$tool->securize($url->get('title'))}</td>
			<td>{$tool->securize($url->get('description'))}</td>
			<td>{$commentslink.$currentUrl.$currentLangIso}</td>
			<td>{$delete.$currentUrl.$currentLangIso} &nbsp;-&nbsp; {$edit.$currentUrl.$currentLangIso}</td>
		</tr>
{/foreach}

</table>
<p>There are {$count} UrlSkeleton(s) into the database. Would you like to <b>{$add}</b> another one ?</p>
