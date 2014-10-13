
<p class='title'>004/001 : Test the Cast Mapping with date, time & datetime ?</p>
<?php
	
	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTDateTime());
	OrmCore::createTable(new CastOrmUTDateTime());
	
	list($currentUS, $currentTS) = explode(" ", microtime());
	$currentTIME = date("H:i:s", $currentTS );
	$currentDT = $currentTS;
	$currentDATE = strtotime(date("Y-m-d", $currentTS ));
	
	//Test insert
	$cast = new CastOrmUTDateTime();
	$cast->set("aDate",$currentDATE);
	$cast->set("aDateNull",null);
	$cast->set("aTime",$currentTIME);
	$cast->set("aTimeNull",null);
	$cast->set("aTS",$currentTS);
	$cast->set("aTSNull",null);
	$cast->set("aDateTime",$currentDT);
	$cast->set("aDateTimeNull",null);
	try{
		$cast->save();
		echo "<p class='$cssSuccess'>saving entity with success</p>";
	} catch (Exception $o){
		echo "<p class='$cssError'>Exception during \$cast->save()</p>";
		echo $o->getMessage();
	}
	
	// DATE null ?
	try{
		$cast->set("aDate",null);
		$cast->set("aTime",$currentTIME);
		$cast->set("aTS",$currentTS);
		$cast->set("aDateTime",$currentDT);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
		
	// TIME null ?
	try{
		$cast->set("aDate",$currentDATE);
		$cast->set("aTime",null);
		$cast->set("aTS",$currentTS);
		$cast->set("aDateTime",$currentDT);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
	
	// TIMESTAMP null ?
	try{
		$cast->set("aDate",$currentDATE);
		$cast->set("aTime",$currentTIME);
		$cast->set("aTS",null);
		$cast->set("aDateTime",$currentDT);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
	
	// DATETIME null ?
	try{
		$cast->set("aDate",$currentDATE);
		$cast->set("aTime",$currentTIME);
		$cast->set("aTS",$currentTS);
		$cast->set("aDateTime",null);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
	

	//
	$elements = OrmCore::findAll($cast);

	UtilsTest::assertIsEquals(count($elements), 1);

	$cast = $elements[0];

	
	UtilsTest::assertIsEquals($cast->get("aDate"),$currentDATE);
	UtilsTest::assertIsNull($cast->get("aDateNull"));
	UtilsTest::assertIsEquals($cast->get("aTime"),$currentTIME);
	UtilsTest::assertIsNull($cast->get("aTimeNull"));
	UtilsTest::assertIsEquals($cast->get("aTS"),$currentTS);
	UtilsTest::assertIsNull($cast->get("aTSNull"));
	UtilsTest::assertIsEquals($cast->get("aDateTime"),$currentDT);
	UtilsTest::assertIsNull($cast->get("aDateTimeNull"));
	

	
	//Finally, drop the table
	OrmCore::dropTable(new CastOrmUTDateTime());
?>