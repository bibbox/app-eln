<?php
// Register default settings for the IARC Customization plugin
function iarc_customization_activation() {
	$iarc_options = get_option('iarc_options');
	
	$iarc_options['htmlEditorHeight'] = "500";
	$iarc_options['displayEditorInfo'] = '0';
	$iarc_options['disableAutosave']  = '1';
	$iarc_options['displayComments']  = '0';
	$iarc_options['displayHome'] 	  = '1';
	
	$iarc_options['colSortableList']['author'] = '0'; 
	$iarc_options['colSortableList']['categories'] = '0'; 
	$iarc_options['colSortableList']['tags'] = '0';
	
	$iarc_options['maxAttachmentSize'] = '2';
	
	update_option('iarc_options', $iarc_options);
}
?>