<style>
.ok, .nok{
    padding: 3px 3px 3px 37px;
}
.ok{
	background: url("themes/OneEleven/images/icons/extra/accept.png") no-repeat scroll 10px 50% #D9E6C3;
}
.nok{
	background: url("themes/OneEleven/images/icons/extra/block.png") no-repeat scroll 10px 50% #F2D4CE;
}
pre{
	display:none;
}
p.title{
    font-size: 1.2em;
    font-weight: bold;
}
div.test{
    background-color: #D9E6C3;
    border-left: 1px solid #77AB13;
    border-radius: 5px;
    margin: 5px;
    padding: 5px;
}

</style>

<script type="text/javascript">
  // <![CDATA[
$(document).ready(function(){
	$( "label.labelExample" ).click(function() {
			$( this ).next().toggle();
	});
	$( "p.nok" ).parent().css( "background-color", "#F2D4CE" ).css( "border-color" , "#AE432E");
});

// ]]>
  
  </script>


<h1>Units Tests</h1>

<?php

if (!function_exists("cmsms")) exit;

function load(&$mod, $pattern){
	
	$cssError = 'nok';
	$cssSuccess = 'ok';
	$db = cmsms()->GetDb();
	$config = cmsms()->GetConfig();
	
	$tests = glob(cms_join_path(dirname(__FILE__),"tests",$pattern));
	foreach($tests as $test) {
		echo "<div class='test'>";
		require($test);
		echo "<label class='labelExample' for='ex_".md5($test)."'>Clic to show the code</label><pre name='ex_".md5($test)."' class='toggle'>".htmlentities(file_get_contents($test))."</pre>";
		echo "</div>";
	}
}

function reinitAllTables($mod){
	$entities = $mod->getAllInstances();
	foreach($entities as $anEntity) {
		OrmCore::dropTable($anEntity);
		OrmCore::createTable($anEntity);
	}
}

?>

<h2>Loading the classes</h2><?php load($this, "000_*.php"); ?>
<h2>Table & Sequence Creation/Deletion</h2><?php load($this, "001_*.php"); ?>
<h2>Basic Crud Operations</h2><?php load($this, "002_*.php"); ?>
<h2>CAST test</h2><?php load($this, "003_*.php"); ?>
