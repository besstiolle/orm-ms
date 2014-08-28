<?php
 /**
 * Contains the class OrmIllegalArgumentException
 *
 * @since 0.3.0
 * @author Bess
 **/
 

/**
 * Class extends Exception, used when the parameters is not the one expected
 *
 * @since 0.3.0
 * @author Bess
 * @package Orm
*/
class OrmSqlException extends Exception {
    
 
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
