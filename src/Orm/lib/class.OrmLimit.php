<?php
/**
 * Contains the class which will manage the Sql LIMIT
 *
 * @since 0.2.0
 * @author Heriquet
 **/


/**
 * Contains the class which will manage the Sql LIMIT
 * 
 * @since 0.2.0
 * @author Heriquet
 * @package Orm
*/
class OrmLimit {	

	/**
	* internal offset
	**/
	private $offset = 0;

	/**
	* internal counter for row
	**/
	private $row_count = 0;

	/**
	* public construct
	*
	* @param $offset [optional] the offset. Default = 0
	* @param $row_count [optional] the counter of row. Default = 0
	**/
	function __construct($offset=0, $row_count=0) {
		if($offset < 0) {
			throw new OrmIllegalArgumentException("Invalid offset value: {$offset}. Must be >= 0");
		}
		
		if($row_count <= 0) {
			throw new OrmIllegalArgumentException("Invalid row_count value: {$row_count}. Must be > 0");
		}

		$this->offset = $offset;
		$this->row_count = $row_count;
	}
	
   /**
    * return the LIMIT clause
    * 
    * @return string the LIMIT clause under sql language
    */      
	function getLimit()	{
		if($this->offset == 0 && $this->row_count == 0) { // no limit set
			return '';
		}
		
		$limit = ' LIMIT '.$this->offset.' , '.$this->row_count;
		
		return $limit;
	}
	
   /**
    * Set offset
    * 
    * @param $offset the offset of row to set
    */
	function setOffset($offset) {
		if($offset < 0) {
			throw new OrmIllegalArgumentException("Invalid offset value: {$offset}. Must be >= 0");
		}

		$this->offset = $offset;
	}
	
   /**
    * Set row_count
    * 
    * @param $row_count the counter of row to set
    */
	function setRowCount($row_count) {
		if($row_count <= 0) {
			throw new OrmIllegalArgumentException("Invalid row_count value: {$row_count}. Must be > 0");
		}

		$this->row_count = $row_count;
	}
}

?>
