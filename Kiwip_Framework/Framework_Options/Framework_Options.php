<?php

/**
 * Framework Options
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class Framework_Options{

	/* General options */
	public $dir     = KIWIP_PATH;
	public $url     = KIWIP_URL;
	public $options = array();

	/* Specific options */
	public $delete_revision;


	/**
	 * Construct
	 */
	public function __construct(){

		// fix path
		$this->dir = $this->dir.'/Framework_Options';
		$this->url = $this->url.'Framework_Options';

		/*$this->debug($this->url.'/css/kiwip-framework-options.css'); die();*/

		/* Load the css */
		wp_register_style('kiwip-framework-options-css', $this->url.'/css/kiwip-framework-options.css', 'false', time(), 'screen');
		wp_enqueue_style('kiwip-framework-options-css');

		/* Create the page of options */
		$this->kiwip_create_page();

		/* Load the action caller */
		$this->kiwip_action_caller();
	}

	/**
	 * Kiwip Create Page
	 * Create the page of options with the Kiwip Theme Options class of Kiwip Framework
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_create_page(){
		/* Define options_args */
		$options_args = array();
		$options_args['option_name'] = 'Kiwip';
		/* Define sections */
		$options_sections[] = array(
			'icon' => KIWIP_URL.'/img/icons/pen.png',
			'title' => 'Posts Configurations',
			'slug' => 'posts_section',
			'desc' => '<p class="description">Some additionnal options for posts management in WordPress</p>',
			'fields' => array(
				array(
					"id"      => 'delete_revisions',
					"title"   => 'Do you want to delete all posts revision ?',
					"desc"    => 'This will run the SQL query:<br />DELETE FROM wp_posts WHERE post_type = "revision";',
					"type"    => "checkbox",
					"std"     => 0
					)
				)
			);

		$options_args['custom_html_header'] = KIWIP_PATH.'Framework_Options/header.php';
		$options_args['page_position'] = 101;

		$options_args['help_tabs'][] = array(
									'id' => 'kiwip-options-framework-1',
									'title' => 'Information Framework',
									'content' => '<p>This is the tab content, HTML is allowed. Information 1</p>'
									);
		$options_args['help_tabs'][] = array(
									'id' => 'kiwip-options-framework-1',
									'title' => 'Information Framework',
									'content' => '<p>This is the tab content, HTML is allowed. Information 2</p>'
									);

		//Set the Help Sidebar for the options page - no sidebar by default										
		$options_args['help_sidebar'] = '<p>This is the sidebar content, HTML is allowed.</p>';

		$options_extra_tabs = array();

		/* Create page */
		$kiwip_options = new Kiwip_Theme_Options($options_args, $options_sections, $options_extra_tabs);
	}

	/**
	 * Kiwip Action Caller
	 * Test and call all options available
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_action_caller(){
		/* Grab options of Kiwip Framework Options */
		$this->options = get_option('Kiwip');

		if($this->options['delete_revisions'] == 1){
			$this->kiwip_action_delete_revisions();
		}
	}

	/**
	 * Kiwip Action Delete Revisions
	 * Delete all posts revision of the WordPress
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_action_delete_revisions(){
		global $wpdb;

		$this->delete_revision = $wpdb->query('DELETE FROM wp_posts WHERE post_type = "revision";');

		add_action('admin_notices', array(&$this, 'kiwip_action_delete_revisions_adm_msg'));
		$updated_option = array('delete_revisions' => 0);
		update_option('Kiwip', $updated_option);
		$this->options['delete_revisions'] == 0;
	}
	/**
	 * Kiwip Action Delete Revisions Adm Msg
	 * Show message for delete revisios action
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_action_delete_revisions_adm_msg(){
		if(current_user_can('manage_options')){
	       	echo '<div id="message" class="updated fade"><p><strong>All posts revisions have been deleted correctly.</strong></p></div>';
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
