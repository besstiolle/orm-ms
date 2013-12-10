<?php

if (!function_exists("cmsms")) exit;

// I can instantiate a "UserSkeleton" whenever i want in my module
$user = new UserSkeleton();

// In the same way i can interrogate the table of UserSkeleton : 
$count = OrmCore::countAll(new UserSkeleton());

$link = $this->CreateLink($id, 'editUser', $returnid, 'add');

echo "<table class='pagetable' cellspacing='0'><tr>
		<th>&nbsp;</th>
		<th>login</th>
		<th>name</th>
		<th>date_creation</th>
		<th>hour_last_modification</th>
		<th>&nbsp;</th>
	</tr>";
if($count == 0){
	echo "<tr><td colspan='5'><center>no record in database</center></td></tr>";
} else {
	//I can also retrieve all the UserSkeleton
	$all = OrmCore::findAll(new UserSkeleton());
	
	//And iterate over each one
	foreach($all as $user){
	
		// We can easily get all the values with the $object->get('fieldname') syntax
		echo "<tr>
				<td>".$this->securize($user->get('user_id'))."</td>
				<td>".$this->securize($user->get('login'))."</td>
				<td>".$this->securize($user->get('name'))."</td>
				<td>".date("Y-m-d",$user->get('date_creation'))."</td> 
				<td>".$this->securize($user->get('hour_last_modification'))."</td>
				<td>".$this->CreateLink($id, 'editUserDelete', $returnid, $img_delete,array('user_id'=>$user->get('user_id'))).
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editUser', $returnid, $img_edit,array('user_id'=>$user->get('user_id'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There are " . $count . " UserSkeleton(s) into the database. Would you like to <b>$link</b> another one ?</p>";

?>