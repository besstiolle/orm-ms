<p class='title'>002/006 : can I create an Entity with OrmEntity->save() with AK?</p>
<?php

	//First, drop the table, recreate it
	reinitAllTables($mod);
	
	$code = new ZipcodeOrmUT();
	$code->set('code', '35000');
	$code = $code->save();

	UtilsTest::assertIsEquals($code->get('zipcode_id'),1);
	UtilsTest::assertIsNull($code->get('cities'));

	$rennes = new CityOrmUT();
	$rennes->set('label', 'Rennes');
	$rennes = $rennes->save();


	UtilsTest::assertIsEquals($rennes->get('city_id'),1);
	UtilsTest::assertIsNull($rennes->get('zipcodes'));

	$assoc = new CityZipCodeOrmUT();
	$assoc->set('city_id', 1);
	$assoc->set('zipcode_id', 1);
	$assoc = $assoc->save();

/*
	//Test bug 86 with insert
	$lang = new CountryLangOrmUt();
	$lang->set('label', 'English');
	$lang->set('country', $china);
	$lang = $lang->save();

	UtilsTest::assertIsEquals($lang->get('lang_id'),2);
	UtilsTest::assertIsEquals($lang->get('country')->get('country_id'),1);

	//Test bug 86 with update
	$lang = $lang->save();
	
	UtilsTest::assertIsEquals($lang->get('lang_id'),2);
	UtilsTest::assertIsEquals($lang->get('country')->get('country_id'),1);*/

?>