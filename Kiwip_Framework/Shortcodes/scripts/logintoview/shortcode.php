<?php


class kiwip_shortcode_logintoview{

	public function make($atts, $content=null){
		if (is_user_logged_in() && !is_null( $content ) && !is_feed())  
	        return do_shortcode($content);
	    return ''; 
	}

}