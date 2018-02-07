<?php
/*
  Plugin Name: Login Configurator
  Plugin URI: http://grandslambert.tk/plugins/login-configurator.html
  Description: Change the way your login functions work including forcing users to log in, changing the URL they go to when the login is successful, adding text to the login form, and change the logo and link on the login form.
  Version: 2.0
  Author: GrandSlambert
  Author URI: http://www.grandslambert.tk/

 * *************************************************************************

  Copyright (C) 2009-2013 GrandSlambert

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * *************************************************************************

 */

class gsLoginConfigurator {

	var $version = '2.0';

	/* Plugin Settings */
	var $optionsName = "login_configurator_options";
	var $menuName = "login-configurator-options";
	var $pluginName = 'Login Configurator';
	var $options = array( );
	var $make_link = false;

	/**
	 * Plugin Constructor. Adds actions and filters
	 */
	function gsLoginConfigurator() {
		/* Load Langauge Files */
		$langDir = dirname( plugin_basename( __FILE__ ) ) . '/lang';
		load_plugin_textdomain( 'login-configurator', false, $langDir, $langDir );

		/* Plugin paths */
		$this->pluginPath = WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) );
		$this->pluginURL = WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) );

		/* Load Options */
		$this->load_options();

		/* WordPress Actions */
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'login_head', array( $this, 'login_head' ) );
		add_action( 'update_option_' . $this->optionsName, array( &$this, 'update_option' ), 10 );

		/* WordPress Filters */
		add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );

		/* Login form filters */
		if ( $this->options['logo_link'] ) {
			add_filter( 'login_headerurl', array( $this, 'get_logo_link' ) );
		}
	}

	/**
	 * Load Options
	 */
	function load_options() {
		$defaults = array(
			'force' => false,
			'feed' => false,
			'redirect_url' => '',
			'redirect_home' => '',
			'form_title' => '',
			'form_title_style' => '',
			'form_text' => '',
			'form_text_style' => '',
			'login_background' => '',
			'background_repeat' => 'repeat',
			'logo_url' => '',
			'logo_link' => false,
			'form_title_style' => 'font-size: 18px; font-weight: bold;',
			'form_text_style' => 'margin: 5px 0 10px !important; border: solid 1px #666666; padding: 5px;'
		);

		$this->options = wp_parse_args( get_option( $this->optionsName ), $defaults );
	}

	/**
	 * Load the styles for the login form.
	 */
	function login_head() {
		include $this->pluginPath . '/includes/login-configurator-css.php';
	}

	/**
	 * Filter to change the link for the logo on the login page
	 */
	function get_logo_link() {
		return $this->options['logo_link'];
	}

	/**
	 * Sets the redirect for the login form and adds any extra text.
	 *
	 * If a redirect is present in the URL it will not override that redirect.
	 * Text will be added below the password field and above the Login button.
	 */
	function lc_login_form() {
		global $redirect_to;

		if ( $this->options['redirect_url'] and !isset( $_GET['redirect_to'] ) ) {
			$redirect_to = $this->options['redirect_url'];
		}

		if ( $this->options['form_title'] ) {
			printf( '<h3 class="lc_form_title">%1s</h3>', $this->options['form_title'] );
		}

		if ( $this->options['form_text'] ) {
			printf( '<p class="lc_form_text">%1s</p>', $this->options['form_text'] );
		}
	}

	/**
	 * Check if login is forced.
	 *
	 * If the login is forced, it will send users to the login screen based on the
	 * rules you have set in the admin panel for this plugin.
	 */
	function lc_force_login() {
		/*
		  Skip the check if a user is logged in or on a login page
		  Note: We don't need to check if they are on an admin page because they
		  would have to be logged in, so is_user_logged_in() covers that.
		 */
		if ( is_user_logged_in() or preg_match( '/login/', $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		/*
		  Check if we should protected the feed
		 */
		if ( is_feed() and $this->options['feed'] != true ) {
			return;
		}

		/*
		  Build the URL whitelist array
		 */
		$whitelisturls = array( );
		$urls = preg_split( "/[\n,;]+/", $this->options['whitelistURLs'], NULL, PREG_SPLIT_NO_EMPTY );
		$site_url = get_option( 'siteurl' );
		foreach ( $urls as $url ) {
			$url = preg_replace( "/[\s,;]+/", '', $url );
			$url = str_replace( $site_url, '', $url );
			array_push( $whitelisturls, $url );
		}

		// Redirect to login based on the rules set in the admin
		if (
			(
			$this->options['force'] == 'all'
			or ($this->options['force'] == 'inside' and is_front_page() == false )
			or ($this->options['force'] == 'posts' and is_single() == true )
			)
			and (!in_array( $_SERVER['REQUEST_URI'], $whitelisturls ) )
		) {
			if ( function_exists( wp_login_url ) )
				wp_safe_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
			else {
				$url = get_option( 'siteurl' ) . '/wp-login.php?redirect_to=' . $_SERVER['REQUEST_URI'];
				wp_safe_redirect( $url );
			}
		}
	}

	/**
	 * Outputs the options sub panel
	 */
	function options_panel() {
		// Include options panel
		include($this->pluginPath . "/includes/settings.php");
	}

	/**
	 * Adds Disclaimer options tab to admin menu
	 */
	function admin_menu() {
		global $wp_version;
		$page = add_options_page( $this->pluginName . ' Options', $this->pluginName, 'manage_options', $this->menuName, array( &$this, "options_panel" ) );

		add_action( 'admin_print_styles-' . $page, array( &$this, 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'admin_print_scripts' ) );
	}

	/**
	 *  Set up admin side stuff
	 */
	function admin_init() {
		register_setting( $this->optionsName, $this->optionsName );
		wp_register_style( 'login_configurator-admin-css', $this->pluginURL . '/includes/login-configurator-admin.css' );
		wp_register_script( 'login-configurator-js', $this->pluginURL . '/js/login-configurator.js' );
	}

	/**
	 * Print the administration styles.
	 */
	function admin_print_styles() {
		wp_enqueue_style( 'login_configurator-admin-css' );
	}

	/**
	 * Print the scripts needed for the admin.
	 */
	function admin_print_scripts() {
		wp_enqueue_script( 'login-configurator-js' );
	}

	/**
	 * Adds a settings link next to Login Configurator on the plugins page
	 */
	function plugin_action_links( $links, $file ) {
		static $this_plugin;

		if ( !$this_plugin ) {
			$this_plugin = plugin_basename( __FILE__ );
		}

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . get_option( 'siteurl' ) . '/wp-admin/options-general.php?page=' . $this->menuName . '">' . __( 'Settings', 'login-configurator' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Check on update option to see if we need to reset the options.
	 * @param <array> $input
	 * @return <boolean>
	 */
	function update_option( $input ) {
		if ( $_REQUEST['confirm-reset-options'] ) {
			delete_option( $this->optionsName );
			wp_redirect( admin_url( 'options-general.php?page=' . $this->menuName . '&tab=' . $_POST['active_tab'] . '&reset=true' ) );
			exit();
		} else {
			wp_redirect( admin_url( 'options-general.php?page=' . $this->menuName . '&tab=' . $_POST['active_tab'] . '&updated=true' ) );
			exit();
		}
	}

	/**
	 * Display the list of contributors.
	 * @return boolean
	 */
	function contributor_list() {

		if ( function_exists( 'simplexml_load_file' ) ) {
			$this->showFields = array( 'NAME', 'LOCATION', 'COUNTRY' );
			print '<ul>';

			$items = simplexml_load_file( 'http://cdn.grandslambert.com/xml/login-configurator.xml' );

			foreach ( $items as $item ) {
				print '<li>';
				if ( empty( $item->url ) ) {
					print $item->name;
				} else {
					print '<a href="' . $item->url . '" target="_blank">' . $item->name . '</a>';
				}

				if ( !empty( $item->location ) ) {
					print ' from ' . $item->location;
				}

				if ( !empty( $item->country ) ) {
					print ', ' . $item->country;
				}

				print ' contributed ' . $item->item . ' on ' . date( 'F jS, Y', strtotime( $item->date ) ) . '</li>';
			}
			print '</ul>';
		} else {
			_e( 'PHP 5 Required to see a list of contributors.', 'pretty-sidebar-categories' );
		}
	}

	function activate() {

		/* Compile old options into new options Array */
		$options = array( 'form_text', 'redirect_home', 'redirect_url', 'feed', 'force', 'logo_url', 'logo_link', 'whitelistURLs' );

		foreach ( $options as $option ) {
			if ( $old_option = get_option( 'login_configurator_' . $option ) ) {
				$this->options[$option] = $old_option;
				delete_option( 'login_configurator_' . $option );
			}
		}
		add_option( $this->optionsName, $this->options );
	}

}

// Pre 2.6 Compatibility
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

$GSLC = new gsLoginConfigurator;
add_action( 'login_form', array( $GSLC, 'lc_login_form' ) );
add_action( 'wp', array( $GSLC, 'lc_force_login' ) );
register_activation_hook( __FILE__, array( $GSLC, 'activate' ) );
?>