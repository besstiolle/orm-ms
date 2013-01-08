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
	$country1 = new Country();
	$country1->set('label', 'France');
	$country1->set('iso_code', 'fr');
	Core::insertEntity($country1);
	
	$country2 = new Country();
	$country2->set('label', 'Spain');
	$country2->set('iso_code', 'es');
	Core::insertEntity($country2);
	
	$country3 = new Country();	
	$country3->set('label', 'England');
	$country3->set('iso_code', 'en');
	Core::insertEntity($country3);
		
	$expected = 3; 
	$result = Core::countAll(new Country());
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
	$country3 = Core::selectById(new Country(),3);
		
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
	$country3->set('label', 'Belgium');
	$country3->set('iso_code', 'be');
	Core::updateEntity($country3);
	
	$expected = 3; 
	$result = Core::countAll(new Country());
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected entities in tables, we have got $result entities in tables</p>";
	
	$country3 = Core::selectById(new Country(),3);
		
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
<h2>Test #8 : Do we have problem if we try to set null a not nullable field ?</h2>
<?php
	$country3->set('label', null);
	
	$expected = 'one'; 
	$result = 'no';
	try{
		Core::updateEntity($country3);
	} catch (IllegalArgumentException $iae) {
		$result = 'one';
	}
	
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected exception, we have got $result exception caused by null value in the entity #3</p>";
?>

<h2>Test #9 : can I also save directly a Entity with Entity->save() ?</h2>
<?php
	
	$country4 = MyAutoload::getInstance($this->getName(), 'country');
	$country4->set('label', 'China');
	$country4->set('iso_code', 'cn');
	$copyChina = $country4->save();
	
	$expected = '4'; 
	$result = $copyChina->get('country_id');
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected id #$expected in the returned Entity, we have got id #$result label in the returned entity</p>";
	
	$country4 = Core::selectById(new Country(),4);
		
	$expected = 'China'; 
	$result = $country4->get('label');
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected label, we have got $result label in the entity #4</p>";
	
	$country4->set('label', 'China_bis');
	$country4->save();
	
	$country4 = Core::selectById(new Country(),4);
		
	$expected = 'China_bis'; 
	$result = $country4->get('label');
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected label, we have got $result label in the entity #4</p>";
?>