<?php

if (!function_exists("cmsms")) exit;

	$cssError = 'error';
	$cssSuccess = 'success';
	$db = cmsms()->GetDb();
	$config = cmsms()->GetConfig();

?> 
<style>
.success, .error{
    padding: 3px 3px 3px 37px;
}

</style>
<h1>Units Tests</h1>

<h2>Test #1 : does Entities from /lib are loaded ?</h2>

<?php
	$entities = MyAutoload::getAllInstances($this->GetName());
	
	$expected = 1;
	$result = count($entities);
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected classes, we have got $result classes</p>";
?> 
<h2>Test #2 : can we lunch creation of tables</h2>
<?php
	try{
		$entities = MyAutoload::getAllInstances($this->GetName());
		foreach($entities as $anEntity)
		{
			Core::createTable($anEntity);
		}
		echo "<p class='success'>It seems it work :)</p>";
	} catch (Exception $e){
		echo "<p class='fail'>fail ... :(</p>";
	}
	
?> 
<h2>Test #3 : do we have all the tables created ?</h2>
<?php
	$requete = "SHOW TABLES FROM `".$config['db_name']."` LIKE 'cms_module_poc_%'";
	$result = $db->execute($requete);
	if ($result === false)
    {
        throw new Exception("Database error durant la requête!".$db->ErrorMsg());
    }
	
	$expected = 2; // don't forget the Sequence
	$result = $result->RecordCount();
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected tables, we have got $result tables</p>";
?>
<h2>Test #4 : do we have all the sequences created ?</h2>
<?php
	$requete = "SHOW TABLES FROM `".$config['db_name']."` LIKE 'cms_module_poc_%_seq'";
	$result = $db->execute($requete);
	if ($result === false)
    {
        throw new Exception("Database error durant la requête!".$db->ErrorMsg());
    }
	
	$expected = 1; 
	$result = $result->RecordCount();
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected sequences, we have got $result sequences</p>";
?>


<?php
/*
	$requete = "DESCRIBE cms_groups";
	$result = $db->execute($requete);
	if ($result === false)
    {
        throw new Exception("Database error durant la requête!".$db->ErrorMsg());
    }
	
	$expected = 1; 
	$result = $result->RecordCount();
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected sequences, we have got $result sequences</p>";

*/
?>

<?php
?>