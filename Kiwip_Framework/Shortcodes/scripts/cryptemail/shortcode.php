<?php

class Kiwip_shortcode_cryptemail{

	public function make($atts, $email){
		return '<a href="'.antispambot("mailto:".$email).'">'.antispambot($email).'</a>';
	}

}