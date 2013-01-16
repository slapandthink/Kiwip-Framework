<?php

class kiwip_fieldtype_textarea {

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
		echo '<textarea name="kiwip['.$id_name.']" id="'.$id_name.'">'.$value.'</textarea>';
	}
}