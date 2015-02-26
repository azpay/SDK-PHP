<?php 

class Utils {

	public static function formatNumber($value) {
		
		$int = filter_var($value, FILTER_SANITIZE_NUMBER_INT);

		return str_replace(array('-','+'), '', $int);
	}

}

?>