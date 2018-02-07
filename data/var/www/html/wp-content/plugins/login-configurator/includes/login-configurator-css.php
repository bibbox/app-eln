<?php
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * login-configurator-css.php - Creates the styles needed for the login form.
 *
 * @package Login Configurator
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2013
 * @access public
 * @since 1.6
 */
?>

<style>
     /* Styles added by "<?php echo $this->pluginName; ?>" Plugin */

	<?php if ( 1 || $this->options['login_background'] ) : ?>
		body.login {background: url(<?php echo $this->options['login_background']; ?>) left top <?php print $this->options['background_repeat']; ?>;height: auto;}
	<?php endif; ?>

	<?php if ( $this->options['logo_url'] ) : ?>
		h1 a, .login h1 a {background: url(<?php echo $this->options['logo_url']; ?>) no-repeat center;}
	<?php endif; ?>

	<?php if ( $this->options['form_title'] ) : ?>
		.lc_form_title {<?php print $this->options['form_title_style']; ?>}
	<?php endif; ?>

	<?php if ( $this->options['form_text'] ) : ?>
		.lc_form_text {<?php print $this->options['form_text_style']; ?>}
	<?php endif; ?>

</style>