<?php


/**
 * Kiwip Taxonomy
 * Register the taxonomy
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Kiwip_Taxonomy{
	
	public $cpt_name = string;
	public $name     = string;
	public $slug     = string;
	public $plural   = string;
	public $args     = array();
	public $labels   = array();


	/**
	 * Construct
	 * @param string $name
	 * @param array $args
	 * @param array $labels
	 * @since Kiwip Framework 0.1
	 */
	public function __construct($name, $cpt_name=NULL, $args=array(), $labels=array()){

		// Load Helper class
		$Helper = new Kiwip_Helper;

		/* Setters */
		if(isset($name) AND !empty($name)){
			$this->cpt_name = $cpt_name;
			$this->name     = $Helper->kiwip_make_readable($name);
			$this->slug     = $Helper->kiwip_make_slugable($name);
			$this->plural   = $Helper->kiwip_make_plural($name);
			$this->args     = $args;
			$this->labels   = $labels;

			/* If the post type doesn't already exist, we add it */
			if(!taxonomy_exists($this->slug)){
				add_action('init', array(&$this, 'kiwip_register_taxonomy'), 0);
			}else{
				add_action('init', array(&$this, 'kiwip_register_taxonomy_for_object_type'), 0);
			}
		}else{
			wp_die("The Name is empty, you have to give a name to the taxonomy.");
		}

	}

	/**
	 * Kiwip Register Taxonomy
	 * Register a new Taxonomy to the WordPress Theme
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_register_taxonomy(){
		
		/* Set labels */
		$this->labels = $this->kiwip_labels_merge();
		/* Set args */
		$args = $this->kiwip_args_merge();
		/* Register the post type */
		register_taxonomy(strtolower($this->name), $this->cpt_name, $args); //transform string name to lower string name
	}

	/**
	 * Kiwip Merge Labels Vars
	 * Merge labels vars with object's vars
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_labels_merge(){
		$defaults_labels                      = array();
		$defaults_labels['name']              = _x($this->plural, 'taxonomy general name', KIWIP_TEXTDOMAIN);
		$defaults_labels['singular_name']     = _x($this->name, 'taxonomy singular name', KIWIP_TEXTDOMAIN);
		$defaults_labels['search_items']      = __('Search '.$this->plural);
		$defaults_labels['all_items']         = __('All '.$this->plural);
		$defaults_labels['parent_item']       = __('Parent '.$this->name);
		$defaults_labels['parent_item_colon'] = __('Parent '.$this->name.':');
		$defaults_labels['edit_item']         = __('Edit '.$this->name);
		$defaults_labels['update_item']       = __('Update '.$this->name);
		$defaults_labels['add_new_item']      = __('Add New '.$this->name);
		$defaults_labels['new_item_name']     = __('New '.$this->name.' Name');
		$defaults_labels['menu_name']         = __($this->name);

		/* Mergeing */
		return wp_parse_args($this->labels, $defaults_labels);
	}

	/**
	 * Kiwip Merge Args Vars
	 * Merge args vars with vars of the object
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_args_merge(){
		$defaults_args                      = array();
		$defaults_args['label']             = $this->plural;
		$defaults_args['labels']            = $this->labels;
		$defaults_args['hierarchical']      = true;
		$defaults_args['public']            = true;
		$defaults_args['show_ui']           = true;
		$defaults_args['show_in_nav_menus'] = true;
		$defaults_args['_builtin']          = false;

		/* Mergeing */
		return wp_parse_args($this->args, $defaults_args);
	}

	/**
	 * Kiwip Register Taxonomy For Object Type
	 * Used to attach the existing taxonomy to the post type
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_register_taxonomy_for_object_type(){
		register_taxonomy_for_object_type($this->taxonomy_name, $this->post_type_name); // register_taxonomy_for_object_type : codex.wordpress.org/Function_Reference/register_taxonomy_for_object_type
	}

}



