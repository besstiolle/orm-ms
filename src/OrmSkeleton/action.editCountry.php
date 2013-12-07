<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['country_id'])){
	//Let's retrieve our country !
	$country = Core::findById(new CountrySkeleton(), $params['country_id']);
	$action = "Edition";
	if($country == null){
		// We create a new one
		$country = new CountrySkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$country = new CountrySkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editCountrySave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

?>
<h2><?php echo $action; ?> of a CountrySkeleton</h2>

<?php echo $error ?>
<?php echo $formStart; ?>
	<input type='hidden' name='<?php echo $id; ?>country_id' value='<?php echo $this->securize($country->get('country_id')); ?>' />
	<label for='labelCountry'>Label of the Country : </label><input type='text' name='<?php echo $id; ?>labelCountry' value='<?php echo $this->securize($country->get('labelCountry')); ?>' /><br/>
	<?php echo $submit; ?>
</form>