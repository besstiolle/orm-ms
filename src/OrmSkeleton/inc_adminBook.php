<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "BookSkeleton" whenever i want in my module
$book = new BookSkeleton();

// In the same way i can interrogate the table of BookSkeleton : 
$count = OrmCore::countAll(new BookSkeleton());

$add = $this->CreateLink($id, 'editBook', $returnid, 'add');

$all = array();
$edit = array();
$delete = array();

if($count !== 0){
	//I can also retrieve all the BookSkeleton, ordered by uuid, then title desc
	
	// ORDER BY can be set like following, or directly inside a constructor
	/*
	$orderBy = new OrmOrderBy();
	$orderBy->addAsc('description');
	$orderBy->addDesc('title');
	*/
	
	// Add "limit"
	// Solution 1:
	/*
	$limit = new OrmLimit();
	$limit->setRowCount(2);
	$all = OrmCore::findAll(new BookSkeleton(), $orderBy, $limit); 
	*/
	// Solution 2
	$all = OrmCore::findAll(new BookSkeleton(),  
							new OrmOrderBy(array('description' => OrmOrderBy::$ASC, 'title' => OrmOrderBy::$DESC)),
							new OrmLimit(0, 2));

	//And iterate over each one
	foreach($all as $book){
		$delete[$book->get('book_id')] = $this->CreateLink($id, 'editBookDelete', $returnid, $img_delete,array('book_id'=>$book->get('book_id')));
		$edit[$book->get('book_id')] = $this->CreateLink($id, 'editBook', $returnid, $img_edit,array('book_id'=>$book->get('book_id')));
	
	}
}
$smarty->assign('all',$all);
$smarty->assign('count',$count);
$smarty->assign('edit',$edit);
$smarty->assign('delete',$delete);
$smarty->assign('add',$add);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('book_view.tpl');


?>