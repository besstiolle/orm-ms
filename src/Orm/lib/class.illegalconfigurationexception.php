<?php
 /**
 * Contient les différentes classes d'exception
 * @since 1.0
 * @author Bess
 * @package mmmfs
 **/
 
/**
* Classe utilisée dans le cas ou la configuration des entités &co n'est pas correcte
 * @since 1.0
 * @author Bess
 * @package mmmfs
*/
class IllegalConfigurationException extends Exception  {
    
    public function __construct($msg=NULL, $code=0)
    {parent::__construct($msg, $code);}
}

?>