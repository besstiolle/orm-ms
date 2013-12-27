<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['user_id'])){
	//Let's retrieve our user !
	$user = OrmCore::findById(new UserSkeleton(), $params['user_id']);
	$action = "Edition";
	if($user == null){
		// We create a new one
		$user = new UserSkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$user = new UserSkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editUserSave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

?>
<h2><?php echo $action; ?> of a UserSkeleton</h2>

<?php echo $error ?>
<?php echo $formStart; ?>
	<input type='hidden' name='<?php echo $id; ?>user_id' value='<?php echo $this->securize($user->get('user_id')); ?>' />
	<?php if($user->get('date_creation') != '') {
		echo "Created : ".date("Y-m-d",$user->get('date_creation')); 
	} ?><br/>
	<label for='login'>Login : </label><input type='text' name='<?php echo $id; ?>login' value='<?php echo $this->securize($user->get('login')); ?>' /><br/>
	<label for='name'>Name : </label><input type='text' name='<?php echo $id; ?>name' value='<?php echo $this->securize($user->get('name')); ?>' /><br/>
	<label for='description'>Description : </label><textarea name='<?php echo $id; ?>description' ><?php echo $this->securize($user->get('description')); ?></textarea><br/>
	<?php echo $submit; echo $return; ?>
</form>