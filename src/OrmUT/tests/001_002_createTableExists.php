
<p class='title'>001/002 : do we have all the tables created ?</p>
<?php
	$requete = "SHOW TABLES FROM `".$config['db_name']."` LIKE '%_module_ormut_%'";
	$result = $db->execute($requete);
	if ($result === false)
    {
        throw new Exception("Database error durant la requête!".$db->ErrorMsg());
    }
	
	$expected = 10; // don't forget the Sequence
	$result = $result->RecordCount();
	$class = '';
	if($result == $expected){
		$class = $cssSuccess;
	} else {
		$class = $cssError;
	}
	echo "<p class='$class'>we expected $expected tables, we have got $result tables</p>";
?>