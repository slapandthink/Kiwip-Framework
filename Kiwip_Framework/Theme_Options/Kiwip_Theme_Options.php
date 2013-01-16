<?php

/**
 * Kiwip Theme Options
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Kiwip_Theme_Options{

	public $framework_url     = 'http://slapandthink.com';
	public $framework_version = '0.1 beta';
	
	public $dir               = KIWIP_PATH;
	public $url               = KIWIP_URL;
	
	public $page              = '';
	public $args              = array();
	public $sections          = array();
	public $tabs              = array();
	public $extra_tabs        = array();
	public $errors            = array();
	public $warnings          = array();
	public $options           = array();
	
	public $optionName        = ''; // delete this ?

	/**
	 * Constructor
	 * @param array $args
	 * @param array $sections
	 * @param array $extra_tabs
	 *
	 * @since Kiwip Framework 0.1
	 */
	public function __construct($args=array(), $sections=array(), $extra_tabs=array()){

		// fix path
		$this->dir = $this->dir.'/Theme_Options';
		$this->url = $this->url.'Theme_Options';

		/* Cleaning args and sections */
		$this->args     = array();
		$this->sections = array();

		/* Merge args with default args*/
		$this->args = $this->kiwip_defaults_merge($args);

		/* Set sections */
		$this->sections = $sections;
		/* Set tabs */
		$this->tabs = $this->kiwip_make_tabs();
		/* Set extra_tabs */
		$this->extra_tabs = $extra_tabs;

		/* Define page slug */
		$this->args['page_slug'] = strtolower(trim($this->args['option_name'].$this->args['page_slug']));
		/* Define menu title */
		$this->args['menu_title'] = $this->args['option_name'].' '.$this->args['menu_title'];

		/* Grab defaults options */
		add_action('init', array(&$this, 'kiwip_set_default_theme_options'));
		/* Create the menu and load all CSS and JS files needed */
		add_action('admin_menu', array(&$this, 'kiwip_create_page'));
		
		/* Register settings */
		add_action('admin_init', array(&$this, 'kiwip_register_setting'));
		/* Load CSS and JS */
		add_action('admin_print_styles', array(&$this, 'kiwip_enqueue_files_css'));
		add_action('admin_print_scripts', array(&$this, 'kiwip_enqueue_files_js'));
		/* Admin messages hook! */
		add_action('admin_notices', array(&$this, 'kiwip_admin_msgs'));
		/* Load Page for Design hook */
		add_action('load-'.$this->page, array(&$this, 'kiwip_load_design_page'));
	}

	/**
	 * Kiwip Merge Defaults Vars
	 * Merge defaults vars with vars given
	 * @param array $args
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_defaults_merge($args){
		$defaults                   = array();
		$defaults['option_name']    = '';//must be defined by theme/plugin
		$defaults['menu_icon']      = KIWIP_URL.'img/icons/world.png';
		$defaults['menu_title']     = 'Options';
		$defaults['page_icon']      = 'icon-themes';
		$defaults['page_title']     = 'Options';
		$defaults['page_slug']      = '_options';
		$defaults['page_capabilty'] = 'manage_options';
		$defaults['page_type']      = 'menu';
		$defaults['page_parent']    = '';
		$defaults['page_position']  = 100;
		$defaults['footer_credits'] = ' <span id="footer-thankyou"><img src="'.KIWIP_URL.'img/icons/window.png" height="15px" > Options Panel created using the <a href="'.$this->framework_url.'" target="_blank">Kiwip Framework</a> Version '.$this->framework_version.'</span>';
		$defaults['help_tabs']      = array();
		$defaults['help_sidebar']   = '';

		/* Mergeing */
		return wp_parse_args($args, $defaults);
	}

	/**
	 * Kiwip Make Tabs
	 * Construct tabs with sections
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_make_tabs(){
		// tabs are sections
		foreach ($this->sections as $tabs) {
			unset($tabs['fields']); // we don't want fields 
			$this->tabs[] = $tabs;
		}
	}

	/**
	 * Kiwip Default Values
	 * Get default options into an array suitable for the settings API
	 * @return array $defaults
	 * @since Kiwip Framework 0.1
	*/
	public function kiwip_default_values(){

		$defaults = array();
		foreach($this->sections as $k => $section){
			if(isset($section['fields'])){
				foreach($section['fields'] as $fieldk => $field){
					if(!isset($field['std'])){$field['std'] = '';}
						$defaults[$field['id']] = $field['std'];
				}
			}
		}
		//fix for notice on first page load
		$defaults['last_tab'] = 0;
		return $defaults;
	}

	/**
	 * Kiwip Set Default Theme Options
	 * Set the default theme options, or create it if doesn't exist yet
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_set_default_theme_options(){
		if(!get_option($this->args['option_name'])){
			add_option($this->args['option_name'], $this->kiwip_default_values()); // add an named option/value pair to the options database table http://codex.wordpress.org/Function_Reference/add_option
		}
		$this->options = get_option($this->args['option_name']);
	}

	/**
	 * Kiwip Enqueue Files JS
	 * Registers and enqueues (loads) the necessary JavaScript file for working with the
	 * Media Library-driven AJAX File Uploader Module.
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_enqueue_files_js(){
		wp_register_script('kiwip-theme-options-js-general', $this->url.'/js/kiwip-framework.js', array('jquery'), time(), true);
		wp_register_script('kiwip-theme-options-js-media-uploader', $this->url.'/js/medialibrary-uploader.js', array( 'jquery', 'thickbox' ), time(), true);
		wp_enqueue_script('kiwip-theme-options-js-general');
		wp_enqueue_script('kiwip-theme-options-js-media-uploader');
		wp_enqueue_script('media-upload');
	}

	/**
	 * Kiwip Enqueue Files CSS
	 * Adds the Thickbox CSS file and specific loading and button images to the header
	 * on the pages where this function is called.
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_enqueue_files_css(){
		$_html = '';
		$_html .= '<link rel="stylesheet" href="' . site_url() . '/' . WPINC . '/js/thickbox/thickbox.css" type="text/css" media="screen" />' . "\n";
		$_html .= '<script type="text/javascript">
		var tb_pathToImage = "' . site_url() . '/' . WPINC . '/js/thickbox/loadingAnimation.gif";
	    var tb_closeImage = "' . site_url() . '/' . WPINC . '/js/thickbox/tb-close.png";
	    </script>' . "\n";

	    echo $_html;
	}

	/**
	 * Kiwip Create Page
	 * Create the menu page and page
	 * Call the help tabs and sidebars
	 * Call the credits
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_create_page(){

		if($this->args['page_type'] == 'submenu'){
			if(!isset($this->args['page_parent']) || empty($this->args['page_parent'])){
				$this->args['page_parent'] = 'themes.php';
			}
			$this->page = add_submenu_page( // codex: http://codex.wordpress.org/Function_Reference/add_submenu_page
							$this->args['page_parent'],
							$this->args['page_title'],
							$this->args['menu_title'],
							$this->args['page_capabilty'],
							$this->args['page_slug'],
							array(&$this, 'kiwip_render_html') // The function to ba called to output the content for this page 
						);
		}else{
			$this->page = add_menu_page( // http://codex.wordpress.org/Function_Reference/add_menu_page
							$this->args['page_title'],
							$this->args['menu_title'],
							$this->args['page_capabilty'],
							$this->args['page_slug'],
							array(&$this, 'kiwip_render_html'),
							$this->args['menu_icon'],
							$this->args['page_position']
						);
		}

		/* Load help tabs and sidebar */
		add_action('load-'.$this->page, array(&$this, 'kiwip_add_help'));
		/* Load admin footer credits by hook */
		add_filter('admin_footer_text', array(&$this, 'kiwip_footer_credits'));
	}

	/**
	 * Kiwip Register Setting
	 * Register Option for use
	 * The name of all option is setted into the $this->args['option_name'] name, called with get_option()
	 * @since Kiwip Framework 0.1
	*/
	function kiwip_register_setting(){
		register_setting($this->args['option_name'].'_group', $this->args['option_name'], array(&$this,'kiwip_validate_options')); // http://codex.wordpress.org/Function_Reference/register_setting
		foreach($this->sections as $k => $section){
			add_settings_section($k.'_section', $section['title'], array(&$this, 'kiwip_section_callback'), $k.'_section_group_'.$this->args['option_name']); // http://codex.wordpress.org/Function_Reference/add_settings_section
			if(isset($section['fields'])){
				foreach($section['fields'] as $fieldk => $field){
					// default arra to overwite when callin the function
					$defaults = array(
						'id'      => 'default_field',					// the ID of the setting in our options array, and the ID of the HTML form element
						'title'   => 'Default Field',					// the label for the HTML form element
						'desc'    => 'This is a default description.',	// the description displayed under the HTML form element
						'std'     => '',								// the default value for this setting
						'type'    => 'text',							// the HTML form element to use
						'section' => 'main_section',					// the section this setting belongs to - must match the array key of a section in kiwip_options_page_sections();
						'choices' => array(),							// (optional) : the values in radio buttons or a drop-down menu
						'class'   => ''
					);

					// "extract" to be able to use the array keys as variables in our function output below
					extract(wp_parse_args($field, $defaults));

					// addintionnal argument for use in form field output in the function kiwip_form_field_callback!
					$field_args = array(
						'type'      => $type,
						'id'        => $id,
						'title'     => $title,
						'desc'      => $desc,
						'std'       => $std,
						'choices'   => $choices,
						'label_for' => $id,
						'class'     => $class
					);

					add_settings_field($fieldk.'_field', $title, array(&$this,'kiwip_form_field_callback'), $k.'_section_group_'.$this->args['option_name'], $k.'_section', $field_args); // checkbox | http://codex.wordpress.org/Function_Reference/add_settings_field
				}
			}
		}
	}

	/**
	 * Kiwip Section Callback
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_section_callback($value){		
		$id = str_replace('_section', '', $value['id']);

		if(isset($this->sections[$id]['desc']) && !empty($this->sections[$id]['desc'])) {
			echo '<div class="kiwip-options-section-desc">'.$this->sections[$id]['desc'].'</div>';
		}
	}

	/**
	 * Kiwip Render HTML
	 * @return echoes output
	 * @since Kiwip Framework 0.1
	 */
	function kiwip_render_html(){
		echo '<div class="wrap"><!-- wrap -->';

        // displays the page title, tabs and more (if needed)
        $this->kiwip_settings_page_header();

		echo '<form action="options.php" method="post">';
		
		// http://codex.wordpress.org/Function_Reference/settings_fields
		settings_fields($this->args['option_name'].'_group');
		// http://codex.wordpress.org/Function_Reference/do_settings_sections
		foreach($this->sections as $k => $section){

			echo '<div id="'.$k.'_section_group_'.$this->args['option_name'].'" class="kiwip-options-group-tab">';
				do_settings_sections($k.'_section_group_'.$this->args['option_name']);
			echo '</div>';
		}	

		echo '<p class="submit">';
		echo '<input type="submit" name="Submit" class="button-primary" value="Save Changes" >';
		echo '</p>';
		echo '</form>';
		echo '</div><!-- end of wrap -->';
	}

	/**
	 * Kiwip Form Field Callback
	 * all form field types share the same function!!
	 * @return echoes output
	 * @since Kiwip Framework 0.1
	 */
	function kiwip_form_field_callback($args = array()){
		extract($args);
		
		// get the settings sections array
		$kiwip_option_name = $this->args['option_name'];
		$options           = get_option($kiwip_option_name);

		// pass the standard value if the option is not yet set in the database
		if(!isset($options[$id]) && 'type' != 'checkbox'){
			$options[$id] = $std;
		}

		// additional field class. output onlu if the class is defined in the create_setting argument
		$field_class = ($class != '') ? ' '.$class : '';

		// run a switch on option type  
		switch($type){
			case 'text':
				$options[$id] = stripslashes($options[$id]);
				$options[$id] = esc_attr($options[$id]);

				echo "<input class='regular-text$field_class' type='text' id='$id' name='".$kiwip_option_name."[$id]' value='$options[$id]' >";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";


			case 'multi-text':  
	            foreach($choices as $item) {  
	                $item = explode("|",$item); // cat_name|cat_slug  
	                $item[0] = esc_html__($item[0], 'kiwip_textdomain');
	                if (!empty($options[$id])) {
	                    foreach ($options[$id] as $option_key => $option_val){
	                        if ($item[1] == $option_key) {  
	                            $value = $option_val;
	                        }
	                    }  
	                } else {  
	                    $value = '';  
	                }  
	                echo "<span>$item[0]:</span> <input class='$field_class' type='text' id='$id|$item[1]' name='" . $kiwip_option_name . "[$id|$item[1]]' value='$value' /><br/>";  
	            }  
	            //echo ($desc != '') ? "<span class='description'>$desc</span>" : "";  // desactivated cause of reapetition
	        break;
	  
	        case 'textarea':  
	            $options[$id] = stripslashes($options[$id]);  
	            if($class == 'editor'){
	                $editor_settings = array(
	                    'textarea_name' => $kiwip_option_name.'['.$id.']',
	                    'textarea_rows' => 10,
	                    'media_buttons' => false,
	                    'editor_class'  => 'textarea'.$field_class,
	                    'tinymce' => array( 'plugins' => 'wordpress' )
	                );
	                wp_editor($options[$id], $id, $editor_settings);
	            }else{
	                $options[$id] = esc_html($options[$id]);
	                echo "<textarea class='textarea$field_class' id='$id' name='" . $kiwip_option_name . "[$id]' rows='5' cols='30'>$options[$id]</textarea>";  
	            }
	            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
	        break;
	  
	        case 'select':
	            echo "<select id='$id' class='select$field_class' name='" . $kiwip_option_name . "[$id]'>";  
	                foreach($choices as $item) {  
	                    $value  = esc_attr($item, 'kiwip_textdomain');  
	                    $item   = esc_html($item, 'kiwip_textdomain');  
	  
	                    $selected = ($options[$id]==$value) ? 'selected="selected"' : '';  
	                    echo "<option value='$value' $selected>$item</option>";  
	                }  
	            echo "</select>";  
	            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
	        break;  
	  
	        case 'select2':  
	            echo "<select id='$id' class='select$field_class' name='" . $kiwip_option_name . "[$id]'>";  
	            foreach($choices as $item) {  
	  
	                $item = explode("|",$item);  
	                $item[0] = esc_html($item[0], 'kiwip_textdomain');  
	  
	                $selected = ($options[$id]==$item[1]) ? 'selected="selected"' : '';  
	                echo "<option value='$item[1]' $selected>$item[0]</option>";  
	            }  
	            echo "</select>";  
	            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
	        break;  
	  
	        case 'checkbox':  
	            echo "<input class='checkbox$field_class' type='checkbox' id='$id' name='" . $kiwip_option_name . "[$id]' value='1' " . checked( $options[$id], 1, false ) . " />";  
	            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
	        break;
	  
	        case "multi-checkbox":  
	            foreach($choices as $item) {  
	  
	                $item = explode("|",$item);  
	                $item[0] = esc_html($item[0], 'kiwip_textdomain');  
	  
	                $checked = '';  
	  
	                if ( isset($options[$id][$item[1]]) ) {  
	                    if ( $options[$id][$item[1]] == 'true') {  
	                        $checked = 'checked="checked"';  
	                    }  
	                }  
	  
	                echo "<input class='checkbox$field_class' type='checkbox' id='$id|$item[1]' name='" . $kiwip_option_name . "[$id|$item[1]]' value='1' $checked /> $item[0] <br/>";  
	            }  
	            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
	        break;

	        // Uploader
	        case "upload":
	            if($class == 'no-tabs'){
	                // kiwip_medialibrary_uploader( $id, $value, $mode = 'full', $desc = '', $postid = 0, $name = '', $tab)
	                echo $this->kiwip_medialibrary_uploader( $id, $options[$id], 'full', $desc, 0, $kiwip_option_name.'['.$id.']', false );
	            }else{
	                // kiwip_medialibrary_uploader( $id, $value, $mode = 'full', $desc = '', $postid = 0, $name = '', $tab)
	                echo $this->kiwip_medialibrary_uploader( $id, $options[$id], 'full', $desc, 0, $kiwip_option_name.'['.$id.']', true );
	            }
	        break;
	            
		}
	}

	/**
	 * Kiwip Validate Options
	 * validate inputs
	 * more info: http://codex.wordpress.org/Data_Validation
	 * @return array
	 * @since Kiwip Framework 0.1
	 */
	function kiwip_validate_options($input){
		// for enhanced security, create a new empty array
		$valid_input = array();

		foreach($this->sections as $section){

			

			// run a foreach and switch on option type
			foreach($section['fields'] as $option){

				switch ($option['type']) {  
	                case 'text':  
	                    //switch validation based on the class!  
	                    switch ($option['class']) {  
	                        //for numeric  
	                        case 'numeric':
	                            //accept the input only when numeric!  
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
	                            $valid_input[$option['id']] = (is_numeric($input[$option['id']])) ? $input[$option['id']] : 'Expecting a Numeric value!';  
	  							
	                            
	                            // register error  
	                            if(is_numeric($input[$option['id']]) == FALSE) {  
	                                add_settings_error(  
	                                    $option['id'], // setting title  
	                                    $this->arge['option_name'] . '_txt_numeric_error', // error ID  
	                                    __('Expecting a Numeric value! Please fix.','kiwip_textdomain'), // error message  
	                                    'error' // type of message  
	                                );  
	                            }  
	                        break;  
	  
	                        //for multi-numeric values (separated by a comma)  
	                        case 'multinumeric':  
	                            //accept the input only when the numeric values are comma separated  
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
	  

	                            if($input[$option['id']] !=''){  
	                                // /^-?\d+(?:,\s?-?\d+)*$/ matches: -1 | 1 | -12,-23 | 12,23 | -123, -234 | 123, 234  | etc.  
	                                $valid_input[$option['id']] = (preg_match('/^-?\d+(?:,\s?-?\d+)*$/', $input[$option['id']]) == 1) ? $input[$option['id']] : __('Expecting comma separated numeric values','kiwip_textdomain');  
	                            }else{  
	                                $valid_input[$option['id']] = $input[$option['id']];  
	                            }  
	  
	                            // register error  
	                            if($input[$option['id']] !='' && preg_match('/^-?\d+(?:,\s?-?\d+)*$/', $input[$option['id']]) != 1) {  
	                                add_settings_error(  
	                                    $option['id'], // setting title  
	                                    $this->arge['option_name'] . '_txt_multinumeric_error', // error ID  
	                                    __('Expecting comma separated numeric values! Please fix.','kiwip_textdomain'), // error message  
	                                    'error' // type of message  
	                                );  
	                            }  
	                        break;  
	  
	                        //for no html  
	                        case 'nohtml':  
	                            //accept the input only after stripping out all html, extra white space etc!
	                            $input[$option['id']]       = sanitize_text_field($input[$option['id']]); // need to add slashes still before sending to the database  
	                            $valid_input[$option['id']] = addslashes($input[$option['id']]);  
	                        break;  
	  
	                        //for url  
	                        case 'url':  
	                            //accept the input only when the url has been sanited for database usage with esc_url_raw()  
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
	                            $valid_input[$option['id']] = esc_url_raw($input[$option['id']]);  
	                        break;  
	  
	                        //for email  
	                        case 'email':  
	                            //accept the input only after the email has been validated
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
	                            if($input[$option['id']] != ''){  
	                                $valid_input[$option['id']] = (is_email($input[$option['id']])!== FALSE) ? $input[$option['id']] : __('Invalid email! Please re-enter!','kiwip_textdomain');  
	                            }elseif($input[$option['id']] == ''){  
	                                $valid_input[$option['id']] = __('This setting field cannot be empty! Please enter a valid email address.','kiwip_textdomain');  
	                            }  
	  
	                            // register error
	                            if(is_email($input[$option['id']]) == FALSE || $input[$option['id']] == '') {  
	                                add_settings_error(  
	                                    $option['id'], // setting title  
	                                    $this->arge['option_name'] . '_txt_email_error', // error ID  
	                                    __('Please enter a valid email address.','kiwip_textdomain'), // error message  
	                                    'error' // type of message  
	                                );  
	                            }  
	                        break;  
	  
	                        // a "cover-all" fall-back when the class argument is not set  
	                        default:  
	                            // accept only a few inline html elements  
	                            $allowed_html = array(  
	                                'a' => array('href' => array(),'title' => array()),  
	                                'b' => array(),  
	                                'em' => array(),
	                                'i' => array(),
	                                'strong' => array()  
	                            );  
	  
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
	                            $input[$option['id']]       = force_balance_tags($input[$option['id']]); // find incorrectly nested or missing closing tags and fix markup  
	                            $input[$option['id']]       = wp_kses( $input[$option['id']], $allowed_html); // need to add slashes still before sending to the database  
	                            $valid_input[$option['id']] = addslashes($input[$option['id']]);  
	                        break;  
	                    }  
	                break;  
	  
	                case "multi-text":  
	                    // this will hold the text values as an array of 'key' => 'value'  
	                    unset($textarray);  
	  
	                    $text_values = array();

	                    foreach ($option['choices'] as $k => $v ) {
	                        // explode the connective  
	                        $pieces = explode("|", $v);  
	  
	                        $text_values[] = $pieces[1];  
	                    }
	                    foreach ($text_values as $v) {
	                        // Check that the option isn't empty
	                        if (!empty($input[$option['id'] . '|' . $v])) { 
	                            // If it's not null, make sure it's sanitized, add it to an array 
	                            switch ($option['class']) { 
	                                // different sanitation actions based on the class create you own cases as you need them 
	 
	                                //for numeric input 
	                                case 'numeric': 
	                                    //accept the input only if is numberic! 
	                                    $input[$option['id'] . '|' . $v]= trim($input[$option['id'] . '|' . $v]); // trim whitespace 
	                                    $input[$option['id'] . '|' . $v]= (is_numeric($input[$option['id'] . '|' . $v])) ? $input[$option['id'] . '|' . $v] : ''; 
	                                break; 
	 
	                                // a "cover-all" fall-back when the class argument is not set 
	                                default: 
	                                    // strip all html tags and white-space. 
	                                    $input[$option['id'] . '|' . $v]= sanitize_text_field($input[$option['id'] . '|' . $v]); // need to add slashes still before sending to the database 
	                                    $input[$option['id'] . '|' . $v]= addslashes($input[$option['id'] . '|' . $v]); 
	                                break; 
	                            } 
	                            // pass the sanitized user input to our $textarray array 
	                            $textarray[$v] = $input[$option['id'] . '|' . $v];
	                        }else{ 
	                            $textarray[$v] = ''; 
	                        } 
	                    }
	                    // pass the non-empty $textarray to our $valid_input array 
	                    if (!empty($textarray)) { 
	                        $valid_input[$option['id']] = $textarray;
	                    }
	                break;

	                case 'textarea': 
	                    // switch validation based on the class! 
	                    switch ( $option['class'] ) { 
	                        // for only inline html 
	                        case 'inlinehtml': 
	                            // accept only inline html 
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace 
	                            $input[$option['id']]       = force_balance_tags($input[$option['id']]); // find incorrectly nested or missing closing tags and fix markup 
	                            $input[$option['id']]       = addslashes($input[$option['id']]); //wp_filter_kses expects content to be escaped! 
	                            $valid_input[$option['id']] = wp_filter_kses($input[$option['id']]); //calls stripslashes then addslashes 
	                        break; 
	 
	                        // for no html 
	                        case 'nohtml': 
	                            // accept the input only after stripping out all html, extra white space etc! 
	                            $input[$option['id']]       = sanitize_text_field($input[$option['id']]); // need to add slashes still before sending to the database 
	                            $valid_input[$option['id']] = addslashes($input[$option['id']]); 
	                        break; 
	 
	                        // for allowlinebreaks 
	                        case 'allowlinebreaks': 
	                            // accept the input only after stripping out all html, extra white space etc! 
	                            $input[$option['id']]       = wp_strip_all_tags($input[$option['id']]); // need to add slashes still before sending to the database 
	                            $valid_input[$option['id']] = addslashes($input[$option['id']]); 
	                        break;

	                        // for tinyMCE editor
	                        case 'editor':
	                            // accept all html
	                            $valid_input[$option['id']] = wpautop($input[$option['id']]);
	                        break;
	 
	                        // a "cover-all" fall-back when the class argument is not set 
	                        default: 
	                            // accept only limited html 
	                            //my allowed html 
	                            $allowed_html = array( 
	                                'a'             => array('href' => array (),'title' => array ()), 
	                                'b'             => array(), 
	                                'blockquote'    => array('cite' => array ()), 
	                                'br'            => array(), 
	                                'dd'            => array(), 
	                                'dl'            => array(), 
	                                'dt'            => array(), 
	                                'em'            => array(), 
	                                'i'             => array(), 
	                                'li'            => array(), 
	                                'ol'            => array(),
	                                'ul'            => array(), 
	                                'p'             => array(), 
	                                'q'             => array('cite' => array ()), 
	                                'strong'        => array(), 
	                                'h1'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
	                                'h2'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
	                                'h3'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
	                                'h4'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
	                                'h5'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
	                                'h6'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()) 
	                            ); 
	 
	                            $input[$option['id']]       = trim($input[$option['id']]); // trim whitespace 
	                            $input[$option['id']]       = force_balance_tags($input[$option['id']]); // find incorrectly nested or missing closing tags and fix markup 
	                            $input[$option['id']]       = wp_kses( $input[$option['id']], $allowed_html); // need to add slashes still before sending to the database 
	                            $valid_input[$option['id']] = addslashes($input[$option['id']]); 
	                        break; 
	                    } 
	                break; 
	 
	                case 'select': 
	                    // check to see if the selected value is in our approved array of values! 
	                    $valid_input[$option['id']] = (in_array( $input[$option['id']], $option['choices']) ? $input[$option['id']] : '' ); 
	                break; 
	 
	                case 'select2': 
	                    // process $select_values 
	                        $select_values = array(); 
	                        foreach ($option['choices'] as $k => $v) { 
	                            // explode the connective 
	                            $pieces = explode("|", $v); 
	 
	                            $select_values[] = $pieces[1]; 
	                        } 
	                    // check to see if selected value is in our approved array of values! 
	                    $valid_input[$option['id']] = (in_array( $input[$option['id']], $select_values) ? $input[$option['id']] : '' ); 
	                break; 
	 
	                case 'checkbox': 
	                    // if it's not set, default to null!  
	                    if (!isset($input[$option['id']])) {  
	                        $input[$option['id']] = null;  
	                    }  
	                    // Our checkbox value is either 0 or 1  
	                    $valid_input[$option['id']] = ( $input[$option['id']] == 1 ? 1 : 0 );  
	                break;  
	  
	                case 'multi-checkbox':  
	                    unset($checkboxarray);  
	                    $check_values = array();  
	                    foreach ($option['choices'] as $k => $v ) {  
	                        // explode the connective  
	                        $pieces = explode("|", $v);  
	  
	                        $check_values[] = $pieces[1];  
	                    }  
	  
	                    foreach ($check_values as $v ) {          
	  
	                        // Check that the option isn't null  
	                        if (!empty($input[$option['id'] . '|' . $v])) { 
	                            // If it's not null, make sure it's true, add it to an array 
	                            $checkboxarray[$v] = 'true'; 
	                        } 
	                        else { 
	                            $checkboxarray[$v] = 'false'; 
	                        } 
	                    } 
	                    // Take all the items that were checked, and set them as the main option 
	                    if (!empty($checkboxarray)) { 
	                        $valid_input[$option['id']] = $checkboxarray;  
	                    }  
	                break;

	                case 'upload':
	                    $valid_input[$option['id']] = '';
	                    $filetype = wp_check_filetype($input[$option['id']]);
	                    if ($filetype["ext"]) {
	                        $valid_input[$option['id']] = $input[$option['id']];
	                    }
	                break;
	            }  
	        }
		}
		return $valid_input; // return validated input
	}

	/**
	 * Kiwip Add Help
	 * Add tabs and sidebars for help
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_add_help(){
		$screen = get_current_screen();

		if(is_array($this->args['help_tabs'])){
			foreach($this->args['help_tabs'] as $tab){
				$screen->add_help_tab($tab);
			}
		}
		
		if($this->args['help_sidebar'] != ''){
			$screen->set_help_sidebar($this->args['help_sidebar']);
		}
	}

	/**
	 * Kiwip Footer Credits
	 * Add the footer credits
	 * @since Kiwip Framework 0.1
	 */
	function kiwip_footer_credits($footer_text){
		$screen = get_current_screen();
		if($screen->parent_file == $this->args['page_slug']){
			return $this->args['footer_credits'];
		}else{
			return $footer_text;
		}
	}

	/**
	 * Kiwip Show MSG
	 * Helper function for creating admin messages
	 * src: http://www.wprecipes.com/how-to-show-an-urgent-message-in-the-wordpress-admin-area
	 * 
	 * @param (string) $message The message to echo
	 * @param (string) $msgclass The message class
	 * @return echoes the message
	 * @since Kiwip Framework 0.1
	 */
	function kiwip_show_msg($message, $msgclass = 'info'){
		echo "<div id='message' class='$msgclass'>$message</div>";
	}

	/**
	 * Kiwip Admin MSG
	 * Callback function for displaying admin messages
	 * @return calls kiwip_show_msg()
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_admin_msgs(){
		// check for our settings page - need this in conditional further down
		$kiwip_settings_pg = strpos($_GET['page'], $this->args['page_slug']);
		// collect setting error/notices: http://codex.wordpress.org/Function_Reference/get_settings_errors
		$set_errors = get_settings_errors();

		// display admin message only for the admin to see, only on our settings page and only when setting errors/notices are returned!
		if(current_user_can('manage_options') && $kiwip_settings_pg !== FALSE && !empty($set_errors)){
			
			// have our settings successfully been updated?
			if($set_errors[0]['code'] == 'settings_updated' && isset($_GET['settings-updated'])){
			$this->kiwip_show_msg("<p>".$set_errors[0]['message']."</p>", 'updated');

			// have errors been found?
			}else{
				// there maybe more than one so run a foreach loop.
				foreach($set_errors as $set_error){
					// set the title attribute to matchthe error "setting title" - need this in js file
					$this->kiwip_show_msg("<p class='setting-error-message' title='".$set_error['setting']."'>".$set_error['message']."</p>", 'error');
				}
			}
		}
	}

	/**
	 * Kiwip Medialibrary Uploader Init
	 * Sets up a custom post type to attach image to.  This allows us to have
	 * individual galleries for different uploaders.
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_medialibrary_uploader_init () {
		register_post_type( 'kiwip_framework', array(
			'labels' => array(
				'name' => 'Kiwip Framework Internal Container',
			),
			'public' => true,
			'show_ui' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'supports' => array( 'title', 'editor' ), 
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => false
		) );
	}

	/**
	 * Kiwip Medialibrary Uploader
	 * Media Uploader Using the WordPress Media Library.
	 *
	 * Parameters:
	 * - string $_id - A token to identify this field (the name).
	 * - string $_value - The value of the field, if present.
	 * - string $_mode - The display mode of the field.
	 * - string $_desc - An optional description of the field.
	 * - int $_postid - An optional post id (used in the meta boxes).
	 *
	 * Dependencies:
	 * - kiwip_mlu_get_silentpost()
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_medialibrary_uploader( $_id, $_value, $_mode = 'full', $_desc = '', $_postid = 0, $_name = '', $tab=true) {
		// setting tabs or not, by default yes
		if($tab == false){
			
		}

		// Call kiwip_medialibrary_uploader_init for register post type (get silent post)
		$this->kiwip_medialibrary_uploader_init();

		// Gets the unique option id
		$option_name = $this->args['option_name'];

		$output = '';
		$id = '';
		$class = '';
		$int = '';
		$value = '';
		$name = '';
		
		$id = strip_tags( strtolower( $_id ) );
		// Change for each field, using a "silent" post. If no post is present, one will be created.
		$int = $this->kiwip_medialibrary_uploader_get_silentpost( $id );
		
		// If a value is passed and we don't have a stored value, use the value that's passed through.
		if ( $_value != '' && $value == '' ) {
			$value = $_value;
		}
		
		if ($_name != '') {
			$name = $_name;
		}
		else {
			$name = $option_name.'['.$id.']';
		}
		
		if ( $value ) { $class = ' has-file'; }
		$output .= '<input id="' . $id . '" class="upload' . $class . ' file" type="text" name="'.$name.'" value="' . $value . '" />' . "\n";
		$output .= '<input id="upload_' . $id . '" class="upload_button button" type="button" value="' . __( 'Upload', 'kiwip_textdomain') . '" rel="' . $int . '" />' . "\n";
		
		if ( $_desc != '' ) {
			$output .= '<br /><span class="description">' . $_desc . '</span>' . "\n";
		}
		
		$output .= '<div class="screenshot" id="' . $id . '_image">' . "\n";
		
		if ( $value != '' ) { 
			$remove = '<br /><a href="javascript:(void);" class="mlu_remove button">Remove</a>';
			$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
			if ( $image ) {
				$output .= '<img src="' . $value . '" alt="" />'.$remove.'';
			} else {
				$parts = explode( "/", $value );
				for( $i = 0; $i < sizeof( $parts ); ++$i ) {
					$title = $parts[$i];
				}

				// No output preview if it's not an image.			
				$output .= '';
			
				// Standard generic output if it's not an image.	
				$title = __( 'View File', 'kiwip_textdomain' );
				$output .= '<div class="no_image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span>' . $remove . '</div>';
			}	
		}
		$output .= '</div>' . "\n";

		return $output;
	}

	/**
	 * Kiwip Medialibrary Uploader Get Silentpost
	 * Uses "silent" posts in the database to store relationships for images.
	 * This also creates the facility to collect galleries of, for example, logo images.
	 * 
	 * Return: $_postid.
	 *
	 * If no "silent" post is present, one will be created with the type "kiwip_framework"
	 * and the post_name of "of-$_token".
	 *
	 * Example Usage:
	 * kiwip_mlu_get_silentpost ( 'of_logo' );
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_medialibrary_uploader_get_silentpost ( $_token ) {
		global $wpdb;
		$_id = 0;

		// Check if the token is valid against a whitelist.
		// $_whitelist = array( 'of_logo', 'of_custom_favicon', 'of_ad_top_image' );
		// Sanitise the token.
		
		$_token = strtolower( str_replace( ' ', '_', $_token ) );
		
		// if ( in_array( $_token, $_whitelist ) ) {
		if ( $_token ) {
			
			// Tell the function what to look for in a post.
			
			$_args = array( 'post_type' => 'kiwip_framework', 'post_name' => 'of-' . $_token, 'post_status' => 'draft', 'comment_status' => 'closed', 'ping_status' => 'closed' );
			
			// Look in the database for a "silent" post that meets our criteria.
			$query = 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_parent = 0';
			foreach ( $_args as $k => $v ) {
				$query .= ' AND ' . $k . ' = "' . $v . '"';
			} // End FOREACH Loop
			
			$query .= ' LIMIT 1';
			$_posts = $wpdb->get_row( $query );
			
			// If we've got a post, loop through and get it's ID.
			if ( count( $_posts ) ) {
				$_id = $_posts->ID;
			} else {
			
				// If no post is present, insert one.
				// Prepare some additional data to go with the post insertion.
				$_words = explode( '_', $_token );
				$_title = join( ' ', $_words );
				$_title = ucwords( $_title );
				$_post_data = array( 'post_title' => $_title );
				$_post_data = array_merge( $_post_data, $_args );
				$_id = wp_insert_post( $_post_data );
			}	
		}
		return $_id;
	}

	/**
	 * Kiwip Load Design Page
	 * Load some design stuff
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_load_design_page(){
		add_action( 'admin_head', array(&$this, 'kiwip_design_css'));
	}

	/**
	 * Kiwip Design CSS
	 * Load some desin stuff CSS
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_design_css(){
		//echo '<style type="text/css" media="screen">';
		//echo '#'.$this->page.' .wp-menu-image, a { background: url('.KIWIP_URL.'img/icons/world.png) center center no-repeat !important; }';
		//echo '</style>';
	}

	/**
	 * Kiwip Get Admin Page
	 * helper function: check for pages and return the current page name
	 * @return string
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_get_admin_page(){
		global $pagenow;

		// read the current page
		$current_page = trim($_GET['page']); // delete space

		// use a different way to read the current page name when the form submits
		if($pagenow == 'options.php'){
			// get the page name
			$parts = explode('page=', $_POST['_wp_http_referer']); // http://codex.wordpress.org/Function_Reference/wp_referer_field

			// prevent resubmiting bug
			if(substr_count($parts[1], '&settings-updated=true') > 0){
				$parts[1] = str_replace('&settings-updated=true', '', $parts[1]);
			}
			$page  = $parts[1];

			// account for the use of tabs (we do not want the tab name to be part of our return value!)
			$t = strpos($page, "&");

			if($t !== FALSE){
				$page = substr($page, 0, $t);
			}

			$current_page = trim($page);
		}

		return $current_page;
	}

	/**
	 * Kiwip Get The Tab
	 * helper function: check for tabs and return the current tab name
	 * @return string
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_get_the_tab(){
		global $pagenow;

		// set default tab
		$default_tab = $this->sections[0]['slug'];

		// read the current tab when on our settings page
		$current_tab = (isset($_GET['tab']) ? $_GET['tab'] : $default_tab);

		// use a different way to read the tab when the form submits
		if($pagenow == 'options.php'){
			// need to read the tab name so we explode()!
			$parts = explode('&tab=', $_POST['_wp_http_referer']); // http://codex.wordpress.org/Function_Reference/wp_referer_field  
			
			// prevent resubmiting bug
			if(substr_count($parts[1], '&settings-updated=true') > 0){
				$parts[1] = str_replace('&settings-updated=true', '', $parts[1]);
			}

			// count the "exploded" parts
			$partsNum = count($parts);

			// account for "&settings-updated=true" (we don't want that to be part of our return value!)
			// is it "&settings-updated=true" there? - check for the "&"
			$settings_updated = strpos($part[1], "&");

			// filter it out and get the tab name
			$tab_name = ($settings_updated !== FALSE ? substr($parts[1], 0, $settings_updated) : $parts[1]);

			// use id found, otherwise pass the default tab name
			$current_tab = ($partsNum == 2 ? trim($tab_name) : $default_tab);
		}

		return $current_tab;
	}

	/**
	 * Kiwip Settings Page Header
	 * helper function: creates settings page title tabs (if needed)
	 * @return echos output
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_settings_page_header(){
		// get the current tab
		$current_tab = $this->kiwip_get_the_tab();

		// display the icon and page title
		echo '<div id="icon-options-general" class="icon32"></div>';
		echo '<h2>'.$this->args['option_name'].' Options</h2>';

		/* Add some html stuff here */
		$this->kiwip_add_custom_header_html();


		// wrap each in anchor html tags
		$links = array();

		foreach($this->sections as $tab){
			// set anchor class
			$class = ($tab['slug'] == $current_tab ? 'nav-tab nav-tab-active '.$tab['slug'] : 'nav-tab '.$tab['slug']);
			$page = $_GET['page'];

			// is the tab (section) have an icon
			if(isset($tab['icon']) AND !empty($tab['icon'])){
				$image = '<img src="'.$tab['icon'].'" alt="tab_icon" height="10px" > ';
			}else{
				$image = '';
			}

			// the link
			$links[] = '<a class="'.$class.'" href="?page='.$page.'&tab='.$tab['slug'].'">'.$image.'<span>'.$tab['title'].'</span></a>';
		}

		echo '<h3 class="nav-tab-wrapper">';
		foreach($links as $link){
			echo $link;
		}
		echo '</h3>';
	}

	/**
	 * Kiwip Add Custom Header HTML
	 * Add some HTML stuff in the header of options page
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_add_custom_header_html(){
		if($this->args['custom_html_header']){
			require_once $this->args['custom_html_header'];
		}
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
