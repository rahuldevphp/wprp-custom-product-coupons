<?php
/**
 * Admin Class
 *
 * Handles the Admin side functionality of plugin
 * 
 * @package WP Rahul Prajapati
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Wp_Rp_Admin {

	function __construct() {
		
		// Action to add product admin menu
		add_action( 'admin_menu', array( $this, 'wprp_register_menu' ) );
	}

	/**
	 * Function to add menu
	 * 
	 */
	function wprp_register_menu() {

		// Product main menu
		add_menu_page(
	        'Coupons',
	        __('Product','wprp-cpc'),
	        'manage_options',
	        'wprp-coupons',
	        array( $this, 'wprp_coupons_list_page' ),
	        'dashicons-tickets-alt',
	        30
	    );

	    add_submenu_page( 
				    	'wprp-coupons',
				    	__('Coupons','wprp-cpc'),
				    	__('Coupons','wprp-cpc'),
				    	'manage_options',
				    	'wprp-coupons',
				    	array( $this, 'wprp_coupons_list_page')
				    );

	    // sub menu import and export labels data in `Greensheeps`
	    add_submenu_page( 
				    	'wprp-coupons',
				    	__('Add Coupon','wprp-cpc'),
				    	__('Add Coupon','wprp-cpc'),
				    	'manage_options',
				    	'wprp-add-coupon',
				    	array( $this, 'wprp_coupons_add_page')
				    );
	}

	/**
	 * Function to display coupons list
	 * 
	 * @since 1.0.0
	 */
	function wprp_coupons_list_page() {
		include_once( WPRP_CPC_DIR . '/includes/admin/list-coupons.php' );
	}

	/**
	 * Function to display add coupon
	 * 
	 * @since 1.0.0
	 */
	function wprp_coupons_add_page() {
		include_once( WPRP_CPC_DIR . '/includes/admin/add-coupons.php' );
	}

}

$wp_rp_admin = new Wp_Rp_Admin();