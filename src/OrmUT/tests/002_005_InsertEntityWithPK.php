
<p class='title'>002/004 : can I create an Entity with OrmEntity->save() with an initial value in PK?</p>
<?php
	
	//First, drop the table, recreate it
	reinitAllTables($mod);
	
	$country = new CountryOrmUT();
	$country->set('country_id', '1');
	$country->set('label', 'China');
	$country->set('iso_code', 'cn');
	$china1 = $country->save();
	$country->set('country_id', '2');
	$china2 = $country->save();
	$country->set('country_id', '3');
	$china3 = $country->save();
	
	$expected = '3'; 
	$result = OrmCore::countAll(new CountryOrmUT());
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected #$expected elements in table, we have got #$result elements in table</p>";
	
	
	$country = new CountryOrmUT();
	$country->set('label', 'Other');
	$country->set('iso_code', 'ot');
	try{
		$china3 = $country->save();
	} catch(Exception $e){
	}
	$result = OrmCore::findById(new CountryOrmUT(),4);
	UtilsTest::assertIsNotNull($result);

?>