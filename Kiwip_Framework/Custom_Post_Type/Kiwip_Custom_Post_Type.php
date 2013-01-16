<?php 

/******
*******

Some things to add:

Change The Enter Title Text For Custom Post Types: 
http://www.paulund.co.uk/change-the-enter-title-text-for-custom-post-types

Include All Custom Post Types In WordPress Search: 
http://www.paulund.co.uk/include-all-custom-post-types-in-wordpress-search


*******
******/



/**
 * Kiwip Custom Post Type Class
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Kiwip_Custom_Post_Type{

	public $dir    = KIWIP_PATH;
	public $url    = KIWIP_URL;
	
	public $name   = string;
	public $slug   = string;
	public $plural = string;
	public $args   = array();
	public $labels = array();
	public $icons = array();

	/**
	 * Construct
	 * @param string $name
	 * @param array $args
	 * @param array $labels
	 * @since Kiwip Framework 0.1
	 */
	public function __construct($name, $config=array(), $args=array(), $labels=array()){

		// fix path
		$this->dir = $this->dir.'/Custom_Post_Type';
		$this->url = $this->url.'Custom_Post_Type';

		// Load Helper class
		$Helper = new Kiwip_Helper;

		/* Loads scripts and styles */
		add_action('admin_init', array(&$this, 'kiwip_register_and_enqueue'));

		/* Setters */
		if(isset($name) AND !empty($name)){
			$this->name   = $Helper->kiwip_make_readable($name);
			$this->slug   = $Helper->kiwip_make_slugable($name);
			$this->plural = $Helper->kiwip_make_plural($name);
			$this->args   = $args;
			$this->labels = $labels;

			/* If the post type doesn't already exist, we add it */
			if(!post_type_exists($this->slug)){
				add_action('init', array(&$this, 'kiwip_register_post_type'));
			}

			/* Display the custom icon if given */
			if(!empty($config['icons']) && is_array($config['icons'])){
				$this->icons = $config['icons'];
			}else{ // add a generic icon if not
				$this->icons = array(
					'16px' => KIWIP_URL.'img/icons/bookmark.png',
					'32px' => KIWIP_URL.'img/icons/bookmark.png'
				);
			}

			/* Add the icon for post type */
			add_action('admin_head', array(&$this, 'kiwip_change_cpt_icon'));

			/* Include the custom post type in Wordpress Search if wanted */
			if($config['include_in_search'] === true){
				add_action('pre_get_posts', array(&$this, 'kiwip_include_cpt_in_search'));
			}


		}else{
			wp_die("The Name is empty, you have to give a name to the custom post type.");
		}

	}

	/**
	 * Kiwip Register Post Type
	 * Register a new Custom Post Type to the WordPress Theme
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_register_post_type(){
		
		/* Set labels */
		$this->labels = $this->kiwip_labels_merge();
		/* Set args */
		$args = $this->kiwip_args_merge();
		/* Register the post type */
		/**
		 * http://codex.wordpress.org/Function_Reference/register_post_type
		 * $post_type
   		 * (string) (required) Post type. (max. 20 characters, can not contain capital letters or spaces) 
   		 * We use the slug for the name of the custom post type
		 */
		register_post_type($this->slug, $args);

	}

	/**
	 * Kiwip Merge Labels Vars
	 * Merge labels vars with vars of the object
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_labels_merge(){
		$defaults_labels                       = array();
		$defaults_labels['name']               = __($this->plural, KIWIP_TEXTDOMAIN);
		$defaults_labels['singular_name']      = __($name, KIWIP_TEXTDOMAIN);
		$defaults_labels['add_new']            = _x('Add New', $this->slug, KIWIP_TEXTDOMAIN);
		$defaults_labels['add_new_item']       = _x('Add New', $this->slug, KIWIP_TEXTDOMAIN);
		$defaults_labels['edit_item']          = __('Edit '.$this->name, KIWIP_TEXTDOMAIN);
		$defaults_labels['new_item']           = __('New '.$this->name, KIWIP_TEXTDOMAIN);
		$defaults_labels['all_items']          = __('All '.$this->plural, KIWIP_TEXTDOMAIN);
		$defaults_labels['view_item']          = __('View '.$this->name, KIWIP_TEXTDOMAIN);
		$defaults_labels['search_items']       = __('Search '.$this->plural, KIWIP_TEXTDOMAIN);
		$defaults_labels['not_found']          = __('No '.strtolower($this->plural).' found', KIWIP_TEXTDOMAIN);
		$defaults_labels['not_found_in_trash'] = __('No '.strtolower($this->plural).' found in Trash', KIWIP_TEXTDOMAIN);
		$defaults_labels['parent_item_colon']  = '';
		$defaults_labels['menu_name']          = __($this->plural, KIWIP_TEXTDOMAIN);

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
		$defaults_args['public']            = true;
		$defaults_args['show_ui']           = true;
		$defaults_args['supports']          = array('title', 'editor');
		$defaults_args['show_in_nav_menus'] = true;
		$defaults_args['_builtin']          = false;

		/* Mergeing */
		return wp_parse_args($this->args, $defaults_args);
	}

	/**
	 * Kiwip Register and Enqueues
	 * Enqueue some scripts and styles
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_register_and_enqueue(){
		/* Register */
		wp_register_style('kiwip-custom-post-type-css', $this->url.'/css/kiwip-custom-post-type.css', 'false', time(), 'screen');
		wp_register_style('kiwip-custom-post-type-css-jquery-ui', $this->url.'/css/smoothness/jquery-ui-1.8.20.custom.css', false, time(), 'screen'); // ??
		wp_register_script('kiwip-custom-post-type-js', $this->url.'/js/kiwip-custom-post-type.js', array('jquery', 'jquery-ui-datepicker'), time(), true);
		wp_register_script('kiwip-medialibrary-uploader-js', $this->url.'/js/medialibrary-uploader.js', false, time(), true);

		/* Enqueue */
		wp_enqueue_style('kiwip-custom-post-type-css');
		wp_enqueue_style('kiwip-custom-post-type-css-jquery-ui');
		wp_enqueue_script('kiwip-custom-post-type-js');
		//wp_enqueue_script('kiwip-medialibrary-uploader-js');
	}

	/**
	 * Kiwip Include Cpt In Search
	 * Include post type in wordpress search
	 * @return object
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_include_cpt_in_search($query){
		if(is_search()) {
			$query->set('post_type', $this->slug);
		}
		return $query;
	}

	/**
	 * Kiwip Change CPT Icon
	 * Change the icons of custom post type
	 * @return mixed
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_change_cpt_icon(){
		echo '<style type="text/css" media="screen">';
		echo '#menu-posts-'.$this->slug.' .wp-menu-image {';
        echo 'background: url('.$this->icons['16px'].') center center no-repeat !important; } ';
        echo '.icon32-posts-'.$this->slug.' {';
        echo 'background: url('.$this->icons['32px'].') center center no-repeat !important; } ';
        echo '</style>';
	}

	/**
	 * Kiwip Add Taxonomy
	 * @return object
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_add_taxonomy($name, $args=array(), $labels=array()){
		// Call Cuztom_Taxonomy with this post type name as second parameter
		$taxonomy = new Kiwip_Taxonomy($name, $this->slug, $args, $labels);
		
		// For method chaining
		return $this;
	}

	/**
	 * Kiwip Add Meta Box
	 * Add post meta box to the Post Type
	 * @param string $title
	 * @param array $fields
	 * @param string $context
	 * @param string $priority
	 * @return object
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_add_meta_box($title, $fields=array(), $context='normal', $priority='default'){
		$meta_box = new Kiwip_Meta_Box($title, $fields, $this->name, $context, $priority );
		
		// For method chaining
		return $this;
	}

}