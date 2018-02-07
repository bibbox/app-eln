<?php

/*
 * IARC L.Alteyrac 20160818
 *  - Replace function wp_new_user_notification and function wp_notify_postauthor
 *  - Email messages for admin, new user, and post's author (comments) are customized for IARC
 * @see wp-includes/pluggable.php
*/

if ( !function_exists('wp_new_user_notification') ) :
/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @since 2.0.0
 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
 * @since 4.3.1 The `$plaintext_pass` parameter was deprecated. `$notify` added as a third parameter.
 * @since 4.6.0 The `$notify` parameter accepts 'user' for sending notification only to the user created.
 *
 * @global wpdb         $wpdb      WordPress database object for queries.
 * @global PasswordHash $wp_hasher Portable PHP password hashing framework instance.
 *
 * @param int    $user_id    User ID.
 * @param null   $deprecated Not used (argument deprecated).
 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
 */
function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
        if ( $deprecated !== null ) {
                _deprecated_argument( __FUNCTION__, '4.3.1' );
        }

        global $wpdb, $wp_hasher;
        $user = get_userdata( $user_id );

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
        // we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        if ( 'user' !== $notify ) {
                // IARC L.Alteyrac 20160818: Custom email message for admin
                $message  = sprintf( __( 'A new user has been created in %s' ), $blogname ) . " (" . network_site_url() . "):\r\n\r\n";
                $message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n";
                $message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n\r\n";
                $message .= __( '* This email has been sent by the IARC custom plugin *' ) . "\r\n";

                @wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
        }

        // `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notifcation.
        if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
                return;
        }

        // Generate something random for a password reset key.
        $key = wp_generate_password( 20, false );

        /** This action is documented in wp-login.php */
        do_action( 'retrieve_password_key', $user->user_login, $key );

        // Now insert the key, hashed, into the DB.
        if ( empty( $wp_hasher ) ) {
                require_once ABSPATH . WPINC . '/class-phpass.php';
                $wp_hasher = new PasswordHash( 8, true );
        }
        $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

        //$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        //$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
        //$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

        // IARC L.Alteyrac 20150826: Custom email message
        $message = __('Dear ' . sprintf(__('%s'), $user->user_firstname)) . ',' . "\r\n\r\n";
        $message .= __('Your access to the ELN has been created. Your username is ' . $user->user_login) .  "\r\n\r\n";
        $message .= __('To set your password, please visit the following address:') . "\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";
        $message .= __('After setting your password, you can login to the ELN from this address: ') . network_site_url() . "\r\n\r\n";
        $message .= __('The User Guide is online at ' . network_site_url() . '/user-guide/') . "\r\n\r\n";
        $message .= __('Don\'t hesitate to contact the ELN team (eln@iarc.fr) if you have any question or suggestion.') . "\r\n\r\n";
        $message .= __('Have a nice day,') . "\r\n\r\n";
        $message .= __('Lucile') . "\r\n\r\n";
        $message .= __('*** This is an automatic email, please do not reply ***') . "\r\n\r\n";

        //$message .= wp_login_url() . "\r\n";

        wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}
endif;

if ( !function_exists('wp_notify_postauthor') ) :
/**
 * Notify an author (and/or others) of a comment/trackback/pingback on a post.
 *
 * @since 1.0.0
 *
 * @param int|WP_Comment  $comment_id Comment ID or WP_Comment object.
 * @param string          $deprecated Not used
 * @return bool True on completion. False if no email addresses were specified.
 */
function wp_notify_postauthor( $comment_id, $deprecated = null ) {
	if ( null !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '3.8.0' );
	}

	$comment = get_comment( $comment_id );
	if ( empty( $comment ) || empty( $comment->comment_post_ID ) )
		return false;

	$post    = get_post( $comment->comment_post_ID );
	$author  = get_userdata( $post->post_author );

	// Who to notify? By default, just the post author, but others can be added.
	$emails = array();
	if ( $author ) {
		$emails[] = $author->user_email;
	}

	/**
	 * Filters the list of email addresses to receive a comment notification.
	 *
	 * By default, only post authors are notified of comments. This filter allows
	 * others to be added.
	 *
	 * @since 3.7.0
	 *
	 * @param array $emails     An array of email addresses to receive a comment notification.
	 * @param int   $comment_id The comment ID.
	 */
	$emails = apply_filters( 'comment_notification_recipients', $emails, $comment->comment_ID );
	$emails = array_filter( $emails );

	// If there are no addresses to send the comment to, bail.
	if ( ! count( $emails ) ) {
		return false;
	}

	// Facilitate unsetting below without knowing the keys.
	$emails = array_flip( $emails );

	/**
	 * Filters whether to notify comment authors of their comments on their own posts.
	 *
	 * By default, comment authors aren't notified of their comments on their own
	 * posts. This filter allows you to override that.
	 *
	 * @since 3.8.0
	 *
	 * @param bool $notify     Whether to notify the post author of their own comment.
	 *                         Default false.
	 * @param int  $comment_id The comment ID.
	 */
	$notify_author = apply_filters( 'comment_notification_notify_author', false, $comment->comment_ID );

	// The comment was left by the author
	if ( $author && ! $notify_author && $comment->user_id == $post->post_author ) {
		unset( $emails[ $author->user_email ] );
	}

	// The author moderated a comment on their own post
	if ( $author && ! $notify_author && $post->post_author == get_current_user_id() ) {
		unset( $emails[ $author->user_email ] );
	}

	// The post author is no longer a member of the blog
	if ( $author && ! $notify_author && ! user_can( $post->post_author, 'read_post', $post->ID ) ) {
		unset( $emails[ $author->user_email ] );
	}

	// If there's no email to send the comment to, bail, otherwise flip array back around for use below
	if ( ! count( $emails ) ) {
		return false;
	} else {
		$emails = array_flip( $emails );
	}

	$switched_locale = switch_to_locale( get_locale() );

	$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	$comment_content = wp_specialchars_decode( $comment->comment_content );

	switch ( $comment->comment_type ) {
		case 'trackback':
			/* translators: 1: Post title */
			$notify_message  = sprintf( __( 'New trackback on your post "%s"' ), $post->post_title ) . "\r\n";
			/* translators: 1: Trackback/pingback website name, 2: website IP, 3: website hostname */
			$notify_message .= sprintf( __('Website: %1$s (IP: %2$s, %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
			$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
			$notify_message .= __( 'You can see all trackbacks on this post here:' ) . "\r\n";
			/* translators: 1: blog name, 2: post title */
			$subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
			break;
		case 'pingback':
			/* translators: 1: Post title */
			$notify_message  = sprintf( __( 'New pingback on your post "%s"' ), $post->post_title ) . "\r\n";
			/* translators: 1: Trackback/pingback website name, 2: website IP, 3: website hostname */
			$notify_message .= sprintf( __('Website: %1$s (IP: %2$s, %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
			$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
			$notify_message .= sprintf( __( 'Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
			$notify_message .= __( 'You can see all pingbacks on this post here:' ) . "\r\n";
			/* translators: 1: blog name, 2: post title */
			$subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
			break;
		default: // Comments
			// IARC L.Alteyrac 20170123: Main message changed, IP and author domain removed from the text, URL removed too
			$notify_message  = sprintf( __( 'Hello %s,' ), $author->display_name ) . "\r\n\r\n";
			$notify_message  .= sprintf( __( 'A new comment has been written on your post "%s"' ), $post->post_title ) . "\r\n";
			/* translators: 1: comment author, 2: author IP, 3: author domain */
			$notify_message .= sprintf( __( 'Author: %1$s' ), $comment->comment_author ) . "\r\n";
			$notify_message .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "\r\n\r\n";
			//$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
			$notify_message .= sprintf( __('Comment: %s' ), "\r\n" . $comment_content ) . "\r\n\r\n";
			$notify_message .= __( 'You can see all comments on this post here:' ) . "\r\n";
			/* translators: 1: blog name, 2: post title */
			$subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
			break;
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
	$notify_message .= sprintf( __('Permalink: %s'), get_comment_link( $comment ) ) . "\r\n";

	// IARC L.Alteyrac 20170123: links removed from the email
	/*if ( user_can( $post->post_author, 'edit_comment', $comment->comment_ID ) ) {
		if ( EMPTY_TRASH_DAYS ) {
			$notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment->comment_ID}#wpbody-content" ) ) . "\r\n";
		} else {
			$notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment->comment_ID}#wpbody-content" ) ) . "\r\n";
		}
		$notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment->comment_ID}#wpbody-content" ) ) . "\r\n";
	}*/

	$wp_email = 'wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));

	if ( '' == $comment->comment_author ) {
		$from = "From: \"$blogname\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: $comment->comment_author_email";
	} else {
		$from = "From: \"$comment->comment_author\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
	}

	$message_headers = "$from\n"
		. "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

	if ( isset($reply_to) )
		$message_headers .= $reply_to . "\n";

	/**
	 * Filters the comment notification email text.
	 *
	 * @since 1.5.2
	 *
	 * @param string $notify_message The comment notification email text.
	 * @param int    $comment_id     Comment ID.
	 */
	$notify_message = apply_filters( 'comment_notification_text', $notify_message, $comment->comment_ID );

	/**
	 * Filters the comment notification email subject.
	 *
	 * @since 1.5.2
	 *
	 * @param string $subject    The comment notification email subject.
	 * @param int    $comment_id Comment ID.
	 */
	$subject = apply_filters( 'comment_notification_subject', $subject, $comment->comment_ID );

	/**
	 * Filters the comment notification email headers.
	 *
	 * @since 1.5.2
	 *
	 * @param string $message_headers Headers for the comment notification email.
	 * @param int    $comment_id      Comment ID.
	 */
	$message_headers = apply_filters( 'comment_notification_headers', $message_headers, $comment->comment_ID );

	foreach ( $emails as $email ) {
		@wp_mail( $email, wp_specialchars_decode( $subject ), $notify_message, $message_headers );
	}

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	return true;
}
endif;
?>