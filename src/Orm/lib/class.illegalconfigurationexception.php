<?php
 /**
 * Contains the class IllegalArgumentException
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/
 

/**
 * Classe extends Exception, used when the initial configuration of Entities isn't correct
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
*/
class IllegalConfigurationException extends Exception  {
    
    public function __construct($msg=NULL, $code=0)
    {parent::__construct($msg, $code);}
}

?>