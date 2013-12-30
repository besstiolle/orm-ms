
<p class='title'>001/001 : can we lunch creation of tables</p>
<?php
	try{
		reinitAllTables($mod);
		echo "<p class='{$cssSuccess}'>It seems it work :)</p>";
	} catch (Exception $e){
		echo "<p class='{$cssError}'>fail ... :(</p>";
	}
	
?> 