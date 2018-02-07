<?php

/*
 * IARC L.Alteyrac 20170412
 * Add a widget to the WP Dashboard, with some information on where to find... the info
 *
*/ 

function iarc_dashboard_info() {
	$iarc_options = get_option('iarc_options');	
	echo $iarc_options['dashboardInfo'];
}

function iarc_custom_dashboard_widget() {
	$iarc_options = get_option('iarc_options');
  	if ( !empty($iarc_options['dashboardInfo']) )
		add_meta_box('id', $iarc_options['dashboardBoxTitle'], 'iarc_dashboard_info', 'dashboard', 'normal', 'high');
}

add_action('wp_dashboard_setup', 'iarc_custom_dashboard_widget');

?>