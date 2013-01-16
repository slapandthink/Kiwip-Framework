<?php


/** 
 * Kiwip Meta Box
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Kiwip_Meta_Box{

	/* General options */
	public $dir     = KIWIP_PATH;
	public $url     = KIWIP_URL;

	/* Fields */
	public $scriptsUrl = string;
	public $typesDir   = string;
	public $fieldtypes = array();
	public $classes    = array();


	public $id          = string;
	public $title       = string;
	public $slug        = string;
	public $context     = string;
	public $priority    = string;
	public $cpt_name    = string;
	public $meta_fields = string;
	
	
	/**
	 * Construct
	 * @param string $name
	 * @param array $fields
	 * @param string $post_type_name
	 * @param string $context
	 * @param string $priorit
	 * @since Kiwip Framework 0.1
	 */
	public function __construct($name, $fields=array(), $cpt_name=null, $context='normal', $priority='default'){

		// Load Helper class
		$Helper = new Kiwip_Helper;

		if(isset($name) AND !empty($name)){
			$this->cpt_name    = $cpt_name;
			$this->id          = $Helper->kiwip_make_slugable($name);
			$this->slug        = $Helper->kiwip_make_slugable($name);
			$this->title       = $Helper->kiwip_make_readable($name);
			$this->context     = $context;
			$this->priority    = $priority;
			$this->meta_fields = $fields;

			add_action('admin_init', array(&$this, 'kiwip_add_meta_box'));
		}
		
		// fix path
		$this->dir = $this->dir.'Custom_Post_Type';
		$this->url = $this->url.'Custom_Post_Type';

		// create the path for types available
		$this->typesDir = $this->dir.'/types';
		$this->scriptsUrl = $this->url.'/types';

		/* Start by making the list of fields available */
		$this->kiwip_make_list();


		// Add multipart for files
		add_action('post_edit_form_tag', array(&$this, 'kiwip_post_edit_form_tag'));
		
		// Listen for the save post hook
		add_action('save_post', array(&$this, 'kiwip_save_post'));		
	}

	/**
	 * Kiwip Add Meta Box
	 * Method that calls the add_meta_box function
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_add_meta_box(){			
		add_meta_box(
			$this->id,
			$this->title,
			array(&$this, 'kiwip_metabox_callback'),
			$this->cpt_name,
			$this->context,
			$this->priority
		);
	}

	/**
	 * Kiwip Metabox Callback
	 * Adding all meta fields of metabox
	 * @param object $post
	 * @param object $data
	 * @return mixed
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_metabox_callback($post, $data){
		// Load Helper class
		$Helper = new Kiwip_Helper;
		// Nonce field for validation
		wp_nonce_field(plugin_basename(__FILE__), 'kiwip_nonce'); // http://codex.wordpress.org/Function_Reference/wp_nonce_field

		// Get all inputs from $data
		$meta_fields = $this->meta_fields;
		
		// Check the array and loop through it
		if(!empty($meta_fields)){
			echo '<div class="kiwip_helper">';
				echo '<table border="0" cellading="0" cellspacing="0" class="kiwip_table kiwip_helper_table">';
						
					/* Loop through $meta_fields */
					foreach($meta_fields as $field){
						$field_id_name = '_'.$this->slug."_".$Helper->kiwip_make_slugable($field['name']);
						$meta          = get_post_meta($post->ID, $field_id_name);

						echo '<tr>';
							echo '<th class="kiwip_th th">';
								echo '<label for="'.$field_id_name.'" class="kiwip_label">'.$field['label'].'</label>';
								echo '<div class="kiwip_description description">'.$field['description'].'</div>';
							echo '</th>';
							echo '<td class="kiwip_td td">';
						
								$this->kiwip_make_field($field_id_name, $field, $meta);
							
							echo '</td>';
						echo '</tr>';
					}
				
				echo '</table>';
			echo '</div>';
		}
	}

	/**
	 * Kiwip Make List
	 * List all field types in the types folder et make an array with there files: field.php and validation.php
	 * This array is like: 
	 *  [1][type_name][field]
	 *  [1][type_name][validation]
	 *
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_list(){
		
		/**
		 * - list all directory in types folder
		 * - list all files in the current folder
		 * - create the attributes of the field type
		 * - increment the array for listing
		 */
		if(is_dir($this->typesDir)){
			if($folder = opendir($this->typesDir)){ //open the directory

				while(($file = readdir($folder)) !== false){ //grab subfolder
					if($file !== '.' AND $file !== '..'){

						/* Constrcut the tree structure in the array to store informations */
						$this->fieldtypes[$file]; //increment the general array of fieldtypes

						if($subfiles = scandir($this->typesDir.'/'.$file)){
							
							foreach($subfiles as $filename){ //loop on each files inside the folder

								if($filename == 'field.php'){
									$field = $file.'/field.php';
									$this->fieldtypes[$file]['field'] = $field; //add information in the array
								}
								if($filename == 'validation.php'){
									$validation = $file.'/validation.php';
									$this->fieldtypes[$file]['validation'] = $validation; //add information in the array
								}

							}//end foreach
						}//end if
					}//end if
				}//end while
			}//end if
		}//end if

		// $this->debug($this->typesDir); $this->debug($this->fieldtypes); die();
	}

	/**
	 * Kiwip Make Field
	 * Load class and return fields
	 * @param string $field_id_name
	 * @param array $type
	 * @param array $meta
	 * @return mixed
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_field($field_id_name, $field, $meta){
		/**
		 * For each of theme:
		 * - include the fieldtype file
		 * - instanciate the class
		 */
		$field_class = 'kiwip_fieldtype_'.$field['type'];

		if(!class_exists($field_class)){	//if this fieldtype is not already loaded, do it
			require_once($this->typesDir.'/'.$this->fieldtypes[$field['type']]['field']);
			$this->classes[$field['type']] = new $field_class;
		}//if
		$this->classes[$field['type']]->render($field_id_name, $meta[0], $field); //call the render method

		// $this->debug($this->fieldtypes); die();
	}

	/**
	 * Kiwip Save Post
	 * Hooks into the save hook for the newly registered Post Type
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_save_post(){
		// Load Helper class
		$Helper = new Kiwip_Helper;
		// Deny the wordpress autosave function
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if($_POST && !wp_verify_nonce($_POST['kiwip_nonce'], plugin_basename(__FILE__))) return;
		if(!isset($_POST)) return;
		
		global $post;
		if(!isset($post->ID) && get_post_type($post->ID) !== $this->post_type_name) return;
		
		// Loop through each meta box
		if(!empty($this->meta_fields)){
			foreach($this->meta_fields as $field){
				$field_id_name = '_'.$this->slug."_".$Helper->kiwip_make_slugable($field['name']);

				// validation rules here 

				update_post_meta($post->ID, $field_id_name, $_POST['kiwip'][$field_id_name]);
				
			}			
		}		
	}
	
	
	/**
	 * Kiwip Post Edit Form Tag
	 * Adds multipart support to the post form
	 * @since Kiwip Framework 0.1
	 */
	function kiwip_post_edit_form_tag(){
		echo ' enctype="multipart/form-data"';
	}

	

	/**
	 * Debug
	 * Debug function
	 * @param anything
	 * @since Kiwip Framework 0.1
	 */
	public function debug($value){
		echo "<pre>";
		print_r($value);
		echo "</pre>";
	}

}






