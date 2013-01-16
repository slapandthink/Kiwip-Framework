<?php

class kiwip_fieldtype_text {

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
		echo '<input type="text" name="kiwip['.$id_name.']" id="'.$id_name.'" value="'.$value.'">';
	}

}