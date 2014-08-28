<?php
/**
 * Contains the class which will manage the Sql sorting of the Entity
 *
 * @since 0.2.0
 * @author Heriquet
 **/


/**
 * Contains the class which will manage the Sql sorting of the Entity
 * 
 *  OrmCAST::$DESC 
 *
 * @since 0.2.0
 * @author Heriquet
 * @package Orm
*/
class OrmOrderBy {

	/**
	* the inner list of orders
	**/
	private $orders = array();

	/**
	* the inner SQL word for ASC sorting
	**/
	public static $ASC = 'ASC';

	/**
	* the inner SQL word for DESC sorting
	**/
	public static $DESC = 'DESC';
	
	/**
    * public constructor
    * 	
    * @param mixed[] $orders an hash with order by definitions
	*/
	function __construct($orders=array()) {
		foreach($orders as $fieldname => $order) {

			if($order == OrmOrderBy::$ASC) {
				$this->addAsc($fieldname);
			} else if($order == OrmOrderBy::$DESC) {
				$this->addDesc($fieldname);			
			} else {
				throw new OrmIllegalArgumentException("Invalid order for ORDER BY: {$order}.");
			}
		}
	}
	
   /**
    * return the SQL ORDER BY clause
    * 
    * @return ORDER BY clause
    */      
	function getOrderBy()	{
		$orderby = ' ORDER BY ';
		
		if(count($this->orders) == 0) {
			return '';
		}
		
		$i=1;
		foreach($this->orders as $key => $value) {
			
			$orderby .= ($i++>1?',':'').' '.$key.' '.$value;
		}
		
		return $orderby;
	}
	
   /**
    * Add a new fieldname in order by: DESC
    * 
    * @param string $fieldname the name of the field to adding
    */
	function addAsc($fieldname) {
		$this->orders = array_merge($this->orders, array($fieldname => '')); // ASC not mandatory
	}
	
   /**
    * Add a new fieldname in order by: DESC
    * 
    * @param string $fieldname the name of the field to adding
    */
	function addDesc($fieldname) {
		$this->orders = array_merge($this->orders, array($fieldname => OrmOrderBy::$DESC));
	}
}

?>
