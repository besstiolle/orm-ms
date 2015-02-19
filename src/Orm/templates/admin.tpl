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
</style>


{$formstartcache}

	<h3>Select the type Of Cache</h3>
	<div class='left'><img src='data:image/png;base64,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' /></div>
	<div class='right'>
		{$selectCache}{$deleteCache}<label for='{$id}cache' class='help' />Learn more about these options on <a href='https://github.com/besstiolle/orm-ms/wiki/Mechanism-Advanced' target='_blank'>the wiki</a>.</label>
	<br/>
	{$submit}
	</div>
	<div class='clear'></div>
</div>

</form>
{$formstarttrace}

<div class='line'>
	<h3>Select the level of message into the console</h3>
	<div class='left'><img src='data:image/png;base64,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' /></div>
	<div class='right'>
		{$selectLog}{$deleteLog}<label for='{$id}level' class='help' />Learn more about these options on <a href='https://github.com/besstiolle/orm-ms/wiki/log-management' target='_blank'>the wiki</a>.</label>
	<br/>
	{$submit}
	</div>
	<div class='clear'></div>
</div>

</form>

<hr/>{*
<ul><li><b>DEBUG :</b> Everything will be wrote. EVERYTHING !</li>
<li><b>INFO :</b> the default value. Great for a production environment.</li>
<li><b>WARN :</b> will be displayed : the errors and the warnings.</li>
<li><b>ERROR :</b>  will be displayed : the errors.</li></ul>
<ul><li><b>NONE :</b> we won't use caching system : Great during your development</li>
<li><b>CALL :</b> the framework will try to remember the last research but only for the current call of PHP.</li></ul>*}

<pre id='output'></pre>
{literal}
<script>

function doUpdate() {
$.ajax({type: "GET", url : "{/literal}{$urlLog}{literal}", cache:false,
          success: function (data) {
             if (data.length > 4) {
                // Data are assumed to be in HTML format
                // Return something like <p/> in case of no updates
                $("#output").text(data);
             }
             setTimeout("doUpdate()", 2000);
           }});
  
}

setTimeout("doUpdate()", 2000);
</script>{/literal}