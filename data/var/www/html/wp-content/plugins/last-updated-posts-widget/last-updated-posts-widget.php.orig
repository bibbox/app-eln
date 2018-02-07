<?php
/*
Plugin Name: Last Updated Posts Widget
Plugin URI: http://wordpress.org/extend/plugins/last-updated-posts-widget/
Description: Display a list of the last updated/modified posts in a sidebar widget. 
Version: 0.5.1
Author: Andrea Developer
Author URI: http://wordpress.org/extend/plugins/last-updated-posts-widget/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St - 5th Floor, Boston, MA  02110-1301, USA.

*/

//-------------------------------------------------- * Registering the widget
    
    function last_updated_posts_widget() {
        register_widget('Last_Updated_Posts_Widget');
    }
    
    // Class Last Updated Posts is extending WP_Widget class
    class Last_Updated_Posts_Widget extends WP_Widget {
        function Last_Updated_Posts_Widget() {
            $widgetSettings     = array (
                                        'classname'     => 'Last_Updated_Posts_Widget',
                                        'description'   => 'Display a list of the last updated/modified posts in a sidebar widget.'
                                        );
            
            $controlSettings    = array (
                                        'width'         => 400,
                                        'height'        => 400,
                                        'id_base'       => 'last_updated_posts_widget'
                                        );
                                        
            $this->WP_Widget('last_updated_posts_widget', 'Last Updated Posts Widget', $widgetSettings, $controlSettings);
        }

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
						echo "<a href=".$luplink." title='{$obj->post_title}'>{$obj->post_title}</a>";
			if ($displayDate == 1) {
                            echo " - " . date($dateFormat, strtotime($obj->post_modified));
                        }
                        echo "</li>";
                    }
                echo "</ul>";
            }
            echo $after_widget;
        }        

        // Updating the settings
        function update($new_instance, $old_instance) {
            $instance                       = $old_instance;
            $instance['title']              = strip_tags($new_instance['title']);
            $instance['totalPostsToShow']   = strip_tags($new_instance['totalPostsToShow']);
            $instance['displayDate']        = strip_tags($new_instance['displayDate']);
            $instance['dateFormat']         = strip_tags($new_instance['dateFormat']);

            return $instance;
        }

        // WP Admin panel form to modify the setting
        function form($instance) {

            $defaults       = array ( 
                                    'title'             => 'Last Updated Posts Widget', 
                                    'totalPostsToShow'  => 5,
                                    'displayDate'       => 1,
                                    'dateFormat'        => "jS F'y"
                                    );
                                    
            $instance       = wp_parse_args((array) $instance, $defaults);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('totalPostsToShow'); ?>">Total Posts to Show:</label>
			<input id="<?php echo $this->get_field_id('totalPostsToShow'); ?>" name="<?php echo $this->get_field_name('totalPostsToShow'); ?>" value="<?php echo $instance['totalPostsToShow']; ?>" size="5" /> <i>( leave empty to show all posts )</i>
		</p>
        <p>
                <label for="<?php echo $this->get_field_id('dateFormat'); ?>">Date Format:</label>
                <input id="<?php echo $this->get_field_id('dateFormat'); ?>" name="<?php echo $this->get_field_name('dateFormat'); ?>" value="<?php echo $instance['dateFormat']; ?>" size="15" />
        </p>
        <p>
                <label for="<?php echo $this->get_field_id('displayDate'); ?>">Display Date:</label>
                <input id="<?php echo $this->get_field_id('displayDate'); ?>" name="<?php echo $this->get_field_name('displayDate'); ?>" value="1" type="checkbox"

           <?php
           if ($instance['displayDate'] == 1) {
               echo "Checked";
           }
           ?>

           />
        </p>
        <p>
<hr/>
<b>Information on Date Format</b>
<hr/><small>
d - Day of the month, 2 digits with leading zeros (01 to 31)<br/>
D - 3 letter textual representation of a day (Mon through Sun)<br/>
j - Day of the month without leading zeros (1 to 31)<br/>
F - A full textual representation of a month (January through December)<br/>
m - Numeric representation of a month, with leading zeros (01 through 12)<br/>
M - A short textual representation of a month, three letters (Jan through Dec)<br/>
Y - A full numeric representation of a year, 4 digits (2000 or 2009)<br/>
y - A two digit representation of a year (98 or 09)<br/>
g - 12-hour format of an hour without leading zeros (1 through 12)<br/>
G - 24-hour format<br/>
i - Minutes with leading zeros (00 to 59)<br/>
s - Seconds, with leading zeros (00 through 59)<br/>
<a href="http://www.php.net/date" target="_blank" title="More information on Date Format">More Info on Date Format</a></small>
</p>
<?php
        }

        // Getting the list of posts based on the option set by the user
        function getListOfPosts($totalPostsToShow) {
            GLOBAL $wpdb;

            $postTypeWhere      = "post_type = 'post'";

			if ($totalPostsToShow != "") { 

            $sql            = "SELECT ID, post_title, post_modified FROM
                                {$wpdb->posts} WHERE
                                post_status = 'publish' AND
                                {$postTypeWhere}
                                ORDER BY post_modified DESC 
                                LIMIT {$totalPostsToShow} ";
            } else {

            $sql            = "SELECT ID, post_title, post_modified FROM
                                {$wpdb->posts} WHERE
                                post_status = 'publish' AND 
                                {$postTypeWhere}
                                ORDER BY post_modified DESC ";
            }		

            $list           = (array) $wpdb->get_results($sql);

            return $list;
        }
		
		
    }		

    // Adding the functions to the WP widget
    add_action('widgets_init', 'last_updated_posts_widget');
    
?>
