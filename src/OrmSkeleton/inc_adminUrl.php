<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "UrlSkeleton" whenever i want in my module
$url = new UrlSkeleton();

// In the same way i can interrogate the table of UrlSkeleton : 
$count = OrmCore::countAll(new UrlSkeleton());

$add = $this->CreateLink($id, 'editUrl', $returnid, 'add');

$all = array();
$edit = array();
$delete = array();
$commentslink = array();

if($count !== 0){
	
	//We simply find all the Url with no limit and no sort
	$all = OrmCore::findAll(new UrlSkeleton());


	//And iterate over each one
	foreach($all as $url){

		if(!isset($edit[$url->get('url')])){
			$edit[$url->get('url')] = array();
			$delete[$url->get('url')] = array();
			$commentslink[$url->get('url')] = array();
		}

		$edit[$url->get('url')][$url->get('lang_iso')] = $this->CreateLink($id, 'editUrl', $returnid, $img_edit,array('url'=>$url->get('url'), 'lang_iso'=>$url->get('lang_iso')));
		$delete[$url->get('url')][$url->get('lang_iso')] = $this->CreateLink($id, 'editUrlDelete', $returnid, $img_delete,array('url'=>$url->get('url'), 'lang_iso'=>$url->get('lang_iso')));
		
		//Like for Country and its city, we automaticly have the comments of the url like this
		// $url->get('comments')
		// It will be an array composed by all comments also in a form of an array key => value
		$commentslink[$url->get('url')][$url->get('lang_iso')] = "<a href='".$this->CreateLink($id, 'editComment', $returnid, '',array('url'=>$url->get('url'), 'lang_iso'=>$url->get('lang_iso')),'',true)
					."' >".count($url->get('comments'))." Comment(s) ".$img_view."</a>";

	}
}

$smarty->assign('all',$all);
$smarty->assign('count',$count);
$smarty->assign('edit',$edit);
$smarty->assign('delete',$delete);
$smarty->assign('commentslink',$commentslink);
$smarty->assign('add',$add);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('url_view.tpl');
?>