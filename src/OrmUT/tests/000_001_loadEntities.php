
<p class='title'>000/001 : does Entities from /lib are loaded ?</p>

<?php
	$entities = $mod->getAllInstances();
	
	$expected = 8;
	$result = count($entities);
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected classes, we have got $result classes</p>";
?> 