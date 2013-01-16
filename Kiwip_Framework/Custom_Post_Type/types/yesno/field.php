<?php

class kiwip_fieldtype_yesno {

	public function __construct(){
		/**
		 * Do some things or include some files first
		 */

	}

	/**
	 * Render
	 * @return output HTML
	 */
	public function render($id_name, $value, $field=null){
		echo '<input type="radio" name="kiwip['.$id_name.']" id="'.$id_name.'_yes" value="yes" '.checked($value, 'yes', false).' >';
		echo '<label for="'.$id_name.'_yes">'.__('Yes').'</label>';
		
		echo ' <input type="radio" name="kiwip['.$id_name.']" id="'.$id_name.'_no" value="no" '.checked($value, 'no', false).' >';
		echo '<label for="'.$id_name.'_no">'.__('No').'</label>';
	}
}
