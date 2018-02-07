<?php

	// IARC L.Alteyrac 20160829:
	// Before using its own template, the plugin WP Favorite Posts looks for a file named wpfp-page-template.php in the theme
	// @see file wp-favorite-posts.php, function wpfp_list_favorite_posts

	// IARC L.Alteyrac 20130102: Add a title to the favorite posts page
	echo "<center><span style=\"color: #1376ae\"><span style=\"font-size: 24px\">List of your favorite posts</span></span></center><br><br>";
	echo "Below is the list of all the posts you have added as favorite.";
    echo "<br>To add a post to this list, go to the post and click on the link \"Add to favorites\" near the yellow star.";	
    echo "<br>To remove a post from this list, click on the link \"Remove from favorites\" after the post title.";
	echo "You can also open the post and use the link included at the top of the post.";
	echo "<br>To remove all the posts from the favorite list, use the link \"Clear ALL favorites\" at the bottom of this page.<br><br>";
	
    $wpfp_before = "";
    echo "<div class='wpfp-span'>";
    if (!empty($user)) {
        if (wpfp_is_user_favlist_public($user)) {
            $wpfp_before = "$user's Favorite Posts.";
        } else {
            $wpfp_before = "$user's list is not public.";
        }
    }

    if ($wpfp_before):
        echo '<div class="wpfp-page-before">'.$wpfp_before.'</div>';
    endif;

    if ($favorite_post_ids) {
		$favorite_post_ids = array_reverse($favorite_post_ids);
        $post_per_page = wpfp_get_option("post_per_page");
        $page = intval(get_query_var('paged'));

        $qry = array('post__in' => $favorite_post_ids, 'posts_per_page'=> $post_per_page, 'orderby' => 'post__in', 'paged' => $page);
        // custom post type support can easily be added with a line of code like below.
        // $qry['post_type'] = array('post','page');
        
		// IARC L.Alteyrac 20150916: replace query_posts($qry) because according to WP documentation, this should NEVER be used in a plugin
		echo "<ul>";
        foreach ($favorite_post_ids as $curr_id){
			$currpost = get_post($curr_id); 
			echo "<li><a href='".$currpost->guid."' title='". $currpost->post_title ."'>" . $currpost->post_title . "</a> ";
			echo " | " . get_the_category($curr_id)[0]->cat_name . " | ";
			wpfp_remove_favorite_link($curr_id);
			echo "</li>";
		}
        echo "</ul>";

        echo '<div class="navigation">';
            if(function_exists('wp_pagenavi')) { wp_pagenavi(); } else { ?>
            <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
            <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>
            <?php }
        echo '</div>';

        //wp_reset_query(); // IARC L.Alteyrac 20150916: Not needed anymore as we don't use query_posts anymore (see comment above)
    } else {
        $wpfp_options = wpfp_get_options();
        echo "<ul><li>";
        echo $wpfp_options['favorites_empty'];
        echo "</li></ul>";
    }
	echo '<br><hr>'; // IARC L.Alteyrac 20160829: layout
    echo '<p>'.wpfp_clear_list_link().'</p>';
    echo "</div>";
    wpfp_cookie_warning();
?>