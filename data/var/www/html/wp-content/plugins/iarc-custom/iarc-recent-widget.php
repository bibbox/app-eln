<?php
/*
 * IARC L.Alteyrac 20160818
 *  - Create a class that inherits WP_Widget_Recent_Posts
 *  - override function widget, to custom the date display (date format and style)
 *
 * Note: the following css could also be used:
 *		span.post-date::before {
 *			color: black;
 *			content: "- ";
 *		}
 *		span.post-date {
 *		color: #8C8C8C;
 *		font-style: italic;
 *		white-space: nowrap;
 *		}
 *
*/

if ( ! class_exists( 'WP_Widget_Recent_Posts' ) ) {
	require_once( ABSPATH . WPINC . '/widgets/class-wp-widget-recent-posts.php' );
}

function iarc_recent_widget_registration() {
  unregister_widget('WP_Widget_Recent_Posts');
  register_widget('Iarc_Recent_Posts_Widget');
}
add_action('widgets_init', 'iarc_recent_widget_registration');

Class Iarc_Recent_Posts_Widget extends WP_Widget_Recent_Posts {
	
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		/**
		 * Filters the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );

		if ($r->have_posts()) :
		?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
			<?php if ( $show_date ) : ?>
				
				<?php // IARC custom: add a hyphen, change date color and font-style, change date format, replace spaces by non-breaking spaces ?>
				<?php echo "- "; ?>
				<span class="post-date" style="color:#8C8C8C; font-style:italic;"><?php echo str_replace(" ","&nbsp",get_the_date('M j, Y')); ?></span>
				
			<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget']; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;
	}
}

?>