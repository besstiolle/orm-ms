<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['book_id'])){
	//Let's retrieve our book !
	$book = OrmCore::findById(new BookSkeleton(), $params['book_id']);
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
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

$smarty->assign('formStart',$formStart);
$smarty->assign('action',$action);
$smarty->assign('submit',$submit);
$smarty->assign('return',$return);
$smarty->assign('error',$error);
$smarty->assign('uuid',OrmCore::generateUUID());
$smarty->assign('book',$book);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('book_edit.tpl');

?>
