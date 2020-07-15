<?php
/**
 * The plugin uninstall file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.ulisesfreitas.com/
 * @since             1.0.0
 * @package           Uli_Contact_Bar
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// if not allowed to delete plugins go to plugins page
if ( ! current_user_can( 'delete_plugins' ) ) {
	$text = 'Sorry, you are not allowed to delete plugins for this site.';
	wp_die( esc_html__( $text ) );
}



// clean up the database considering multisite installation
if ( is_multisite() ) {

	// get registered site IDs
	$site_ids = array();
	if ( version_compare( get_bloginfo( 'version' ), '4.6', '>=' ) ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			$site_ids[] = $site->id;
		}
	} else {
		$sites = wp_get_sites();
		foreach ( $sites as $site ) {
			$site_ids[] = $site[ 'blog_id' ];
		}
	}

	if ( empty ( $site_ids ) ) return;

	foreach ( $site_ids as $site_id ) {
		// switch to next blog
		switch_to_blog( $site_id );

		// remove settings
		delete_option( 'uli-contact-bar-options' );
	}
	// restore the current blog, after calling switch_to_blog()
	restore_current_blog();
} else {
	// remove settings
	delete_option( 'uli-contact-bar-options' );
}
