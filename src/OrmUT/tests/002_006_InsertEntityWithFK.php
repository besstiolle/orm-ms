
<p class='title'>002/006 : can I create an Entity with OrmEntity->save() with FK?</p>
<?php
	
	//First, drop the table, recreate it
	reinitAllTables($mod);
	
	$china = new CountryOrmUT();
	$china->set('label', 'China');
	$china->set('iso_code', 'cn');
	$china = $china->save();

	UtilsTest::assertIsEquals($china->get('country_id'),1);

	$lang = new CountryLangOrmUt();
	$lang->set('label', 'Mandarin');
	$lang->set('country', 1);
	$lang = $lang->save();


	UtilsTest::assertIsEquals($lang->get('lang_id'),1);
	UtilsTest::assertIsEquals($lang->get('country'),1);

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
	UtilsTest::assertIsEquals($lang->get('country')->get('country_id'),1);

	

?>