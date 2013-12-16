<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "BookSkeleton" whenever i want in my module
$book = new BookSkeleton();

// In the same way i can interrogate the table of BookSkeleton : 
$count = OrmCore::countAll(new BookSkeleton());

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
	//I can also retrieve all the BookSkeleton, ordered by uuid, then title desc
	
	// ORDER BY can be set like following, or directly inside a constructor
	/*
	$orderBy = new OrmOrderBy();
	$orderBy->addAsc('description');
	$orderBy->addDesc('title');
	*/
	
	// Add "limit"
	// Solution 1:
	/*
	$limit = new OrmLimit();
	$limit->setRowCount(2);
	$all = OrmCore::findAll(new BookSkeleton(), $orderBy, $limit); 
	*/
	// Solution 2
	$all = OrmCore::findAll(new BookSkeleton(),  
							new OrmOrderBy(array('description' => OrmOrderBy::$ASC, 'title' => OrmOrderBy::$DESC)),
							new OrmLimit(0, 2));
	
	//And iterate over each one
	foreach($all as $book){
	
		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($book->get('book_id'))."</td>
				<td>".$this->securize($book->get('title'))."</td>
				<td>".$this->securize($book->get('description'))."</td>
				<td>".$this->CreateLink($id, 'editBookDelete', $returnid, $img_delete,array('book_id'=>$book->get('book_id'))).
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editBook', $returnid, $img_edit,array('book_id'=>$book->get('book_id'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There are " . $count . " BookSkeleton(s) into the database. (Only 2 displayed as we set a 2 limit.) Would you like to <b>$link</b> another one ?</p>";

?>