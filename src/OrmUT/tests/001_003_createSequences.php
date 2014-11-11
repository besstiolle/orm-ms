
<p class='title'>001/003 : do we have all the sequences created ?</p>
<?php
	$requete = "SHOW TABLES FROM `".$config['db_name']."` LIKE '%_module_ormut_%_seq'";
	$result = $db->execute($requete);
	if ($result === false)
    {
        throw new Exception("Database error durant la requête!".$db->ErrorMsg());
    }
	
	$expected = 4; 
	$result = $result->RecordCount();
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected sequences, we have got $result sequences</p>";
?>
