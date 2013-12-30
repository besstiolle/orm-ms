
<p class='title'>002/003 : Can we made some update ?</p>
<?php
	$country3->set('label', 'Belgium');
	$country3->set('iso_code', 'be');
	OrmCore::updateEntity($country3);
	
	$expected = 3; 
	$result = OrmCore::countAll(new CountryOrmUT());
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected entities in tables, we have got $result entities in tables</p>";
	
	$country3 = OrmCore::findById(new CountryOrmUT(),3);
		
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