<?php

/**
 * Kiwip Shortcodes
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Kiwip_Shortcodes{

	/* General options */
	public $dir     = KIWIP_PATH;
	public $url     = KIWIP_URL;

	/* Shortcodes */
	public $scriptsUrl;
	public $scriptsDir;
	public $shortcodes = array();
	public $tinymce    = array();
	public $stylesheet = array();

	/**
	 * Construct
	 */
	public function __construct(){

		// fix path
		$this->dir = $this->dir.'/Shortcodes';
		$this->url = $this->url.'Shortcodes';

		// create the path for scripts
		$this->scriptsDir = $this->dir.'/scripts';
		$this->scriptsUrl = $this->url.'/scripts';

		/* Start by making the list of shortcodes available */
		$this->kiwip_make_list();

		/* Finally load all shortcode in the list */
		$this->kiwip_load_shortcodes();
	}

	/**
	 * Kiwip Make List
	 * List all shortcodes in the scripts folder et make an array with there type [with javascript interaction or not]
	 * This array is like: [1][name][type]
	 *
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_list(){
		
		/**
		 * - list all directory in scripts folder
		 * - list all files in the current folder
		 * - create the attributes of the shortcode
		 * - increment the array for listing
		 */
		if(is_dir($this->scriptsDir)){
			if($folder = opendir($this->scriptsDir)){ //open the directory

				$i = 0; //increment
				while(($file = readdir($folder)) !== false){ //grab subfolder
					if($file !== '.' AND $file !== '..'){

						/* Constrcut the tree structure in the array to store informations */
						$this->shortcodes[$i] = array('name' => $file); //increment the general array of shortcode

						if($subfiles = scandir($this->scriptsDir.'/'.$file)){
							$options   = false;
							$tinymce   = false;
							$shortcode = false;

							foreach($subfiles as $filename){

								if($filename == 'tinymce.js'){
									$tinymce = $file.'/tinymce.js';
									$this->shortcodes[$i]['tinymce'] = $tinymce; //add information in the array
									$tinymce = true;
								}
								if($filename == 'options.php'){
									$options = $file.'/options.php';
									$this->shortcodes[$i]['options'] = $options; //add information in the array
									$options = true;
								}
								if($filename == 'shortcode.php'){
									$shortcode = $file.'/shortcode.php';
									$this->shortcodes[$i]['shortcode'] = $shortcode; //add information in the array
									$shortcode = true;
								}
								if($filename == 'style.css'){
									$stylesheet = $file.'/style.css';
									$this->shortcodes[$i]['stylesheet'] = $stylesheet; //add information in the array
									$stylesheet = true;
								}


								/**
								 * The type are :
								 * - complete+style: tinymce button + shortcode + options + stylesheet
								 * - complete: tinymce button + shortcode + options
								 * - medium: tinymce button + shortcode
								 * - small: shortcode
								 */
								if($tinymce AND $shortcode AND $options AND $stylesheet){
									$this->shortcodes[$i]['type'] = 'complete+stylesheet';
								}elseif($tinymce AND $shortcode AND $options){
									$this->shortcodes[$i]['type'] = 'complete';
								}elseif($shortcode AND $tinymce AND !$options){
									$this->shortcodes[$i]['type'] = 'medium';
								}elseif($shortcode AND !$tinymce AND !$options){
									$this->shortcodes[$i]['type'] = 'small';
								}else{
									//nothing
								}
							}//end foreach
						}//end if
						$i++;
					}//end if
				}//end while
			}//end if
		}//end if

		// $this->debug($this->shortcodes); die();
	}

	/**
	 * Kiwip Load Shortcodes 
	 * Load all shortcodes in the list ($this->shortcodes | array)
	 */
	public function kiwip_load_shortcodes(){
		/**
		 * For each of theme:
		 * - load the shortcode function
		 * - register the shortcode
		 * - load tinymce plugin (if exsit)
		 * - load options for tinymce (if exsit)
		 * - call all shortcode when wordpress is executed (hook into wordpress)
		 */
		$i = 0;
		foreach($this->shortcodes as $shortcode){

			if(!function_exists('kiwip_shortcode_'.$shortcode['name'])){ //if this shortcode is not already loaded, do it
				require_once($this->scriptsDir.'/'.$shortcode['shortcode']);

				/**
				 * Tell at Wordpress that function is a shortcode
				 * The tag will be the name of the shortcode (name of the shortcode's folder)
				 */
				add_shortcode($shortcode['name'], array('kiwip_shortcode_'.$shortcode['name'], 'make'));


				/**
				 * The type are :
				 * - complete+style: tinymce button + shortcode + options + stylesheet
				 * - complete: tinymce button + shortcode + options
				 * - medium: tinymce button + shortcode
				 * - small: shortcode
				 */
				if($shortcode['type'] != 'small'){
					//load the tinymce button et register it like tinymce plugin
					if(current_user_can('edit_posts') && current_user_can('edit_pages')){ //if the user can write something...
						if(get_user_option('rich_editing') == true){

							//update vars for tinymce
							$this->tinymce[$i]['buttonName'] = $shortcode['name'];
							$this->tinymce[$i]['pluginName'] = $shortcode['name'];
							$this->tinymce[$i]['pluginUrl']  = $this->scriptsUrl.'/'.$shortcode['tinymce'];

						}//end if
					}//end if

					//spacial for css, no authentification necessary
					if($shortcode['type'] == 'complete+stylesheet'){
						$this->stylesheet[$i]['stylesheetId'] = $shortcode['name'];
						$this->stylesheet[$i]['stylesheetURL'] = $this->scriptsUrl.'/'.$shortcode['stylesheet'];
					}
				}//end if

			}//end if
			
			$i++;
		}//end foreach
		
		//adding filters
		add_filter('mce_external_plugins', array($this, 'kiwip_tinymce_add_plugin')); //add the plugin
		add_filter('mce_buttons', array($this, 'kiwip_tinymce_resister_button')); //add the button
		add_action('init', array($this, 'kiwip_stylesheet_add_file')); //add the stylesheet
		add_filter('mce_css', array($this, 'kiwip_tinymce_stylesheet_add_file')); //add the stylesheet into tinymce admin
		// $this->debug($this->shortcodes); die();
	}

	/**
	 * Kiwip Tinymce Register Button
	 * Push the shortcode into the array of buttons, 
	 * adding a divider between the new button and the existing ones
	 * @return array
	 */
	public function kiwip_tinymce_resister_button($buttons){
		foreach($this->tinymce as $key) {
			array_push($buttons, '|', $key['buttonName']);
		}
		return $buttons;
	}

	/**
	 * Kiwip Tinymce Add Plugin
	 * Point to the path and name the JavaScript file
	 * @return array
	 */
	public function kiwip_tinymce_add_plugin($plugin_array){
		foreach($this->tinymce as $key){
			$plugin_array[$key['pluginName']] = $key['pluginUrl'];	
		}
		return $plugin_array;
	}

	/**
	 * Kiwip Stylesheet Add File
	 * Add shortcode stylesheet to the wordpress (with admin)
	 * @return void
	 */
	public function kiwip_stylesheet_add_file(){
		//register
		foreach($this->stylesheet as $key){
			wp_register_style('kiwip_shortcode-css-'.$key['stylesheetId'], $key['stylesheetURL']);
		}
		
		//load
		foreach($this->stylesheet as $eky){
			wp_enqueue_style('kiwip_shortcode-css-'.$key['stylesheetId']);
		}		

		// $this->debug($this->stylesheet); die();
	}

	/**
	 * Kiwip Tinymce Stylesheet Add File
	 * Add shortcode stylesheet to the tinymce (in admin area)
	 * @return string
	 */
	public function kiwip_tinymce_stylesheet_add_file($css_string){
		foreach($this->stylesheet as $key){
			$css_string .= ','.$key['stylesheetURL'];
		}
		return $css_string;
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
