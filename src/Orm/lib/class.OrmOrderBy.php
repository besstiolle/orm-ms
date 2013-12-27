<?php
/**
 * Contains the class which will manage the Sql sorting of the Entity
 *
 * @since 0.2.0
 * @author Heriquet
 * @package Orm
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
class OrmOrderBy
{
	private $orders = array();

	public static $ASC = 'ASC';
	public static $DESC = 'DESC';
	
	/**
    * public constructor
    * 	
    * @param array $orders an hash with order by definitions
	*/
	function __construct($orders=array()) {
		foreach($orders as $field => $order) {
			if($order == OrmOrderBy::$ASC) {
				$this->addAsc($field);
			}
			else if($order == OrmOrderBy::$DESC) {
				$this->addDesc($field);			
			}
			else {
				throw new OrmIllegalArgumentException("Invalid order for ORDER BY: {$order}.");
			}
		}
	}
	
   /**
    * return ORDER BY clause
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
    * Add a new field in order by: DESC
    * 
    */
	function addAsc($field) {
		$this->orders = array_merge($this->orders, array($field => '')); // ASC not mandatory
	}
	
   /**
    * Add a new field in order by: DESC
    * 
    */
	function addDesc($field) {
		$this->orders = array_merge($this->orders, array($field => OrmOrderBy::$DESC));
	}
}

?>
