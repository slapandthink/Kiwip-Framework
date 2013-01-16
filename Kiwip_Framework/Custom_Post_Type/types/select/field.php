<?php

class kiwip_fieldtype_select {

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
		echo '<select name="kiwip['.$id_name.']" id="'.$id_name.'">';
		
		foreach($field['options'] as $slug => $name){
			echo '<option value="'.$value.'" '.selected($Helper->kiwip_make_slugable($slug), $value, false).'>'.$Helper->kiwip_make_readable($name).'</option>';
		}
		
		echo '</select>';
	}

}