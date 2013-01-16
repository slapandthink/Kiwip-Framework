<?php

/**
 * Kiwip Custom Post Type Helper class
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Kiwip_Helper{

	/**
	 * Kiwip Make Readable
	 * Transform the string given by a beautiful and human comprehensive string
	 * @param string $string (ugly)
	 * @return string (beautiful)
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_readable($string){
		return ucwords(str_replace('_', ' ', $string));
	}

	/**
	 * Kiwip Make Slugable
	 * Transform the string given by string slug like
	 * @param string $string (ugly)
	 * @return string (beautiful)
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_slugable($string){
		return strtolower(preg_replace('/[^A-z0-9]/', '_', $string));
	}

	/**
	 * Kiwip Make plural
	 * Make the plural of the string given
	 * @param string $string (single)
	 * @return string (plural)
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_plural($string){
		$lastchar = $string[strlen($string) - 1];
		
		if($lastchar == 'y'){
			$cut = substr($string, 0, -1);
			$plural = $cut . 'ies';
		}else{
			$plural = $string . 's';
		}
		
		return $plural;
	}
}


