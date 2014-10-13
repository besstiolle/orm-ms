
<p class='title'>004/003 : Test the Cast Mapping with numbers AND default values?</p>
<?php
	
	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbersWithDefaultValues());
	OrmCore::createTable(new CastOrmUTNumbersWithDefaultValues());
	
	
	$integer = -123456789;
	$numeric = "-1225";
	$double = "-129999.25";
	
	//Test insert with nothing, we must get the default value
	try{
		$cast = new CastOrmUTNumbersWithDefaultValues();
		$cast->set("aInteger",null);
		$cast->set("aNumeric",null);
		$cast->set("aDouble",null);
		$cast->save();
		echo "<p class='$cssSuccess'>saving entity with success</p>";
	} catch (Exception $o){
		echo "<p class='$cssError'>Exception during \$cast->save()</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	$cast = $elements[0];
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),$integer);
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);
	
	echo "<br/>";
	
	/**********************************************************/


	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbersWithDefaultValues());
	OrmCore::createTable(new CastOrmUTNumbersWithDefaultValues());
	
	
	$integer = 1;
	$numeric = "-2";
	$double = "-3.3";
	
	//Test insert with something, we must get the passed value in database
	try{
		$cast = new CastOrmUTNumbersWithDefaultValues();
		$cast->set("aInteger",$integer);
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssSuccess'>saving entity with success</p>";
	} catch (Exception $o){
		echo "<p class='$cssError'>Exception during \$cast->save()</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	$cast = $elements[0];
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),$integer);
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);
	
	echo "<br/>";
	
	/*****************************************************/

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbersWithDefaultValues());
	OrmCore::createTable(new CastOrmUTNumbersWithDefaultValues());
	
	
	$integer = 0;
	$numeric = "-0";
	$double = "-0.0";
	
	//Test insert with something, we must get the passed value in database
	try{
		$cast = new CastOrmUTNumbersWithDefaultValues();
		$cast->set("aInteger",$integer);
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssSuccess'>saving entity with success</p>";
	} catch (Exception $o){
		echo "<p class='$cssError'>Exception during \$cast->save()</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	$cast = $elements[0];
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),$integer);
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);

	//Try update
	$cast->save();
	$elements = OrmCore::findAll($cast);
	$cast = $elements[0];
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),$integer);
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);
	
	echo "<br/>";
?>