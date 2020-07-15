<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:       Uli Contact Bar
 * Plugin URI:        https://www.ulisesfreitas.com/uli-contact-bar
 * Description:       Let your website visitors get in touch with you easily with permanent seen contact informations
 * Version:           1.0.0
 * Author:            Ulises Freitas
 * Author URI:        https://www.ulisesfreitas.com/
 * Text Domain:       uli-contact-bar
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-uli-contact-bar.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Uli_Contact_Bar', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Uli_Contact_Bar', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Uli_Contact_Bar', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-uli-contact-bar-admin.php' );
	add_action( 'plugins_loaded', array( 'Uli_Contact_Bar_Admin', 'get_instance' ) );

}
