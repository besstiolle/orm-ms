{$formstart}
	{$select}
	{$submit}{$delete}

</form>

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