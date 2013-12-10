<?php
/**
 * An example of how you can link 2 Object with a single relation : 
 *   + One country can have zero or many cities.
 *   + One city can only have 1 country.
 *
 *  We speak here of Relation One-to-Many and Many-to-One
 *
 * You should also take a look inside the class class.entity.citySkeleton.php
 */ 
class CountrySkeleton extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormskeleton','countrySkeleton');
		
		// A primary key, very useful for most of your definitions
		$this->add(new OrmField('country_id'	
			// This parameter is simply a integer. You can choose between a lot of possibility. 
			//  Take a look inside the class OrmCAST for all the possibilities.
			, OrmCAST::$INTEGER 
			, null	
			, null 		// is required ! 
			, OrmKEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new OrmField('labelCountry'		
			, OrmCAST::$STRING
			, 50	
		));
		
		// A foreign key, will make the link to the cities of this country
		$this->add(new OrmField('cities'	
			// it won't be  a real field into your database, it's a virtual fields that will allow you to
			//  get all the cities of this country like this : 
			//  array $cities = $myCountry->get('cities');
			, OrmCAST::$NONE 
			, null	
			, TRUE 		// it's not required. A country can have zero city.
			, OrmKEY::$AK	// it's a associate key as soon it's a virtual link to a bunch of Cities
			// You could see this code like "a path to go to the informations in the other Entity
			// It's simply "nameOfTheOtherEntity.nameOfMyFKCorrespondence
			, "citySkeleton.country" 
		));
	}	
	
	/**
	 * When you declare this function, the framework will try to execute this function as soon as the table is created.
	 * So it's the best place to initiate your tables with some data !
	 */
	public function initTable(){
		$country1 = new CountrySkeleton();
		$country1->set('labelCountry', 'France');
		$country1->save();
		
		$country2 = new CountrySkeleton();
		$country2->set('labelCountry', 'Belgium');
		$country2->save();
		
		$country3 = new CountrySkeleton();
		$country3->set('labelCountry', 'Spain');
		$country3->save();
		
		$country4 = new CountrySkeleton();
		$country4->set('labelCountry', 'Chile');
		$country4->save();
	}
}
?>