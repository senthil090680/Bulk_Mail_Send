<?php
class common {
	public static function prestyle($preval) {
		echo "<pre>";
		print_r($preval);
		echo "</pre>";		
	}
	public static function checkstring($string,$arrayVal) {
		$arrayValAdded	=	"--".$arrayVal."--";
		$stringCheck = strpos($string, $arrayValAdded);
		if (!empty($stringCheck)) {
			return $arrayValAdded;
		} else {
			return false;
		}
	}
}
?>