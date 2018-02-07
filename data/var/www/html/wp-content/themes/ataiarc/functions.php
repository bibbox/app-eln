<?php

function iarc_theme_enqueue_styles() {
	$parent_style = 'atahualpa-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ), wp_get_theme()->get('Version'));
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style-login.css', array( $parent_style ), wp_get_theme()->get('Version'));
	
}
add_action( 'wp_enqueue_scripts', 'iarc_theme_enqueue_styles' );

add_action( 'after_setup_theme', 'ataiarc_setup' );

function ataiarc_setup(){

	// IARC L.Alteyrac 20160825: Replace Parent's function for Custom Excerpts
	// 20120430: Excerpts causes slowness in the TOC when it contains huge posts or several pages
	// @see function bfa_wp_trim_excerpt in Parent's theme Atahualpa functions.php
	function iarc_wp_trim_excerpt($text) {

		global $bfa_ata;

		if ( '' <> $text ) {
			$words = preg_split("/\s+/u", $text);
			$custom_read_more = str_replace('%permalink%', get_permalink(), $bfa_ata['custom_read_more']);
			if ( get_the_title() == '' ) { 
				$custom_read_more = str_replace('%title%', 'Permalink', $custom_read_more);
			} else {		
				$custom_read_more = str_replace('%title%', the_title('','',FALSE), $custom_read_more);
			}
			array_push($words, $custom_read_more);
			$text = implode(' ', $words);
			return $text;
		}

		return $text;
	}

	remove_filter('get_the_excerpt', 'bfa_wp_trim_excerpt');
	add_filter('get_the_excerpt', 'iarc_wp_trim_excerpt');
	
}

// IARC L.Alteyrac 20180131: Enable "Links" menu in Dashboard
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

?>