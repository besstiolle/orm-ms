<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "BookSkeleton" whenever i want in my module
$book = new BookSkeleton();

// In the same way i can interrogate the table of BookSkeleton : 
$count = Core::countAll(new BookSkeleton());

$link = $this->CreateLink($id, 'editBook', $returnid, 'add');

echo "<table class='pagetable' cellspacing='0'><tr>
		<th>&nbsp;</th>
		<th>title</th>
		<th>description</th>
		<th>&nbsp;</th>
	</tr>";
if($count == 0){
	echo "<tr><td colspan='5'><center>no record in database</center></td></tr>";
} else {
	//I can also retrieve all the BookSkeleton
	$all = Core::findAll(new BookSkeleton());
	
	//And iterate over each one
	foreach($all as $book){
	
		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($book->get('book_id'))."</td>
				<td>".$this->securize($book->get('title'))."</td>
				<td>".$this->securize($book->get('description'))."</td>
				<td>".$this->CreateLink($id, 'editBookDelete', $returnid, 'delete',array('book_id'=>$book->get('book_id'))).
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editBook', $returnid, 'edit',array('book_id'=>$book->get('book_id'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There is " . $count . " BookSkeleton(s) into the database. Would you like to <b>$link</b> another one ?</p>";

?>