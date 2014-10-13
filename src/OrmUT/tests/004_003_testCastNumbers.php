
<p class='title'>004/003 : Test the Cast Mapping with numbers ?</p>
<?php
	
	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());
	
	
	$integer = -2147483647; // MIN/MAX SQL = -2147483647	2147483647
	$numeric = "-1225";
	$double = "-129999.25";
	
	//Test insert
	try{
		$cast = new CastOrmUTNumbers();
		$cast->set("aInteger",$integer);
		$cast->set("aIntegerNull",null);
		$cast->set("aNumeric",$numeric);
		$cast->set("aNumericNull",null);
		$cast->set("aDouble",$double);
		$cast->set("aDoubleNull",null);
		$cast->save();
		echo "<p class='$cssSuccess'>saving entity with success</p>";
	} catch (Exception $o){
		echo "<p class='$cssError'>Exception during \$cast->save()</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	$cast = $elements[0];
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),$integer);
	UtilsTest::assertIsNull($cast->get("aIntegerNull"));
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsNull($cast->get("aNumericNull"));
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);
	UtilsTest::assertIsNull($cast->get("aDoubleNull"));
	
	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());

	/**************************************************************/
	
	// integer null ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger",null);
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	UtilsTest::assertIsTrue(empty($elements));
	
	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());

	/**************************************************************/
	
	// integer empty ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger","");
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	UtilsTest::assertIsTrue(empty($elements));
	
	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());
	
	/**************************************************************/
		
	// integer empty ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger","  ");
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmIllegalArgumentException, that's not okay</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmIllegalArgumentException</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	UtilsTest::assertIsTrue(empty($elements));
	
	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());

	/*******************************************************************/
		
	// integer string ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger","z99");
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmCastFormatException, that's not okay</p>";
	} catch (OrmCastFormatException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmCastFormatException</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	UtilsTest::assertIsTrue(empty($elements));

	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());

	/*******************************************************************/


	// integer with negatif ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger","-99");
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssSuccess'>saving entity with success</p>";
	} catch (OrmIllegalArgumentException $o){
		echo "<p class='$cssError'>Exception during \$cast->save()</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	$cast = $elements[0];
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),"-99");
	UtilsTest::assertIsNull($cast->get("aIntegerNull"));
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsNull($cast->get("aNumericNull"));
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);
	UtilsTest::assertIsNull($cast->get("aDoubleNull"));
	
	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());

	/**********************************************************/
	
	
	// integer with dot ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger","99.9");
		$cast->set("aNumeric",$numeric);
		$cast->set("aDouble",$double);
		$cast->save();
		echo "<p class='$cssError'>we expected OrmCastFormatException, that's not okay</p>";
	} catch (OrmCastFormatException $o){
		echo "<p class='$cssSuccess'>As expected we've got a OrmCastFormatException</p>";
	}
	
	//
	$elements = OrmCore::findAll($cast);
	UtilsTest::assertIsTrue(empty($elements));
	
	echo "<br/>";

	//First, drop the table, recreate it
	OrmCore::dropTable(new CastOrmUTNumbers());
	OrmCore::createTable(new CastOrmUTNumbers());

	/************************************************************/
	
	// integer zero ?
	$cast = new CastOrmUTNumbers();
	try{
		$cast->set("aInteger","0");
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
	
	UtilsTest::assertIsEquals($cast->get("aInteger"),"0");
	UtilsTest::assertIsNull($cast->get("aIntegerNull"));
	UtilsTest::assertIsEquals($cast->get("aNumeric"),$numeric);
	UtilsTest::assertIsNull($cast->get("aNumericNull"));
	UtilsTest::assertIsEquals($cast->get("aDouble"),$double);
	UtilsTest::assertIsNull($cast->get("aDoubleNull"));
	
	echo "<br/>";
	

	
	//Finally, drop the table
	//OrmCore::dropTable(new CastOrmUTString());
	
?>