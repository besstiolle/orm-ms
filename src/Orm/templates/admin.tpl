<style>
	label.help{
		
	}
</style>


{$formstart}
	<h3>Select the type Of Cache</h3>
		{$selectCache}{$deleteCache}<label for='{$id}cache' class='help' />Learn more about these options on <a href='#' target='_blank'>the wiki</a>.</label>
	<br/>
	{$submit}
	<h3>Select the level of message into the console</h3>
		{$selectLog}{$deleteLog}<label for='{$id}level' class='help' />Learn more about these options on <a href='#' target='_blank'>the wiki</a>.</label>
	<br/>
	{$submit}
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