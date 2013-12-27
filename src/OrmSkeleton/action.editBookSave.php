<?php

if (!function_exists("cmsms")) exit;

if(!empty($params['book_id'])){
	//Let's retrieve our book !
	$book = OrmCore::findById(new BookSkeleton(), $params['book_id']);
} else {
	$book = new BookSkeleton();
}	

if(!empty($params['title'])){
	$book->set('title', $params['title']);
} else {
	$book->set('title', null);
}
if(!empty($params['description'])){
	$book->set('description', $params['description']);
} else {
	$book->set('description', null);
}	
if(!empty($params['uuid'])){
	$book->set('uuid', $params['uuid']);
} else {
	$book->set('uuid', null);
}	

try{
	// We simply save the entity
	$book->save();
	// Please note that this code could also work.
	//OrmCore::insertEntity($book);
	
	//Go back to the default admin page
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
	
// The illegaArgument will happen each time you don't controle enough the data before inserting them
} catch (OrmIllegalArgumentException $e){
	// Ho ho ho... there is shitty information ...
	// Let's go inform the user
	$params['error'] = $e->getMessage();
	$this->Redirect($id, 'editBook', $returnid, $params);
}
?>