<?php

if (!function_exists("cmsms")) exit;

if(empty($params['url']) || empty($params['lang_iso'])){
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$admintheme = cms_utils::get_theme_object();
$img_delete = $admintheme->DisplayImage('icons/system/delete.gif','delete','','','systemicon');

//Let's retrieve the comments !
$example = new OrmExample();
$example->addCriteria('url', OrmTypeCriteria::$EQ, array($params['url']));
$example->addCriteria('lang_iso', OrmTypeCriteria::$EQ, array($params['lang_iso']));

$comments = OrmCore::findByExample(new CommentSkeleton(), $example);
$count = count($comments);

$url = $params['url'];
$lang_iso = $params['lang_iso'];

$smarty->assign('url',$url);
$smarty->assign('lang_iso',$lang_iso);
$smarty->assign('count',$count);

$delete = array();

foreach($comments as $comment){
	$delete[$comment->get('comment_id')] = 
		$this->CreateLink($id, 'editCommentDelete', $returnid, $img_delete,array('comment_id'=>$comment->get('comment_id')));
}

$smarty->assign('delete',$delete);


$formStart = $this->CreateFormStart($id, 'editCommentSave', $returnid, 'post', '',false,'',array('url'=>$params['url'], 'lang_iso'=>$params['lang_iso']));
$submit = $this->CreateInputSubmit($id, 'submit', 'submit');
$return = $this->CreateLink($id, 'defaultadmin', $returnid, 'cancel',null,null,null,null,"class='pageback ui-state-default ui-corner-all'" );

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

$smarty->assign('formStart',$formStart);
$smarty->assign('submit',$submit);
$smarty->assign('return',$return);
$smarty->assign('error',$error);
$smarty->assign('comments',$comments);
$smarty->assign('tool',new SmartyTool());

echo $this->ProcessTemplate('comment_edit.tpl');