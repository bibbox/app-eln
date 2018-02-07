<?php

/*
 * IARC L.Alteyrac 20160830
 * Display the list of the revisions of a post, with links to WP Revisions screen
*/ 

function iarc_revisions_list(){
	if (!$post = get_post(get_the_ID())) {
		return;
	}
	
	$revisions = wp_get_post_revisions($post, array('post_type' => 'revision'));
	
	foreach ($revisions as $revision) {
		
		if ( wp_is_post_autosave( $revision ) ) {
			// we don't want to display autosave
		}
		else if (strtotime($revision->post_date_gmt) <= strtotime($post->post_date_gmt)) {
			// we don't want pre-publication revision;
		}
		else {
			$rawdate = wp_post_revision_title($revision, false);
			$date = date_format(date_create_from_format('F j, Y @ H:i:s', $rawdate), 'j F Y @ H:i');
			$revlink = '<a href="' . admin_url() . 'revision.php?revision=' . $revision->ID . '">' . "$date</a>";
			$name = get_the_author_meta('display_name', $revision->post_author);
		
			if ( !isset($revdisplay) ) {
				// IARC L.Alteyrac 20160822: add "[Current Revision]" to the last revision (= first link in the list)
				$revlink = $revlink . ' [Current Revision]';
				$revdisplay = '';
			}
			
			$revdisplay .= "\t<li>$revlink by $name</li>\n";
		}
	}
	
	if ( isset($revdisplay) && $revdisplay ) {
		echo "<h5>Post Revisions:</h5>";
		echo "<ul>$revdisplay</ul>";
	}
	else {
		echo "<h5>No Revision</h5>";
	}
}

?>