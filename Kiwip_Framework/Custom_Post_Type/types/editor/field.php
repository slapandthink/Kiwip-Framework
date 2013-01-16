<?php

class kiwip_fieldtype_editor {

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
		wp_editor($value, $id_name, array_merge(
			// Default
			array(
				'textarea_name' => 'kiwip['.$id_name.']',
				'media_buttons' => false
			),
			
			// Given
			isset($field['options']) ? $field['options'] : array()
		
		));
	}

}