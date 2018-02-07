<?php
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}
/**
 * settings.php - View for the Settings page.
 *
 * @package Login Configurator
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2013
 * @access public
 * @since 1.6
 */
/* Flush the rewrite rules */
global $wp_rewrite, $wp_query;
$wp_rewrite->flush_rules();

if ( isset( $_REQUEST['tab'] ) ) {
	$selectedTab = $_REQUEST['tab'];
} else {
	$selectedTab = 'options';
}

$tabs = array(
	'options' => __( 'Plugin Settings', 'login-configurator' ),
	'form_mods' => __( 'Form Modifications', 'login-configurator' ),
	'administration' => __( 'Administration', 'login-configurator' ),
);
?>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;" class="overDiv"></div>
<div class="wrap">
     <form method="post" action="options.php" id="login_configurator_settings">
          <input type="hidden" id="home_page_url" value ="<?php echo site_url(); ?>" />
          <div class="icon32" id="icon-login-configurator"><br/></div>
          <h2><?php echo $this->pluginName; ?> &raquo; <?php _e( 'Plugin Settings', 'login-configurator' ); ?> </h2>
		<?php if ( isset( $_REQUEST['reset'] ) ) : ?>
			<div id="settings-error-login-configurator_upated" class="updated settings-error">
				<p><strong><?php _e( 'Index Press settings have been reset to defaults.', 'login-configurator' ); ?></strong></p>
			</div>
		<?php endif; ?>
		<?php settings_fields( $this->optionsName ); ?>
		<input type="hidden" name="<?php echo $this->optionsName; ?>[random-value]" value="<?php echo rand( 1000, 100000 ); ?>" />
		<input type="hidden" name="active_tab" id="active_tab" value="<?php echo $selectedTab; ?>" />
		<ul id="login_configurator_tabs">
			<?php foreach ( $tabs as $tab => $name ) : ?>
				<li id="login_configurator_<?php echo $tab; ?>" class="login-configurator<?php echo ($selectedTab == $tab) ? '-selected' : ''; ?>" style="display: <?php echo ($tab == 'taxonomies' && !$this->options['use-taxonomies']) ? 'none' : 'block'; ?>">
					<a href="#top" onclick="login_configurator_show_tab('<?php echo $tab; ?>')"><?php echo $name; ?></a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div style="width:49%; float:left">
			<?php foreach ( $tabs as $tab => $name ) : ?>
				<div id="login_configurator_box_<?php echo $tab; ?>" style="display: <?php echo ($selectedTab == $tab) ? 'block' : 'none'; ?>">
					<?php require_once('settings/' . $tab . '.php'); ?>
				</div>
			<?php endforeach; ?>

		</div>

		<div  style="width:49%; float:right">
			<?php require_once('settings/right-rail.php'); ?>
		</div>

		<div style="clear: both;">
			<p class="submit" align="center">
				<input type="hidden" name="action" value="update" />
				<?php if ( function_exists( 'wpmu_create_blog' ) ) : ?>
					<input type="hidden" name="option_page" value="<?php echo $this->optionsName; ?>" />
				<?php else : ?>
					<input type="hidden" name="page_options" value="<?php echo $this->optionsName; ?>" />
				<?php endif; ?>
				<input type="submit" name="Submit" class="login-configurator-save" value="<?php _e( 'Save Settings', 'login-configurator' ); ?>" />
               </p>
          </div>

	<?php require_once('footer.php'); ?>
     </form>
</div>
