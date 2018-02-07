<?php

/*
 * IARC L.Alteyrac 20170127
 *	- Add custom buttons in the TinyMCE toolbar (Post's editor and Comments) (@see https://www.gavick.com/blog/wordpress-tinymce-custom-buttons)
 *	- Add some markdowns
 *	- Filter the list of posts in the link dialog box
 * 
*/

function iarc_add_buttons() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    
	// check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'add_tinymce_iarc_plugins');
        add_filter('mce_buttons', 'register_iarc_buttons');
    }
}
add_action('admin_head', 'iarc_add_buttons');

// Specify the path to the scripts
function add_tinymce_iarc_plugins($plugin_array) {
    $plugin_array['lab_button'] = plugins_url( '/js/lab.js', __FILE__ );
	$plugin_array['instruments_button'] = plugins_url( '/js/instruments.js', __FILE__ );
	$plugin_array['columns_button'] = plugins_url( '/js/columns.js', __FILE__ );
	$plugin_array['selectall_button'] = plugins_url( '/js/selectall.js', __FILE__ );
    return $plugin_array;
}
// Add button(s) in the editor
function register_iarc_buttons($buttons) {
   //array_unshift($buttons, 'lab_button');
   array_splice($buttons, 1, 0, 'lab_button');
   array_splice($buttons, 2, 0, 'instruments_button');
   array_splice($buttons, 3, 0, 'columns_button');
   array_splice($buttons, 4, 0, 'selectall_button');
   return $buttons;
}

// Add CSS 
function iarc_buttons_css() {
    wp_enqueue_style('iarc-buttons', plugins_url('/css/iarc-buttons.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'iarc_buttons_css');

// Add some TinyMCE buttons to the comment editor
function comment_editor() {
  ob_start();
  
  wp_editor( '', 'comment', array(
    'textarea_rows' => 8,
    //'teeny' => true,
    'quicktags' => false,
	'media_buttons' => false,
	'tinymce' => array('toolbar1'=> 'bold,italic,underline,strikethrough,separator,removeformat,bullist,numlist,link,unlink,charmap')
  ) );
  
  $editor = ob_get_contents();
  ob_end_clean();
  return $editor;
}
add_filter( 'comment_form_field_comment', 'comment_editor' );

function add_comment_css() {
	echo "
	<style type='text/css'>
	#wp-comment-editor-container {
		border: 2px solid #DFDFDF;
		margin: 10px 0 10px 0;
	}
	</style>
	";
}
add_action('wp_head', 'add_comment_css');

// IARC L.Alteyrac 20170411: add some markdowns
function iarc_mce_init_wptextpattern( $init ) {
   $init['wptextpattern'] = wp_json_encode( array(
      'inline' => array(
        array( 'delimiter' => '**', 'format' => 'bold' ),
        array( 'delimiter' => '__', 'format' => 'italic' ),
		array( 'delimiter' => '~~', 'format' => 'strikethrough' ),
		array( 'delimiter' => '`', 'format' => 'code' )
      ),
   ) );

   return $init;
}
add_filter( 'tiny_mce_before_init', 'iarc_mce_init_wptextpattern' );

// IARC L.Alteyrac 20170130: filter list "Or link to existing content"
// Don't display posts that can not be read by the current user
function filter_wp_link_query( $results, $query ) { 
	foreach ( $results as $resultElement => $result ) {
		if ( ! current_user_can( 'read_post', $result['ID'] )){
			unset( $results[$resultElement] );
		}
	}
	return $results;
};
add_filter( 'wp_link_query', 'filter_wp_link_query', 10, 2 );

?>