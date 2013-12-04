orm-ms
======

*A delightful and powerful ORM system to improve the API of CmsMadeSimple.*

Why should i use an ORM framework into my own module
---------------------------------------------------

Less code (up to -75%) equals less bug and obviously it's a easier way to manage your modules. this framework provide a impressive list of basics [CRUD's functionnalities](http://en.wikipedia.org/wiki/CRUD) but also advanced functionnalities : 

*  findAll, findById, findByIds, countAll, delete, save
*  findByExample, deleteByExample
*  A **caching system** for the best performances
*  Primary Keys, Foreign Keys, Associate Keys (many-to-one, one-to-many, many-to-many)

And we're already thinking about the future

*  the **"lazymode = false"** functionnality to allow you autoloading the clusters of objects
*  the **IUUD**' implementation  
*  the **"unique key"** functionnality
*  the **"composite primary key"** functionnality
*  ...

And because an example is worth a thousand speeches, here an example with an install file in a random CmsMadeSimple Module

Before : 
```php
$db =& $this->GetDb();

$db_prefix = cms_db_prefix();
$dict = NewDataDictionary($db);
$flds= "
  	id I,
    textid C(32),
		description C(255)
	";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_quotegroups', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence( cms_db_prefix()."module_quotegroups_seq" );


$new_css_id = $db->GenID(cms_db_prefix() . "css_seq");
$css_name = "Module: Quotes Made Simple";
$css_text = file_get_contents('stylesheet.css');
$media_type = '';
$query = "INSERT INTO " . cms_db_prefix() . "css (css_id, css_name, css_text, media_type, create_date, modified_date) VALUES (?, ?, ?, ?, ?, ?)";
$result = $db->Execute($query, array($new_css_id, $css_name, $css_text, $media_type, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
```
After :

```php
//Instanciate a new Css entity
$css = new Css();

//Create its table
Core::createTable($css);

//Populate values
$css->set('css_name','Module: Quotes Made Simple');
$css->set('css_text',file_get_contents('stylesheet.css'));
$css->set('media_type','');
$css->set('create_date',date());
$css->set('modified_date',date());

//Save the entity
$css->save();
```

So have you other questions ? 


Where should i start ?
----------------------

*   You should start by taking a look on the [wiki of this project](https://github.com/besstiolle/orm-ms/wiki). 
*   After making your first step with the documentation you could try by yourself the [Orm-Skeleton module](http://dev.cmsmadesimple.org/project/files/1250#package-1235) to see how it's good to doing so much with so few code
*   If you need to start your project you can also read the **manual of the death** with our  [apidoc](http://orm.furie.be/apidoc/index.html) fully up-to-date !
*   Okay so you need more functionnalities ? why don't you [share your ideas](https://github.com/besstiolle/orm-ms/issues?state=open) with us ! i love the ideas <3
