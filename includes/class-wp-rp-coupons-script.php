<?php
/**
 * Script Class
 *
 * Handles the script and style functionality of plugin
 *
 * @package WP Rahul Prajapati
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Wp_Rp_Script {

	function __construct() {

		// Action to add style in backend
		add_action( 'admin_enqueue_scripts', array( $this, 'wprp_admin_style_script' ) );
	}

	/**
	 * Enqueue admin styles & script
	 * 
	 * @package WP Rahul Prajapati
 	 * @since 1.0.0
	 */
	function wprp_admin_style_script( $hook ) {
		
		/* Styles */
		// Registring admin css
		wp_register_style( 'wprp-coupons-admin-css', WPRP_CPC_URL.'assets/css/wprp-coupons-admin.css', array(), WPRP_CPC_VERSION );

		/* Scripts */
		// Registring admin script
		wp_register_script( 'wprp-coupons-admin-js', WPRP_CPC_URL.'assets/js/wprp-coupons-admin.js', array( 'jquery' ), WPRP_CPC_VERSION, true );

		wp_localize_script( 'wprp-coupons-admin-js', 'Wcf_Aio_Admin', array( 
																'ajaxurl'	 	=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )
															) );
		wp_enqueue_media(); // For media uploader
		wp_enqueue_style( 'wprp-coupons-admin-css' );
		wp_enqueue_script( 'wprp-coupons-admin-js' );
	}
	
}

$wprp_script = new Wp_Rp_Script();