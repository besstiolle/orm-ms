<?php

if (!function_exists("cmsms")) exit;


Trace::warn("it should work also here:)");

$country = MyAutoload::getInstance($this->GetName(),'country');

print_r($country,true);

?>