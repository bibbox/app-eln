<?php

global $iarc_forms_version;
$iarc_forms_version = "0.1";

// ------------------------------------
// Install plugin: 
// - create new table in the database
// - store version number in the database
// ------------------------------------
function iarc_forms_install(){
  
  global $wpdb;
  $table_iarc_forms = $wpdb->prefix . 'iarc_forms';
  
  $sql = "CREATE TABLE $table_iarc_forms (
			cat_id bigint( 20 ) NOT NULL,
			date_of_issue datetime default NULL,
			date_of_first_entry datetime default NULL,
			date_of_last_entry datetime default NULL,
			checking varchar(500) default NULL,
			approved_by varchar(60) default NULL,
			was_located tinyint(1) default 0,
			archived tinyint(1) default 0,
			date_of_archival datetime default NULL,
			PRIMARY KEY  ( cat_id )   
			)";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
  
  add_option( "iarc_forms_version", $iarc_forms_version );

}

?>