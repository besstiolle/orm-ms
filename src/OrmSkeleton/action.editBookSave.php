<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['book_id'])){
	//Let's retrieve our book !
	$book = Core::findById(new BookSkeleton(), $params['book_id']);
} else {
	$book = new BookSkeleton();
}	
if(!empty($params['title'])){
	$book->set('title', $params['title']);
}
if(!empty($params['description'])){
	$book->set('description', $params['description']);
}	
if(!empty($params['uuid'])){
	$book->set('uuid', $params['uuid']);
}	

try{
	// We simply save the entity
	$book->save();
	// Please note that this code could also work.
	//Core::insertEntity($book);
	
	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
	
// The illegaArgument will happen each time you don't controle enough the data before inserting them
} catch (IllegalArgumentException $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the user
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'editBook', $returnid, $params);
}
?>