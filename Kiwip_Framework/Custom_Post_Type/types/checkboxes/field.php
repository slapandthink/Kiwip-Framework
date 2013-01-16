<?php

class kiwip_fieldtype_checkboxes {

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
		foreach($field['options'] as $slug => $name){
			echo '<input type="checkbox" name="kiwip['.$id_name.'][]" id="'.$id_name.'_'.$Helper->kiwip_make_slugable($slug).'" value="'.$value.'" '.(in_array($Helper->kiwip_make_slugable($slug), maybe_unserialize($value)) ? 'checked="checked"' : '').' /><label for="'.$field_id_name.'_'.$Helper->kiwip_make_slugable($slug).'">'.$Helper->kiwip_make_readable($name).'</label>';
		}
	}
}