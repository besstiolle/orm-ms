<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "CountrySkeleton" whenever i want in my module
$country = new CountrySkeleton();

// In the same way i can interrogate the table of CountrySkeleton : 
$count = Core::countAll(new CountrySkeleton());

$link = $this->CreateLink($id, 'editCountry', $returnid, 'add');

echo "<table class='pagetable' cellspacing='0'><tr>
		<th>&nbsp;</th>
		<th>label</th>
		<th>cities</th>
		<th>&nbsp;</th>
	   </tr>";
if($count == 0){
	echo "<tr><td colspan='4'><center>no record in database</center></td></tr>";
} else {
	//I can also retrieve all the CountrySkeleton
	$all = Core::findAll(new CountrySkeleton());
	
	//And iterate over each one
	foreach($all as $country){
	
		//We'll get the id of each cities linked to our Country.
		$arrayCitiesId = $country->get('cities');
		
		//Let's transform this information to a better way
		$arrayCities = Core::findByIds(new CitySkeleton(), $arrayCitiesId);
		
		if(count($arrayCities) == 0){
			$citiesLabel = "= No city =";
		} else {
			$citiesLabel = "";
			foreach($arrayCities as $city) {
				if($citiesLabel != ""){
					$citiesLabel.= " , ";
				}
				$citiesLabel.= $city->get('labelCity');
			}
		}
		
		if(Core::verifIntegrity($country, $country->get('country_id')) == ""){
			$linkDelete = $this->CreateLink($id, 'editCountryDelete', $returnid, 'delete',array('country_id'=>$country->get('country_id')));
		} else {
			$linkDelete = '<span style="color:#CCC">still used</span>';
		}
		
	
		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($country->get('country_id'))."</td>
				<td>".$this->securize($country->get('labelCountry'))."</td>
				<td>".$this->securize($citiesLabel)."</td>
				<td>".$linkDelete.
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editCountry', $returnid, 'edit',array('country_id'=>$country->get('country_id'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There is " . $count . " CountrySkeleton(s) into the database. Would you like to <b>$link</b> another one ?</p>";

?>