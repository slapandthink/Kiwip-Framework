<?php

class kiwip_fieldtype_image {

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
		echo $this->kiwip_medialibrary_uploader( $id_name, $value, 'full', '', 0, 'kiwip['.$id_name.']', false );
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
	 * @return mixed
	 * @since Kiwip Framework 0.1
	 */
	public function kiwip_medialibrary_uploader( $_id, $_value, $_mode = 'full', $_desc = '', $_postid = 0, $_name = '', $tab=true) {
		// setting tabs or not, by default yes
		if($tab == false){
			
		}

		// Call kiwip_medialibrary_uploader_init for register post type (get silent post)
		$this->kiwip_medialibrary_uploader_init();

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
			$name = 'kiwip['.$id.']';
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


}