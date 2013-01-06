<?php
 /**
 * Contains the class IllegalArgumentException
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 

/**
 * Classe extends Exception, used when the parameters is not the one expected
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
*/
class IllegalArgumentException extends Exception {
    
    public function __construct($msg=NULL, $code=0)
    {parent::__construct($msg, $code);}
}


?>