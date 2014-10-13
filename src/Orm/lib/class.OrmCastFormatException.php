<?php
 /**
 * Contains the class OrmCastFormatException
 *
 * @since 0.0.1
 * @author Bess
 **/
 

/**
 * Class extends Exception, used when format of Field is not valid
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
*/
class OrmCastFormatException extends Exception {
 
    /**
    * Public constructor
    *
    * @param string $msg [optional] the error message 
    * @param int $code [optional] the error code
    */
    public function __construct($msg=NULL, $code=0) {
    	parent::__construct($msg, $code);
    }
}


?>
