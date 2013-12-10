<?php
/**
 * An example of how you can link 2 Object with a single relation : 
 *   + One country can have zero or many cities.
 *   + One city can only have 1 country.
 *
 *  We speak here of Relation One-to-Many and Many-to-One
 *
 * You should also take a look inside the class class.entity.countrySkeleton.php
 */ 
class CitySkeleton extends OrmEntity
{
	public function __construct()
	{
		parent::__construct('ormskeleton','CitySkeleton');
		
		// A primary key, very useful for most of your definitions
		$this->add(new OrmField('city_id'	
			// This parameter is simply a integer. You can choose between a lot of possibility. 
			//  Take a look inside the class CAST for all the possibilities.
			, OrmCAST::$INTEGER 
			, null	
			, null 		// is required ! 
			, OrmKEY::$PK	// is a primary key (auto-incremented)
		));
		
		$this->add(new OrmField('labelCity'		
			// Will be transformed into a varchar(50) no-nullable into your database.
			, OrmCAST::$STRING
			, 50	
		));
		
		// A foreign key, will make the link to the country of this city
		$this->add(new OrmField('country'	
			// We'll storage the id of the country so you will be able to
			//  get the country of this city like this : 
			//  # $countryId = $myCity->get('country');
			//  # $country = CORE::findById(new Country(), $countryId);
			//  And play with it.
			//  # echo $country->get('labelCountry');
			, OrmCAST::$INTEGER 
			, null	
			, null 		// is required ! 
			, OrmKEY::$FK	// is a foreign key
			// You could see this code like "a path to go to the informations in the other Entity
			// It's simply "nameOfTheOtherEntity.nameOfTheField 
			, "countrySkeleton.country_id" 
		));
	}	
}
?>