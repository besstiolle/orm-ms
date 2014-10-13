
<p class='title'>003/002 : Test the Format of CAST::$INTEGER Field ?</p>
<?php
	$fieldname = "dummy";

	/********** OrmCAST::$STRING a simple string **********/

	$field = new OrmField($fieldname, OrmCast::$INTEGER, 10);

	UtilsTest::assertIsTrue(OrmUtils::isAValidFormat($field, 99));
	UtilsTest::assertIsTrue(OrmUtils::isAValidFormat($field, "99"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, ""));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "  "));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, " 
	 "));
	UtilsTest::assertIsTrue(OrmUtils::isAValidFormat($field, "-99"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "0.0"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "-9.9"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, NULL));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "-9.9a"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "-99a"));

	UtilsTest::assertIsTrue(OrmUtils::isAValidFormat($field, -9223372036854775807));	
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, -9223372036854775808));
	UtilsTest::assertIsTrue(OrmUtils::isAValidFormat($field, 9223372036854775807));	
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, 9223372036854775808));

	echo "<br/>";

?>