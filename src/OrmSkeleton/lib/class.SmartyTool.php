<?php

class SmartyTool{
	
	function securize($str){
		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}
}


?>