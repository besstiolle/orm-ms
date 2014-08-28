<?php

if (!function_exists("cmsms")) exit;

if(empty($params['url']) || empty($params['lang_iso']) || empty($params['text'])){
	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$comment = new CommentSkeleton();
$comment->set('url', $params['url']);
$comment->set('lang_iso', $params['lang_iso']);
$comment->set('text', $params['text']);

try{
	// We simply save the entity
	$comment->save();
	// Please note that this code could also work.
	//OrmCore::insertEntity($comment);
	


	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
	
// The illegaArgument will happen each time you don't controle enough the data before inserting them
} catch (OrmIllegalArgumentException $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the user
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'editComment', $returnid, $params);
}
?>