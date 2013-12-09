<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "CitySkeleton" whenever i want in my module
$city = new CitySkeleton();

// In the same way i can interrogate the table of CitySkeleton : 
$count = Core::countAll(new CitySkeleton());

$link = $this->CreateLink($id, 'editCity', $returnid, 'add');

echo "<table class='pagetable' cellspacing='0'><tr>
		<th>&nbsp;</th>
		<th>label</th>
		<th>country</th>
		<th>&nbsp;</th>
	   </tr>";
if($count == 0){
	echo "<tr><td colspan='4'><center>no record in database</center></td></tr>";
} else {
	//I can also retrieve all the CitySkeleton
	$all = Core::findAll(new CitySkeleton());
	
	//And iterate over each one
	foreach($all as $city){
	
		//We'll only have the id of the country, so we need to retrieve the Entity Country selected
		$country_id = $city->get('country');
		$country = Core::findById(new CountrySkeleton(), $country_id);
		
		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($city->get('city_id'))."</td>
				<td>".$this->securize($city->get('labelCity'))."</td>
				<td>".$this->securize($country->get('labelCountry'))." (#".$city->get('country').")</td>
				<td>".$this->CreateLink($id, 'editCityDelete', $returnid, $img_delete,array('city_id'=>$city->get('city_id'))).
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editCity', $returnid, $img_edit,array('city_id'=>$city->get('city_id'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There are " . $count . " CitySkeleton(s) into the database. Would you like to <b>$link</b> another one ?</p>";

?>