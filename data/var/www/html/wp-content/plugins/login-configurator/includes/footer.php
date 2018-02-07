<?php
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}
/**
 * footer.php - View for the footer on all plugin pages.
 *
 * @package Login Configurator
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2013
 * @access public
 * @since 1.6
 */
?>
<div  style="width:49%; float:left">
	<div class="postbox">
		<h3 class="handl" style="margin:0; padding:3px;cursor:default;"><?php _e( 'Credits', 'login-configurator' ); ?></h3>
		<div style="padding:8px;">
			<p>
				<?php
				printf( __( 'Thank you for trying the %1$s plugin - I hope you find it useful. For the latest updates on this plugin, vist the %2$s. If you have problems with this plugin, please use our %3$s or check out the %4$s.', 'login-configurator' ),
					$this->pluginName, '<a href="http://grandslambert.tk/plugins/login-configurator.html" target="_blank">' . __( 'official site', 'login-configurator' ) . '</a>',
					'<a href="http://grandslambert.tk/support/forum/login-configurator" target="_blank">' . __( 'Support Forum', 'login-configurator' ) . '</a>',
					'<a href="http://grandslambert.tk/documentation/login-configurator.html" target="_blank">' . __( 'Documentation Page', 'login-configurator' ) . '</a>'
				);
				?>
			</p>
			<p>
				<?php
				printf( __( 'This plugin is &copy; %1$s by %2$s and is released under the %3$s', 'login-configurator' ), '2009-' . date( "Y" ), '<a href="http://grandslambert.tk" target="_blank">GrandSlambert Toolkit</a>', '<a href="http://www.gnu.org/licenses/gpl.html" target="_blank">' . __( 'GNU General Public License', 'login-configurator' ) . '</a>'
				);
				?>
			</p>
		</div>
	</div>
</div>
<div  style="width:49%; float:right">
	<div class="postbox">
		<h3 class="handl" style="margin:0; padding:3px;cursor:default;"><?php _e( 'Donate', 'login-configurator' ); ?></h3>
		<div style="padding:8px">
			<p>
				<?php printf( __( 'If you find this plugin useful, please consider supporting this and our other great %1$s.', 'login-configurator' ), '<a href="http://grandslambert.tk/" target="_blank">' . __( 'plugins', 'login-configurator' ) . '</a>' ); ?>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VT4XWVWWQT9G4" target="_blank"><?php _e( 'Donate a few bucks!', 'login-configurator' ); ?></a>
			</p>
			<p style="text-align: center;"><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VT4XWVWWQT9G4"><img width="92" height="26" alt="paypal_btn_donateCC_LG" src="http://cdn.grandslambert.com/assets/btn_donate_LG.gif" title="paypal_btn_donateCC_LG" class="aligncenter size-full wp-image-174"/></a></p>
		</div>
	</div>
</div>
