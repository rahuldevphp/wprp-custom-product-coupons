<?php 
/**
 * Plugin Name: Custom Product Coupons
 * Description: Custom Product Coupons system
 * Version: 1.0.0
 * Author: Rahul Prajapati
 * Text Domain: wprp-cpc
 * Author URI: https://github.com/rahuldevphp/
 * Plugin URI: https://github.com/rahuldevphp/ 
 * 
 * @package Custom Product Coupons
 * @author Rahul Prajapati 
 * 
 * prefix text "_wp_rp_coupons_"
 */

global $wpdb;

if( ! defined( 'WPRP_CPC_VERSION' ) ) {
	define( 'WPRP_CPC_VERSION', '1.0.0' ); // Version of plugin
}

if( ! defined( 'WPRP_CPC_DIR' ) ) {
	define( 'WPRP_CPC_DIR', dirname( __FILE__ ) ); // Plugin dir
}

if( ! defined( 'WPRP_CPC_URL' ) ) {
	define( 'WPRP_CPC_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}

if( ! defined( 'WPRP_CPC_PLUGIN_BASENAME' ) ) {
	define( 'WPRP_CPC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}

if( ! defined( 'WPRP_CPC_COUPONS_TBL' ) ) {
	define( 'WPRP_CPC_COUPONS_TBL', $wpdb->prefix . 'wprp_coupons' ); // wprp_coupons
}

/**
 * Load Text Domain and do stuff once all plugin is loaded
 * This gets the plugin ready for translation
 * 
 * @package Custom Product Coupons
 * @since 1.0.0
 */
function wp_rp_coupons_load_textdomain() {

	global $wp_version;

	// Set filter for plugin's languages directory
	$wp_rp_coupons_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$wp_rp_coupons_lang_dir = apply_filters( 'wp_rp_coupons_languages_directory', $wp_rp_coupons_lang_dir );

	// Traditional WordPress plugin locale filter.
	$get_locale = get_locale();

	if ( $wp_version >= 4.7 ) {
		$get_locale = get_user_locale();
	}

	// Traditional WordPress plugin locale filter
	$locale = apply_filters( 'plugin_locale',  $get_locale, 'wprp-cpc' );
	$mofile = sprintf( '%1$s-%2$s.mo', 'wprp-cpc', $locale );

	// Setup paths to current locale file
	$mofile_global  = WP_LANG_DIR . '/plugins/' . basename( WPRP_CPC_DIR ) . '/' . $mofile;

	if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/plugin-name folder
		load_textdomain( 'wprp-cpc', $mofile_global );
	} else { // Load the default language files
		load_plugin_textdomain( 'wprp-cpc', false, $wp_rp_coupons_lang_dir );
	}
}

/**
 * Plugins Load functions 
 * 
 * @package Custom Product Coupons
 * @since 1.0.0
 */
function wp_rp_coupons_blog_plugin_loaded() {

	global $pagenow;

	wp_rp_coupons_load_textdomain();	
}

add_action('plugins_loaded', 'wp_rp_coupons_blog_plugin_loaded');

/**
 * Activation Hook
 * 
 * Register plugin activation hook.
 * 
 * @package Custom Product Coupons
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'wp_rp_coupons_install' );

/**
 * Deactivation Hook
 * 
 * Register plugin deactivation hook.
 * 
 * @package Custom Product Coupons
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'wp_rp_coupons_uninstall');

/**
 * Plugin Setup (On Activation)
 * 
 * Does the initial setup,
 * stest default values for the plugin options.
 * 
 * @package Custom Product Coupons
 * @since 1.0.0
 */
function wp_rp_coupons_install() {

	// Create tables
	wprp_coupons_create_tables();
}

/**
 * Plugin Setup (On Deactivation)
 * 
 * Delete  plugin options.
 * 
 * @package Custom Product Coupons
 * @since 1.0.0
 */
function wp_rp_coupons_uninstall() {

	// Uninstall functionality
}

// Functions file
require_once( WPRP_CPC_DIR . '/includes/wp-rp-coupons-functions.php' );

// Script Class
require_once( WPRP_CPC_DIR . '/includes/class-wp-rp-coupons-script.php' );

// Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	// Admin Class
	require_once( WPRP_CPC_DIR . '/includes/admin/class-wp-rp-coupons-admin.php' );
}

?>