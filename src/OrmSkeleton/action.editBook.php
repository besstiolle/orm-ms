<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['book_id'])){
	//Let's retrieve our book !
	$book = Core::findById(new BookSkeleton(), $params['book_id']);
	$action = "Edition";
	if($book == null){
		// We create a new one
		$book = new BookSkeleton();
		$action = "Creation";
	}
} else {
	// We create a new one
	$book = new BookSkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editBookSave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

?>
<h2><?php echo $action; ?> of a BookSkeleton</h2>

<?php echo $error ?>
<?php echo $formStart; ?>
	<input type='hidden' name='<?php echo $id; ?>book_id' value='<?php echo $this->securize($book->get('book_id')); ?>' />
	<label for='title'>Title : </label><input type='text' name='<?php echo $id; ?>title' value='<?php echo $this->securize($book->get('title')); ?>' /><br/>
	<label for='description'>Description : </label><input type='text' name='<?php echo $id; ?>description' value='<?php echo $this->securize($book->get('description')); ?>' /><br/>
	<label for='uuid'>UUID : </label><input type='text' name='<?php echo $id; ?>uuid' value='<?php echo $this->securize($book->get('uuid')); ?>' /> example : <?php echo CORE::generateUUID(); ?><br/>
	<?php echo $submit; ?>
</form>