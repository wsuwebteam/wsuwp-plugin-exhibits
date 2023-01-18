<?php
/**
 * Plugin Name: WSUWP Plugin Exhibits
 * Plugin URI: https://github.com/wsuwebteam/wsuwp-plugin-exhibits
 * Description: Adds a custom post type for creating museum exhibits.
 * Version: 1.0.3
 * Requires PHP: 7.3
 * Author: Washington State University, Dan White
 * Author URI: https://web.wsu.edu/
 * Text Domain: wsuwp-plugin-exhibits
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'after_setup_theme', 'wsuwp_plugin_exhibits_init' );

function wsuwp_plugin_exhibits_init() {

	if ( defined( 'ISWDS' ) ) {

		// Initiate plugin
		require_once __DIR__ . '/includes/plugin.php';

	}

}
