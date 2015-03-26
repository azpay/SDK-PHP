<?php
/**
 * Utils Class
 *
 * Class with utility methods
 *
 * @author Gabriel Guerreiro <gabrielguerreiro.com>
 **/

class Utils {


	/**
	 * Return a number formated
	 * @param  [type] $number [number]
	 * @return [type]        [description]
	 */
	public static function formatNumber($number) {

		$int = filter_var($number, FILTER_SANITIZE_NUMBER_INT);

		return str_replace(array('-','+'), '', $int);
	}



	/**
	 * Return a Slug formated
	 * @param  [type] $slug [slug]
	 * @return [type]        [description]
	 */
	public static function formatSlug($slug) {

		return trim(strtolower($slug));
	}

}

?>