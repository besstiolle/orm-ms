<?php

if (!function_exists("cmsms")) exit;


$checkUrl = $this->CreateLink ($id, 'admin_check', null, '',array(),'',true);
$generateUrl = $this->CreateLink ($id, 'admin_generate', null, '',array(),'',true);

$smarty = cmsms()->GetSmarty();
$smarty->assign("id",$id);
$smarty->assign("formstart",$this->CreateFormStart($id, 'admin_save'));
$smarty->assign("checkUrl",$checkUrl);
$smarty->assign("generateUrl",$generateUrl);

echo $this->ProcessTemplate('admin.tpl');


?>