<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "UrlSkeleton" whenever i want in my module
$url = new UrlSkeleton();

// In the same way i can interrogate the table of UrlSkeleton : 
$count = OrmCore::countAll(new UrlSkeleton());

$link = $this->CreateLink($id, 'editUrl', $returnid, 'add');

echo "<table class='pagetable' cellspacing='0'><tr>
		<th>url</th>
		<th>lang_iso</th>
		<th>title</th>
		<th>description</th>
		<th>&nbsp;</th>
	</tr>";
if($count == 0){
	echo "<tr><td colspan='5'><center>no record in database</center></td></tr>";
} else {
	// Let's find all the UrlSkeleton
	$all = OrmCore::findAll(new UrlSkeleton()); 
	

	//And iterate over each one
	foreach($all as $url){
	
		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($url->get('url'))."</td>
				<td>".$this->securize($url->get('lang_iso'))."</td>
				<td>".$this->securize($url->get('title'))."</td>
				<td>".$this->securize($url->get('description'))."</td>
				<td>".$this->CreateLink($id, 'editUrlDelete', $returnid, $img_delete,array('url'=>$url->get('url'),'lang_iso'=>$url->get('lang_iso'))).
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editUrl', $returnid, $img_edit,array('url'=>$url->get('url'), 'lang_iso'=>$url->get('lang_iso'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There are " . $count . " UrlSkeleton(s) into the database. Would you like to <b>$link</b> another one ?</p>";

?>