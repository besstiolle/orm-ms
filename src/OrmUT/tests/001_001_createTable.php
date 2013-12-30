
<p class='title'>Test #2 : can we lunch creation of tables</p>
<?php
	try{
		$entities = $mod->getAllInstances();
		foreach($entities as $anEntity)
		{
			OrmCore::dropTable($anEntity);
			OrmCore::createTable($anEntity);
		}
		echo "<p class='{$cssSuccess}'>It seems it work :)</p>";
	} catch (Exception $e){
		echo "<p class='{$cssError}'>fail ... :(</p>";
	}
	
?> 