<?php

/*
 * IARC L.Alteyrac 20160830
 * Extends class Last_Updated_Posts_Widget (@see last-updated-posts-widget.php) to customize the query used to get the list of posts
 * 2 functions are overridden: 
 *		- widget, to cut long titles and add non breaking space in the date
 *		- getListOfPosts, to filter the list of posts by authorization
*/

if ( ! class_exists( 'Last_Updated_Posts_Widget' ) ) {
	require_once( ABSPATH . '/wp-content/plugins/last-updated-posts-widget/last-updated-posts-widget.php' );
}

function iarc_last_update_widget_registration() {
  unregister_widget('Last_Updated_Posts_Widget');
  register_widget('Iarc_Last_Updated_Posts_Widget');
}
add_action('widgets_init', 'iarc_last_update_widget_registration');

class Iarc_Last_Updated_Posts_Widget extends Last_Updated_Posts_Widget {
	
	// Displaying the widget on the blog
	function widget($args, $instance) {
		extract($args);

		$title              = apply_filters('widget_title', $instance['title']);
		$totalPostsToShow   = (int) $instance['totalPostsToShow'];
		$displayDate	= (int) $instance['displayDate'];
		$dateFormat		= apply_filters('dateFormat', $instance['dateFormat']);

		$defaults           = array (
									'title'             => 'Last Updated Posts Widget',
									'totalPostsToShow'  => 5,									
									'displayDate'       => 1,
									'dateFormat'        => "jS F'y"
									);										
								
		echo $before_widget;

		if ($title != "") {
			echo $before_title . $title . $after_title;
		} else {
			echo $before_title . $defaults['title'] . $after_title;
		}

		$postList = $this->getListOfPosts($totalPostsToShow);

		if (!empty($postList)) {
			echo "<ul>";
			foreach ($postList as $obj) {
				$idlink = $obj->ID;
				$luplink = get_permalink($idlink);
				//echo $luplink;
				echo "<li class='page_item page-item-{$obj->ID}'>";
				if (strlen($obj->post_title) > 40) {
					echo "<a href=".$luplink." title='{$obj->post_title}'>".mb_substr($obj->post_title,0,40)."(...)</a>";
				} else {
					echo "<a href=".$luplink." title='{$obj->post_title}'>{$obj->post_title}</a>";
				}
				if ($displayDate == 1) {
					echo " - <span style=\"color:#8C8C8C\"><i>" . str_replace(" ","&nbsp",date($dateFormat, strtotime($obj->post_modified))) . "</i></span>";
				}
				echo "</li>";
			}
			echo "</ul>";
		}
		echo $after_widget;
	}
	
	// Getting the list of posts based on the option set by the user
	function getListOfPosts($totalPostsToShow) {
		$r = new WP_Query( apply_filters( 'widget_posts_args',
							array( 'posts_per_page' => $totalPostsToShow,
									'no_found_rows' => true,
									'post_status' => 'publish',
									'ignore_sticky_posts' => true,
									'orderby' => 'modified', 'order' => 'DESC'  ) ) );
		$list = $r->get_posts();
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$val->uri = get_permalink($val->ID);
			}
		}

		return $list;
	}
}

?>