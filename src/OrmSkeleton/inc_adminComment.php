<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "CommentSkeleton" whenever i want in my module
$comment = new CommentSkeleton();

// In the same way i can interrogate the table of CommentSkeleton : 
$count = OrmCore::countAll(new CommentSkeleton());

//We simply find all the Url with no limit and no sort
$all = OrmCore::findAll(new CommentSkeleton());

$randomComment = null;
if($count > 0){
	$rand = mt_rand(0,$count-1);
	$randomComment = $all[$rand];
}


$smarty->assign('count',$count);
$smarty->assign('randomComment',$randomComment);

echo $this->ProcessTemplate('comment_view.tpl');
?>