<?php
/**
Plugin Name: Taro CPT Front
Description: Custom post types can have a front page.
Plugin URI: https://wordpress.org/plugins/taro-cpt-front/
Author: Tarosky INC.
Version: nightly
Author URI: https://tarosky.co.jp/
License: GPL3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: tscptf
Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

/**
 * Initializer.
 */
function ts_cptf_init() {
	// Load text domain.
	load_plugin_textdomain( 'tscptf', false, basename( __DIR__ ) . '/languages' );
	// Initialize.
	require_once __DIR__ . '/includes/functions.php';
	require_once __DIR__ . '/includes/settings.php';
}
add_action( 'plugin_loaded', 'ts_cptf_init' );

/**
 * Get plugin version.
 *
 * @return string
 */
function ts_cptf_version() {
	static $version = '';
	if ( $version ) {
		return $version;
	}
	$info    = get_file_data( __FILE__, [
		'version' => 'Version',
	] );
	$version = $info['version'];
	return $version;
}

/**
 * Get plugin root.
 *
 * @return string
 */
function ts_cptf_root() {
	return __DIR__;
}
