<style>
.success, .error{
    padding: 3px 3px 3px 37px;
}
pre{
	display:none;
}

</style>



<script type="text/javascript">
  // <![CDATA[
$(document).ready(function(){
	$( "label.labelExample" ).click(function() {
			$( this ).next().toggle();
	});
});

// ]]>
  
  </script>


<h1>Units Tests</h1>

<?php

if (!function_exists("cmsms")) exit;

	$cssError = 'error';
	$cssSuccess = 'success';
	$db = cmsms()->GetDb();
	$config = cmsms()->GetConfig();

	$tests = glob(cms_join_path(dirname(__FILE__),"tests","*.txt"));
	foreach($tests as $test) {
		include($test);
		echo "<label class='labelExample' for='ex_".md5($test)."'>Clic to show the code</label><pre name='ex_".md5($test)."' class='toggle'>".htmlentities(file_get_contents($test))."</pre>";
		
	}

?>
