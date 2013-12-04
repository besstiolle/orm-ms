<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['city_id'])){
	//Let's retrieve our city !
	$city = Core::findById(new CitySkeleton(), $params['city_id']);
	$action = "Edition";
	if($city == null){
		// We create a new one
		$city = new CitySkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$city = new CitySkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editCitySave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

//We need to propose all countries
$countries = Core::findAll(new CountrySkeleton());
$items = array();
$items['  = Choose one Country =  '] = '';
foreach($countries as $country){ 
	$items[$country->get('labelCountry')] = $country->get('country_id');
}
$selectedvalue = '-1';
if($city->get('country') != '') {
	$selectedvalue = $city->get('country');
}
$selectCountries = $this->CreateInputDropdown($id, 'country', $items, -1, $selectedvalue); 

?>
<h2><?php echo $action; ?> of a CitySkeleton</h2>

<?php echo $error ?>
<?php echo $formStart; ?>
	<input type='hidden' name='<?php echo $id; ?>city_id' value='<?php echo $city->get('city_id'); ?>' />

	<label for='labelCity'>label of the city : </label><input type='text' name='<?php echo $id; ?>labelCity' value='<?php echo $city->get('labelCity'); ?>' /><br/>
	<label for='country'>country : </label><?php echo $selectCountries; ?><br/>
	<?php echo $submit; ?>
</form>