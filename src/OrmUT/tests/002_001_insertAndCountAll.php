
<p class='title'>Test #5 : can we save some entities ?</p>
<?php
	$country1 = new CountryOrmUT();
	$country1->set('label', 'France');
	$country1->set('iso_code', 'fr');
	$country1->save();
	
	$country2 = new CountryOrmUT();
	$country2->set('label', 'Spain');
	$country2->set('iso_code', 'es');
	//Another way to insert a Entity
	OrmCore::insertEntity($country2);
	
	$country3 = new CountryOrmUT();	
	$country3->set('label', 'England');
	$country3->set('iso_code', 'en');
	$country3->save();
		
	$expected = 3; 
	$result = OrmCore::countAll(new CountryOrmUT());
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected entities in tables, we have got $result entities in tables</p>";
?>