<?php

// IARC L.Alteyrac 20141029: custom css for the login page
function iarc_css_login() {
	//wp_enqueue_style( 'login_css', get_template_directory_uri() . '/style-login.css' ); // When using Atahualpa
	wp_enqueue_style( 'login_css', get_stylesheet_directory_uri() . '/style-login.css' ); // When using Atahualpa Child Theme for IARC
}
add_action('login_head', 'iarc_css_login');

// IARC L.Alteyrac 20160817: Display information text before login form, with the CSS defined above (style-login.css)
function iarc_login_message(){
	$iarc_options = get_option('iarc_options');
	$logintext = isset($iarc_options['loginPageText']) ? $iarc_options['loginPageText'] : '' ;
	echo $logintext;
}
add_filter('login_message','iarc_login_message');

?>