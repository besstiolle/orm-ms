<?php

if (!function_exists("cmsms")) exit;


$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}
echo $error;

$img_delete = cmsms()->variables['admintheme']->DisplayImage('icons/system/delete.gif','delete','','','systemicon');
$img_edit = cmsms()->variables['admintheme']->DisplayImage('icons/system/edit.gif','edit','','','systemicon');
$img_view = cmsms()->variables['admintheme']->DisplayImage('icons/system/view.gif','view','','','systemicon');

echo '<h1>Basic Example : A single Entity without connection</h1>';
echo '<img src="http://yuml.me/diagram/classic/class/[User%7Cuser_id%20(PK);login;name;description;date_creation;hour_last_modification]"/>';
include_once('inc_adminUser.php');

echo '<h1>Basic Example : 2 Entities with Foreign Key and Associate Key</h1>';
echo '<img src="http://yuml.me/diagram/classic/class/[City%7Ccity_id%20(PK);labelCity;country%20(FK)]<>1-country 1>[Country%7Ccountry_id%20(PK);labelCountry%7C~ cities%20(AK)]"/>';
echo '<img src="http://yuml.me/diagram/classic/class/[Country%7Ccountry_id%20(PK);labelCountry%7C~ cities%20(AK)]<>1-cities 0..*>[City%7Ccity_id%20(PK);labelCity;country%20(FK)]"/>';
include_once('inc_adminCountry.php');
include_once('inc_adminCity.php');


echo '<h1>Advanced Examples : A single Entities with more option (sort/indexes/...)</h1>';
echo '<img src="http://yuml.me/diagram/classic/class/[Book%7Cbook_id%20(PK);title;description;uuid]"/>';
include_once('inc_adminBook.php');

echo '<h1>Advanced Examples : 2 Entities with Composite Primary Key, Foreign & Associate Key, Alias Field, ...</h1>';
echo '<img src="http://yuml.me/diagram/classic/class/[Comment%7Curl%20(PK);comment_id;text;url%20(FK);lang_iso%20(FK)%7C~ myurl (alias)]<>1-myurl 1>[Url%7Curl%20(PK);lang_iso%20(PK);title;description;%7C~ comments%20(AK)]"/>';
echo '<img src="http://yuml.me/diagram/classic/class/[Url%7Curl%20(PK);lang_iso%20(PK);title;description;%7C~ comments%20(AK)]<>1-comments 0..*>[Comment%7Curl%20(PK);comment_id;text;url%20(FK);lang_iso%20(FK)%7C~ myurl (alias)]"/>';
include_once('inc_adminUrl.php');
include_once('inc_adminComment.php');
?>