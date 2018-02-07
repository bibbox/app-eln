<?php
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}
/**
 * options.php - The settings screen.
 *
 * @package Login Configurator
 * @subpackage includes/settings
 * @author GrandSlambert
 * @copyright 2009-2013
 * @access public
 * @since 1.6
 */
?>

<div class="postbox">
     <h3 class="handl" style="margin:0;padding:3px;cursor:default;">
		<?php _e( 'Settings', 'login-configurator' ); ?>
     </h3>
     <div class="table">
          <table class="form-table">
               <tr align="top">
                    <th scope="row"><?php _e( 'Force Login?', 'login-configurator' ); ?></th>
                    <td><label>
                              <input type="radio" id="login_configurator_force_no" name="<?php print $this->optionsName; ?>[force]" value="off" <?php checked( $this->options['force'], 'off' ); ?> />
						<?php _e( '<strong>OFF</strong> - Do not force login on any page.', 'login-configurator' ); ?>
                         </label>
                         <br>
                         <label>
                              <input type="radio" id="login_configurator_force_yes" name="<?php print $this->optionsName; ?>[force]" value="all" <?php checked( $this->options['force'], 'all' ); ?> />
						<?php _e( '<strong>ON</strong> - User must log in to see entire site, including home page.', 'login-configurator' ); ?>
                         </label>
                         <br>
                         <label>
                              <input type="radio" id="login_configurator_force_inside" name="<?php print $this->optionsName; ?>[force]" value="inside" <?php checked( $this->options['force'], 'inside' ); ?> />
						<?php _e( '<strong>TEASER</strong> - Allow non logged in users to see the home page, but force login on all other pages.', 'login-configurator' ); ?>
                         </label>
                         <br>
                         <label>
                              <input type="radio" id="login_configurator_force_posts" name="<?php print $this->optionsName; ?>[force]" value="posts" <?php checked( $this->options['force'], 'posts' ); ?> />
						<?php _e( '<strong>POSTS</strong> - Only force login when a user wants to see a full post.', 'login-configurator' ); ?>
                         </label>
                    </td>
               </tr>
			<tr align="top">
                    <th scope="row"><?php _e('URLs to ignore (Whitelist)', 'login-configurator'); ?></th>
                    <td><textarea id="login_configurator_whitelistURLs" name="<?php print $this->optionsName; ?>[whitelistURLs]" style="width:100%; height:150px;"><?php echo $this->options['whitelistURLs']; ?></textarea>
                         <br />
                         <?php _e('List of URLs that are unprotected by this plugin. List each URL on a separate line. ', 'login-configurator'); ?>
                         </td>
                    </tr>
                    <tr align="top">
                         <th scope="row"><?php _e('Ignore the feed URL?', 'login-configurator'); ?></th>
				<td><label>
						<input type="radio" id="login_configurator_feed_no" name="<?php print $this->optionsName; ?>[feed]" value="1" <?php checked( $this->options['feed'], true ); ?> />
						<?php _e( '<strong>Protected</strong> - Protect the feed URL as well.', 'login-configurator' ); ?>
                         </label>
                         <br />
                         <label>
                              <input type="radio" id="login_configurator_feed_yes" name="<?php print $this->optionsName; ?>[feed]" value="0" <?php checked( isset( $this->options['feed'] ), false ); ?> />
						<?php _e( '<strong>Ignore</strong> - Do not protect the feed URL on this blog.', 'login-configurator' ); ?>
                         </label>
                         <br />
                         <label></label></td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'Redirect URL', 'login-configurator' ); ?></th>
                    <td><label>
                              <input type="checkbox" name="<?php print $this->optionsName; ?>[redirect_home]" id="login_configurator_redirect_home" value="1" onclick='onClickRedirectHome(this)' <?php checked( $this->options['redirect_home'], true ); ?>  />
						<?php _e( 'Redirect to the home page', 'login-configurator' ); ?>
                         </label>
                         <br>
                         <input type="text" id="login_configurator_redirect_url" name="<?php print $this->optionsName; ?>[redirect_url]" value="<?php echo $this->options['redirect_url']; ?>" <?php echo ($this->options['redirect_home'] == true) ? 'readonly' : ''; ?> />
                    </td>
               </tr>
          </table>
     </div>
</div>