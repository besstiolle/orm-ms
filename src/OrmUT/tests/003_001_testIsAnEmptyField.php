
<p class='title'>003/001 : Test the Function OrmUtil::IsAnEmptyField ?</p>
<?php
	
	$fieldname = "dummy";

	/********** OrmCAST::$STRING a simple string **********/

	$field = new OrmField($fieldname, OrmCast::$STRING, 10);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));

	echo "<br/>";

	/********** OrmCAST::$BUFFER a field with no limit of size (except the mysql natural limit **********/

	$field = new OrmField($fieldname, OrmCast::$BUFFER, NULL);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));

	echo "<br/>";

	/********** OrmCAST::$INTEGER an integer **********/

	$field = new OrmField($fieldname, OrmCast::$INTEGER, 3);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));

	echo "<br/>";


	/********** OrmCAST::$NUMERIC a field for a real number (eg : with coma) **********/

	$field = new OrmField($fieldname, OrmCast::$NUMERIC, 3);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";


	/********** OrmCAST::$DOUBLE a field for big number **********/

	$field = new OrmField($fieldname, OrmCast::$DOUBLE, 3);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";

	 
	/********** OrmCAST::$DATE a field date **********/

	$field = new OrmField($fieldname, OrmCast::$DATE);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";


	/********** OrmCAST::$TIME a field time **********/

	$field = new OrmField($fieldname, OrmCast::$TIME);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";


	/********** OrmCAST::$TS a field timestamp **********/

	$field = new OrmField($fieldname, OrmCast::$TS);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";


	/********** OrmCAST::$DATETIME a field dateTime **********/

	$field = new OrmField($fieldname, OrmCast::$DATETIME);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";


	/********** OrmCAST::$UUID a field UUID **********/

	$field = new OrmField($fieldname, OrmCast::$UUID);

	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "zz"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, ""));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, "  "));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, " 
	 "));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::IsAnEmptyField($field, "-3"));
	UtilsTest::assertIsTrue(OrmUtils::IsAnEmptyField($field, NULL));
	
	echo "<br/>";


?>