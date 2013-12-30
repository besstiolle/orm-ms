
<p class='title'>Test #6 : Also, does values are correctly saved ?</p>
<?php
	$country3 = OrmCore::findById(new CountryOrmUT(),3);
		
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