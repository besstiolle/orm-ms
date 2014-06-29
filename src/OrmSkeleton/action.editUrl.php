<?php

if (!function_exists("cmsms")) exit;

$url = null;
if(!empty($params['url']) && !empty($params['lang_iso'])){
	//Let's retrieve our url ! but we can't call findById(s) because UrlSkeleton is a multi-PrimaryKey.
	$example = new OrmExample();
	$example->addCriteria('url', OrmTypeCriteria::$EQ, array($params['url']));
	$example->addCriteria('lang_iso', OrmTypeCriteria::$EQ, array($params['lang_iso']));
	$urls = OrmCore::findByExample(new UrlSkeleton(), $example);
	if(count($urls) > 1){
		// We can't find 2 entity with the same couple of primary key
		$params['error'] = "UrlSkeleton with dupplicate url & lang_iso found";
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	}
	if(!empty($urls)){
		$url = $urls[0];
	} 
	$action = "Edition";
} 


if($url == null){
	// We create a new one
	$url = new UrlSkeleton();
	$action = "Creation";
}

$formStart = $this->CreateFormStart($id, 'editUrlSave');
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

?>
<h2><?php echo $action; ?> of a UrlSkeleton</h2>

<?php echo $error ?>
<?php echo $formStart; ?>
	<label for='title'>Url : </label><input type='text' name='<?php echo $id; ?>url' value='<?php echo $this->securize($url->get('url')); ?>' /><br/>
	<label for='title'>Lang_iso : </label><input type='text' name='<?php echo $id; ?>lang_iso' value='<?php echo $this->securize($url->get('lang_iso')); ?>' /><br/>
	<label for='title'>Title : </label><input type='text' name='<?php echo $id; ?>title' value='<?php echo $this->securize($url->get('title')); ?>' /><br/>
	<label for='description'>Description : </label><textarea name='<?php echo $id; ?>description' ><?php echo $this->securize($url->get('description')); ?></textarea><br/>
	<?php echo $submit; echo $return; ?>
</form>