<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "CommentSkeleton" whenever i want in my module
$url = new CommentSkeleton();

// In the same way i can interrogate the table of CommentSkeleton : 
$count = OrmCore::countAll(new CommentSkeleton());

//We simply find all the Url with no limit and no sort
$all = OrmCore::findAll(new CommentSkeleton());

echo "There are " . $count . " CommentSkeleton(s) into the database.";

if($count > 0){
	$rand = mt_rand(0,$count-1);
	$randomComment = $all[$rand];

	echo " The random comment is <b>{$randomComment->get('text')} </b>
			 on : <b>{$randomComment->get('myurl')->get('title')}</b>";
}



?>