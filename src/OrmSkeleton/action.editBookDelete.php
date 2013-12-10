<?php

if (!function_exists("cmsms")) exit;

$book = null;
if(!empty($params['book_id'])){
	//Let's retrieve our book !
	$book = OrmCore::findById(new BookSkeleton(), $params['book_id']);
} 

if($book == null){
	// we can't delete something that doesn't exist
	$params['error'] = "BookSkeleton not found";
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

try{
	// We simply delete the entity
	$book->delete();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
} catch (Exception $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the book
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
?>