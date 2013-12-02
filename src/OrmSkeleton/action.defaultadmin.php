<?php

if (!function_exists("cmsms")) exit;

// I can instanciate a "UserSkeleton" whenever i want in my module
$user = new UserSkeleton();

// In the same way i can interrogate the table of UserSkeleton : 
$count = Core::countAll(new UserSkeleton());

$error = '';
if(!empty($params['error'])) {
	$error = "<h2 style='color:#FF0000;'>".$params['error']."</h2>";
}

echo $error;
$link = $this->CreateLink($id, 'editUser', $returnid, 'add');

echo "<table class='pagetable' cellspacing='0'><tr><th>&nbsp;</th><th>login</th><th>name</th><th>date_creation</th><th>hour_last_modification</th><th>&nbsp;</th></tr>";
if($count == 0){
	echo "<tr><td colspan='5'><center>no record in database</center></td></tr>";
} else {
	//I can also retrive all the UserSkeleton
	$all = Core::findAll(new UserSkeleton());
	
	//And iterate over each one
	foreach($all as $user){
	
		// We can easily get all the values with the $object->get('fieldname') syntaxe
		echo "<tr>
				<td>".$user->get('user_id')."</td>
				<td>".$user->get('login')."</td>
				<td>".$user->get('name')."</td>
				<td>".date("Y-m-d",$user->get('date_creation'))."</td> 
				<td>".$user->get('hour_last_modification')."</td>
				<td>".$this->CreateLink($id, 'editUserDelete', $returnid, 'delete',array('user_id'=>$user->get('user_id'))).
					"&nbsp;-&nbsp;".
					$this->CreateLink($id, 'editUser', $returnid, 'edit',array('user_id'=>$user->get('user_id'))).
				"</td>
			</tr>";
	}
}
echo "</table>";
echo "<p>There is " . $count . " UserSkeleton(s) into the database. Would you like to <b>$link</b> another one ?</p>";

?>