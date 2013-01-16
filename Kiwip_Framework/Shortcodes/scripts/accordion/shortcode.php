<?php

/**
 * Shortcode class
 */
class kiwip_shortcode_accordion{

	/**
	 * Make
	 * Function called by the framework to instanciate the shortcode [REQUIRED]
	 * @param array $atts, string $content
	 * @return
	 */
	function make($atts, $content=null){
		extract(shortcode_atts(array(
			'title' => 'your title'
		), $atts));

		$html  = '<dl class="accordion">';
		$html .= '<dt>'.$title.'<span></span></dt>';
		$html .= '<dd>'.do_shortcode($content).'</dd>';
		$html .= '</dl>';

		return $html;
		// return self::test();
	}

	function test(){
		return "It works!";
	}

}
