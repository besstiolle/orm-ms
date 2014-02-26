<?php

if (!function_exists("cmsms")) exit;


$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}
echo $error;

$img_delete = cmsms()->variables['admintheme']->DisplayImage('icons/system/delete.gif','delete','','','systemicon');
$img_edit = cmsms()->variables['admintheme']->DisplayImage('icons/system/edit.gif','edit','','','systemicon');

echo "<h1>Basic Examples</h1>";

include_once('inc_adminUser.php');
include_once('inc_adminCountry.php');
include_once('inc_adminCity.php');

echo "<h1>Advanced Examples</h1>";

include_once('inc_adminBook.php');
include_once('inc_adminUrl.php');

?>