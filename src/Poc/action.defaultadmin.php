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
			Core::dropTable($anEntity);
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


<h2>Test #5 : can we save some entities ?</h2>
<?php
	$country = MyAutoload::getInstance($this->getName(), 'country');
	$myArray = array();
	$myArray[] = array('label'=>'France', 'iso_code'=>'fr');    
    $myArray[] = array('label'=>'Spain', 'iso_code'=>'es');
    $myArray[] = array('label'=>'England', 'iso_code'=>'en'); 
	Core::insertEntity($country, $myArray);
		
	$expected = 3; 
	$result = Core::countAll($country);
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected entities in tables, we have got $result entities in tables</p>";
?>

<h2>Test #6 : Also, does values are correctly saved ?</h2>
<?php
	$country3 = Core::selectById($country,3);
		
	$expected = 'England'; 
	$result = $country3->get('label');
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected label, we have got $result label in the entitie #3</p>";
?>

<h2>Test #7 : Can we made some update ?</h2>
<?php
	$myArray = array();
    $myArray[] = array('country_id'=>3, 'label'=>'Belgium', 'iso_code'=>'be'); 
	Core::updateEntity($country, $myArray);
	
	$expected = 3; 
	$result = Core::countAll($country);
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected entities in tables, we have got $result entities in tables</p>";
	
	$country3 = Core::selectById($country,3);
		
	$expected = 'Belgium'; 
	$result = $country3->get('label');
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected label, we have got $result label in the entitie #3</p>";
?>

<?php
?>