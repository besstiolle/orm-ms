
There are {$count} CommentSkeleton(s) into the database.

{if $randomComment != null}
	<p>The random comment is <b>{$randomComment->get('text')} </b> on : <b>{$randomComment->get('myurl')->get('title')}</b></p>
{/if}
