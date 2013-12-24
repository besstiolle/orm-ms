<?php

class UtilsTest{

	
	public static function assertIsEquals($result,$expected){
		if($expected == $result){
			$class = 'success';
		} else {
			$class = 'error';
		}
		echo "<p class='$class'>we expected value be equals to [{$expected}], we have got [{$result}]</p>";
	}
	
	public static function assertIsNull($result){
		if(null == $result){
			$class = 'success';
		} else {
			$class = 'error';
		}
		echo "<p class='$class'>we expected value being null</p>";
	}
	

}

?>