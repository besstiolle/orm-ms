<style>
	label.help{
		
	}
	
	div.left{
	    float: left;
		text-align: center;
		width: 50px;
	}
	
	div.right{
		border-left: 2px solid #CCCCCC;
		float: left;
		padding-left: 5px;
	}
	.ormbutton {
		clear: both;
		text-align: left
	}
	a.ormbutton{
		display: inline-block;
		position: relative;
		margin: 10px 0;
		line-height: 26px;
		color: #232323;
		text-decoration: none;
		padding: 1px 8px 2px 20px;
	}
	a.ormbutton .ui-icon {
		position: absolute;
		left: 0;
		top: 6px;
	}
	a.ormbutton:hover {
		color: #fff
	}
	.hidden{
		display:none;
	}

	.div__output{
		max-height: 300px;
		overflow: auto;
		border: 1px solid #000;
	}
</style>
<script type="text/javascript" src='{root_url}/modules/Orm/js/diffview.js'></script>
<script type="text/javascript" src='{root_url}/modules/Orm/js/difflib.js'></script>
<link rel="stylesheet" type="text/css" href="{root_url}/modules/Orm/js/diffview.css" media="screen" />
<script type="text/javascript">
	function diffUsingJS(viewType,idbase, idnew, idoutput) {
	"use strict";
	var byId = function (id) { return document.getElementById(id); },
		base = difflib.stringAsLines(byId(idbase).value),
		newtxt = difflib.stringAsLines(byId(idnew).value),
		sm = new difflib.SequenceMatcher(base, newtxt),
		opcodes = sm.get_opcodes(),
		diffoutputdiv = byId(idoutput),
		contextSize = null; 

	diffoutputdiv.innerHTML = "";
	contextSize = contextSize || null;

	diffoutputdiv.appendChild(diffview.buildView({
		baseTextLines: base,
		newTextLines: newtxt,
		opcodes: opcodes,
		baseTextName: "Orm Values",
		newTextName: "Database Values",
		contextSize: contextSize,
		viewType: viewType
	}));
}
</script>


{if empty($listInstance)}
	No module detected
{/if}

{foreach $listInstance as $moduleName => $module}
	<h3>Module {$moduleName}</h3>
	{foreach $module['listEmptyTable'] as $entityname => $emptyTable}
		<h5 style='margin-left:10px;'>{$entityname}</h5>

		{if !empty($emptyTable)}

			<p style='color:#870909;'>
				The table <b>{$emptyTable}</b> for entity <b>{$entityname}</b> is not found.
			</p>

		{elseif $module['listResultXX'][$entityname] == $module['listResultDB'][$entityname]}

			<p style='color:#09870E;'>
				The table for entity <b>{$entityname}</b> is well formed
			</p>

		{else}

			<p style='color:#FAA00F;'>The table for entity <b>{$moduleName}</b> have some differents.</p>
			<textarea id='baseText_{$entityname}' class='hidden'>
				{$module['listResultXX'][$entityname]}
			</textarea>
			<textarea id='newText_{$entityname}' class='hidden'>
				{$module['listResultDB'][$entityname]}
			</textarea>
			<div id="diffoutput_{$entityname}" class='div__output'> </div>

			<script type="text/javascript" >
				$( document ).ready(function() {
					diffUsingJS(0, "baseText_{$entityname}", "newText_{$entityname}", "diffoutput_{$entityname}");
				});
				
			</script>

		{/if}
	{/foreach}
{/foreach}


<a class="ormbutton ui-state-default ui-corner-all" href="{$back}">
	<span class="ui-icon  ui-icon-arrowreturnthick-1-w"></span>
	Back
</a>