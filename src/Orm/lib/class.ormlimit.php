<?php
/**
 * Contains the class which will manage the Sql LIMIT
 *
 * @since 0.2.0
 * @author Heriquet
 * @package Orm
 **/


/**
 * Contains the class which will manage the Sql LIMIT
 * 
 * @since 0.2.0
 * @author Heriquet
 * @package Orm
*/
class OrmLimit
{	
	private $offset = 0;
	private $row_count = 0;

	function __construct($offset=0, $row_count=0) {
		$this->offset = $offset;
		$this->row_count = $row_count;
	}
	
   /**
    * return the LIMIT clause
    * 
    * @return the LIMIT clause
    */      
	function getLimit()	{
		if($this->offset == 0 && $this->row_count == 0) { // no limit set
			return '';
		}
		
		if($this->offset < 0) {
			throw new OrmIllegalArgumentException("Invalid offset value: {$this->offset}. Must be >= 0");
		}
		
		if($this->row_count <= 0) {
			throw new OrmIllegalArgumentException("Invalid row_count value: {$this->row_count}. Must be > 0");
		}
		
		$limit = ' LIMIT ';
		
		if($this->offset = 0) {
			$limit.= $this->offset;
		}
		
		$limit.= ' '.$this->row_count;
		
		return $limit;
	}
	
   /**
    * Set offset
    * 
    */
	function setOffset($offset) {
		$this->offset = $offset;
	}
	
   /**
    * Set row_count
    * 
    */
	function setRowCount($row_count) {
		$this->row_count = $row_count;
	}
}

?>