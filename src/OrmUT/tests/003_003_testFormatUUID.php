
<p class='title'>003/003 : Test the Format of CAST::$UUID Field ?</p>
<?php
	$fieldname = "dummy";

	/********** OrmCAST::$STRING a simple string **********/

	$field = new OrmField($fieldname, OrmCast::$UUID);

	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, 99));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "99"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, ""));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "  "));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, " 
	 "));
	UtilsTest::assertIsTrue(OrmUtils::isAValidFormat($field, "db044410-52ca-11e4-a3e4-142a94684c47"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "db044410-52ca-11e4-a3e4-142a94684c472"));
	UtilsTest::assertIsFalse(OrmUtils::isAValidFormat($field, "zb044410-52ca-11e4-a3e4-142a94684c4"));

	echo "<br/>";

?>