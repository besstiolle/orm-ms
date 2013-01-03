<?php
 /**
 * Contient les différentes classes d'exception
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
 

/**
* Classe utilisée dans le cas ou l'argument passé n'est pas celui attendu
 * @since 1.0
 * @author Bess
 * @package mmmfs
*/
class IllegalArgumentException extends Exception {
    
    public function __construct($msg=NULL, $code=0)
    {parent::__construct($msg, $code);}
}


?>