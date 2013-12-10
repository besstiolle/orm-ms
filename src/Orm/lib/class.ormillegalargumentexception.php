<?php
 /**
 * Contains the class OrmIllegalArgumentException
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
class OrmIllegalArgumentException extends Exception {
    
    public function __construct($msg=NULL, $code=0)
    {parent::__construct($msg, $code);}
}


?>