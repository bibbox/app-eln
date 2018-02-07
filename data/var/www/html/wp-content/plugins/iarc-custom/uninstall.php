<?php
/*
 * IARC Customization uninstall
*/
if ( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
	exit();
}
delete_option('iarc_options');
?>