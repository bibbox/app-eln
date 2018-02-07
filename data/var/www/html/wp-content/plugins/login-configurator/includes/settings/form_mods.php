<?php
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}
/**
 * options-panel.php - The settings screen.
 *
 * @package Login Configurator
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2013
 * @access public
 * @since 1.6
 */
?>

<div class="postbox">
     <h3 class="handl" style="margin:0;padding:3px;cursor:default;">
		<?php _e( 'Form Modifications', 'login-configurator' ); ?>
     </h3>
     <div class="table">
          <table class="form-table">
               <tr align="top">
                    <th scope="row"><?php _e( 'Text Box Title', 'login-configurator' ); ?></th>
                    <td><input type="text" name="<?php print $this->optionsName; ?>[form_title]" id="login_configurator_form_title" value="<?php print $this->options['form_title']; ?>" /></td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'CSS for Text Box Title', 'login-configurator' ); ?></th>
                    <td><textarea id="login_configurator_form_title_style" name="<?php print $this->optionsName; ?>[form_title_style]" style="width:100%; height:50px;"><?php echo $this->options['form_title_style']; ?></textarea></td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'Text Box Content', 'login-configurator' ); ?></th>
                    <td><textarea id="login_configurator_form_text" name="<?php print $this->optionsName; ?>[form_text]" style="width:100%; height:150px;"><?php echo $this->options['form_text']; ?></textarea></td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'CSS for Text Box Content', 'login-configurator' ); ?></th>
                    <td><textarea id="login_configurator_form_text_style" name="<?php print $this->optionsName; ?>[form_text_style]" style="width:100%; height:50px;"><?php echo $this->options['form_text_style']; ?></textarea></td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'Background Image', 'login-configurator' ); ?></th>
                    <td>
                         <input type="text" id="login_configurator_login_background" name="<?php print $this->optionsName; ?>[login_background]" value="<?php echo $this->options['login_background']; ?>" />
					<?php _e( 'Repeat', 'login-configurator' ); ?>
					<select id="login_configurator_background_repeat" name="<?php print $this->optionsName; ?>[background_repeat]">
						<option value="no-repeat" <?php selected( $this->options['background_repeat'], 'no-repeat' ); ?>><?php _e( 'No Repeat', 'login-configurator' ); ?></option>
						<option value="repeat-x" <?php selected( $this->options['background_repeat'], 'repeat-x' ); ?>><?php _e( 'Repeat X', 'login-configurator' ); ?></option>
						<option value="repeat-y" <?php selected( $this->options['background_repeat'], 'repeat-y' ); ?>><?php _e( 'Repeat Y', 'login-configurator' ); ?></option>
						<option value="repeat" <?php selected( $this->options['background_repeat'], 'repeat' ); ?>><?php _e( 'Repeat Full', 'login-configurator' ); ?></option>
					</select>
					<br>
					<?php _e( 'Leave blank to use the default Wordpress Background.', 'login-configurator' ); ?>
                    </td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'Logo URL', 'login-configurator' ); ?></th>
                    <td>
                         <input type="text" id="login_configurator_logo_url" name="<?php print $this->optionsName; ?>[logo_url]" value="<?php echo $this->options['logo_url']; ?>" />
                         <br>
					<?php _e( 'Leave blank to use the default Wordpress Logo.', 'login-configurator' ); ?>
                    </td>
               </tr>
               <tr align="top">
                    <th scope="row"><?php _e( 'Logo Link', 'login-configurator' ); ?></th>
                    <td><input type="text" id="login_configurator_logo_link" name="<?php print $this->optionsName; ?>[logo_link]" value="<?php echo $this->options['logo_link']; ?>" />
                         <br>
					<?php _e( 'Leave blank to use the default Wordpress site.', 'login-configurator' ); ?>
                    </td>
               </tr>
          </table>
     </div>
</div>