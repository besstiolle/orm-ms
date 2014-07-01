<?php

if (!function_exists("cmsms")) exit;

$comment = null;
if(!empty($params['comment_id'])){

	//Let's retrieve our comment !
	$comment = OrmCore::findById(new CommentSkeleton(), $params['comment_id']);
} 

if($comment == null){
	// we can't delete something that doesn't exist
	$params['error'] = "CommentSkeleton not found";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

try{
	// We simply delete the entity
	$comment->delete();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
} catch (Exception $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the comment
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
?>