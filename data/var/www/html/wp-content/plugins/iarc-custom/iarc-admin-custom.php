<?php

/*
 * IARC L.Alteyrac 20160822: Custom of "admin" parts
 *	- (Dashboard) Hide WP update notification
 *	- (Dashboard) Add a link "My Posts" in the Posts menu (Note 20160831: WP includes now a link "Mine" at the top of Posts' list...)
 * 	- (Admin bar) Add links to some pages, such as documentation's pages
 *	- (Dashboard/Lists of Posts) Register columns as sortable
 *	- (Post Edition) Disable autosave, activated in post-new.php
 *	- (Post Edition) Add info above the editor, if defined in the settings
 *	- (Post Edition) Height of HTML editor
*/

// --------------------------------------
function remove_wp_update_notice() {
	if ( !current_user_can('manage_options') ) {
	  remove_action( 'admin_notices', 'update_nag', 3);
	}
}
add_action('admin_init', 'remove_wp_update_notice');

// --------------------------------------
function add_myposts_link() {
    if (function_exists('add_submenu_page')) {
        add_submenu_page('edit.php','My Posts','My Posts', 'edit_posts', 'edit.php?post_type=post&author='.get_current_user_id());
    }
}
add_action('admin_menu', 'add_myposts_link');

// --------------------------------------
function add_button_to_admin_bar( $wp_admin_bar, $button_details ){
	$parent_menu = $button_details['parent'];
	if ( $parent_menu != '' ) {
		$wp_admin_bar->add_node(array( 'id' => $parent_menu, 'title' => $parent_menu));
	}
	if ( $button_details['display'] == '1' ){
		$wp_admin_bar->add_node(array( 'parent' => $parent_menu, 'id' => $button_details['id'], 'title' => $button_details['title'], 'href' => get_permalink($button_details['page']) ));
	}
}
function custom_admin_bar_buttons() {
  global $wp_admin_bar;
  
  if ( !is_admin_bar_showing() )
      return;
  
  $iarc_options = get_option('iarc_options');
  
  if( isset( $iarc_options['displayComments'] ) && $iarc_options['displayComments'] == '0')
	$wp_admin_bar->remove_node('comments');
  
  if( isset( $iarc_options['displayHome'] ) && $iarc_options['displayHome'] == '1')
	$wp_admin_bar->add_node(array( 'id' => 'home', 'title' => ('Home'), 'href' => get_site_url() ));
  
  if( isset( $iarc_options['buttons'] ) ) {
	  $custom_buttons = $iarc_options['buttons'];
	  foreach ( $custom_buttons as $button_details ) {
		  add_button_to_admin_bar( $wp_admin_bar, $button_details );
	  }    
  }
}
add_action( 'admin_bar_menu', 'custom_admin_bar_buttons',1000 );

// --------------------------------------
function custom_columns_sortable( $sortable_columns ) {
	$iarc_options = get_option('iarc_options');
	
	if ( isset($iarc_options['colSortableList']) ){
		if ( $iarc_options['colSortableList']['author'] == '1' ) $sortable_columns['author'] = 'author';
		if ( $iarc_options['colSortableList']['categories'] == '1' ) $sortable_columns['categories'] = 'categories';
		if ( $iarc_options['colSortableList']['tags'] == '1' ) $sortable_columns['tags'] = 'tags';
	}
    return $sortable_columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'custom_columns_sortable' );

// --------------------------------------
function disable_autosave() {
	$iarc_options = get_option('iarc_options');
	if( isset( $iarc_options['disableAutosave'] ) && $iarc_options['disableAutosave'] == '1')
        wp_deregister_script( 'autosave' );
}
add_action( 'admin_init', 'disable_autosave' );

// --------------------------------------
// Add some info before Editor, if info is defined in the Settings page
function iarc_edit_form_after_title() {
	$iarc_options = get_option('iarc_options');
	if( isset( $iarc_options['displayEditorInfo'] ) && $iarc_options['displayEditorInfo'] == '1') {
		if ( !empty($iarc_options['editorInfo']) ) {
			if( isset( $iarc_options['withNotice'] ) && $iarc_options['withNotice'] == '1')
				echo '<p class="editor-info notice notice-info is-dismissible">' . $iarc_options['editorInfo'] . '</p>';
			else
				echo '<p class="editor-info">' . $iarc_options['editorInfo'] . '</p>';
		}
	}
}
add_action( 'edit_form_after_title', 'iarc_edit_form_after_title' );

// --------------------------------------
function html_text_area_height() {
	$iarc_options = get_option('iarc_options');
	if ( isset( $iarc_options['htmlEditorHeight'] ) && $iarc_options['htmlEditorHeight'] != '') 
		$html_height=$iarc_options['htmlEditorHeight'];
	else
		$html_height="500";
	
	if (substr($html_height, -2) != 'px')
		$html_height = $html_height . 'px';
	
	echo '<style type="text/css">
				iframe#content_ifr{ min-height : '. $html_height  .' !important; }
				</style>';
}
add_action( 'admin_head', 'html_text_area_height' );

?>