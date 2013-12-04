<?php

if (!function_exists("cmsms")) exit;


$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}
echo $error;

include_once('inc_adminUser.php');
include_once('inc_adminCountry.php');
include_once('inc_adminCity.php');

?>