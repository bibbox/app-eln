<?php
/*
	Plugin Name: IARC Customization for ELN
	Plugin URI:
	Description: Plugin containing several IARC customizations for the Electronic (Laboratory) Notebook
	Author: Lucile Alteyrac
	Author URI: http://www.iarc.fr
	Version: 1.1
	
	Plugin dependencies:
		- Category and authors
		- Last Updated Posts Widget
		- Post Notification
		- TinyMCE Advanced
*/

global $iarc_custom_version;
$iarc_custom_version = "0.1";

global $wp_version;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

include_once( 'iarc-admin-custom.php' );
include_once( 'iarc-category-reminder.php' );
include_once( 'iarc-dashboard.php' );
if ( is_plugin_active('tinymce-advanced/tinymce-advanced.php') ) {
	include_once( 'iarc-tinymce-editor.php' );
}
include_once( 'iarc-login-page.php' );
include_once( 'iarc-media-library.php' );
include_once( 'iarc-mime-types.php' );
include_once( 'iarc-post-revision-display.php' );
if ( $wp_version >= 4.4 ) {
   // These customizations don't work with previous version of WP,
   // as the directory wp-includes/widgets does not exist before WP4.4
   include_once( 'iarc-archives-widget-custom.php' );
   include_once( 'iarc-recent-widget.php' );
}
include_once( 'iarc-texturization.php' );
include_once( 'iarc-user-notification.php' );
include_once( 'iarc-user-update.php' );

if ( is_plugin_active('last-updated-posts-widget/last-updated-posts-widget.php') ) {
	include_once( 'iarc-last-updated-posts-widget.php');
}

include_once( dirname(__FILE__) . '/forms/iarc-forms.php' );

// Default settings
include_once( dirname(__FILE__) . '/settings/iarc-default-options.php' );
register_activation_hook( __FILE__, 'iarc_customization_activation' );

// --------------------------------------
function launch_iarc_custom() {
	if ( is_plugin_active('categories_authors/categories_authors.php') ) {
		include_once( 'iarc-categories-authors.php' );
		$cat_aut = new Iarc_CategoryAuthorPlugin;
	}
	
	if ( is_plugin_active('post-notification/post_notification.php') ) {
		include_once( 'iarc-post-notification.php' );
		//$post_notif = new Iarc_Walker_post_notification;
	}
	
	wp_enqueue_style('iarc-info', plugins_url('/css/iarc-info.css', __FILE__));
}
add_action( 'wp_loaded' , 'launch_iarc_custom' );

// --------------------------------------
// Add an entry to the Settings menu and the link "Settings" in the Plugins page
function iarc_custom_settings() {
	include( dirname(__FILE__) . "/settings/iarc-options.php" );
}
function add_iarc_plugin_menu() {
	add_options_page( 'IARC', 'IARC Customization', 'manage_options', 'iarc-options', 'iarc_custom_settings' );
}
add_action( 'admin_menu', 'add_iarc_plugin_menu' );

function add_action_links ( $links ) {
	$settings_link = array(
		'<a href="' . admin_url( 'options-general.php?page=iarc-options' ) . '">Settings</a>',
	);
	return array_merge( $settings_link, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

?>