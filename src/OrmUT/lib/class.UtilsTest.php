<?php

class UtilsTest{

	private static $cssError = 'nok';
	private static $cssSuccess = 'ok';
	
	public static function assertIsEquals($result,$expected){
		if($expected == $result){
			$class = UtilsTest::$cssSuccess;
		} else {
			$class = UtilsTest::$cssError;
		}
		echo "<p class='$class'>we expected value be equals to [{$expected}], we have got [{$result}]</p>";
	}
	
	public static function assertIsNull($result){
		if(null == $result){
			$class = UtilsTest::$cssSuccess;
		} else {
			$class = UtilsTest::$cssError;
		}
		echo "<p class='$class'>we expected value being null</p>";
	}
	
	public static function assertIsNotNull($result){
		if(null != $result){
			$class = UtilsTest::$cssSuccess;
		} else {
			$class = UtilsTest::$cssError;
		}
		echo "<p class='$class'>we expected value being not null</p>";
	}
	

}

?>
