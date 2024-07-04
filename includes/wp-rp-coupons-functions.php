<?php
/**
 * Plugin generic functions file
 *
 * @package WP Rahul Prajapati
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to Create Custom Table
 *
 */
function wprp_coupons_create_tables() {

    global $wpdb;

    $coupons_rp_tbl		= WPRP_CPC_COUPONS_TBL;
    
    // Set Variables
    $charset_collate 	= $wpdb->get_charset_collate();

    // Include Files.
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    if ( $wpdb->get_var( "SHOW TABLES LIKE '$coupons_rp_tbl'" ) != $coupons_rp_tbl ) {

        // Table SQL Code.
        $coupons_rp_sql = "CREATE TABLE $coupons_rp_tbl (
            id int(11) NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            coupon_amount VARCHAR(255) NOT NULL,
            category VARCHAR(255) NOT NULL,
            availaibility LONGTEXT NOT NULL,
            images VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY (title)
        ) $charset_collate;";

        // Build Tables.
        dbDelta( $coupons_rp_sql );
    }
}

/**
 * Function to get the coupons details
 * @package WP Rahul Prajapati
 * @since 1.0.0
 */
function wprp_get_coupon_details( $coupons_id ){

    global $wpdb;
    
    if ( empty( $coupons_id ) && !is_numeric( $coupons_id ) ) {
        return;
    }

    $coupons_rp_tbl     = WPRP_CPC_COUPONS_TBL;
    $coupons_result     = $wpdb->get_row( "SELECT * FROM {$coupons_rp_tbl} WHERE id={$coupons_id}",ARRAY_A);

    return $coupons_result;
}

/**
 * Function to get the coupons data
 * @package WP Rahul Prajapati
 * @since 1.0.0
 */
function wprp_get_coupons_data( $args ) {

    global $wpdb;

    // Taking some variables
    $coupons_rp_tbl         = WPRP_CPC_COUPONS_TBL;
    $results                = array();
    $order_by               = 'id';
  
    $limit          = ! empty( $args['limit'] )         ? $args['limit']                                    : $args['per_page'];
    $order_by       = ! empty( $args['order_by'] )      ? $args['order_by']                                 : '';
    $order          = ! empty( $args['order'] )         ? $args['order']                                    : '';
    $search         = ! empty( $args['s'] )             ? $args['s']                                        : '';
  
    if( ! empty( $args['page'] ) ) {
        $page = $args['page'];
    } else if ( ! empty( $_GET['paged'] ) ) {
        $page = $_GET['paged'];
    } else {
        $page = 1;
    }

    // Query Offset
    $offset         = ( ( $page * $limit ) - $limit );

    // Download Log SQL
    $coupons_rp_sql     = "SELECT SQL_CALC_FOUND_ROWS * FROM {$coupons_rp_tbl} WHERE 1=1";

    if( $search ) {
        $coupons_rp_sql .=  wp_ull_get_prepared_search( $search, array(
                                'title',
                            ) );
    }
  
    $order_by = 'id';
    // Check if `orderby` is there
    if( ! empty( $order_by ) ) {
        switch( $order_by ) {
            case 'id' :
                $order_by = 'id';
                break;
            case 'title' :
                $order_by = 'title';
                break;
            case 'coupon_amount' :
                $order_by = 'coupon_amount';
                break;
        }

        $coupons_rp_sql .= " ORDER BY {$order_by} {$order} ";
    }

    // Limit
    if( $limit ) {
        $coupons_rp_sql .= " LIMIT {$offset},{$limit} ";
    }
    // Get download log data
    $coupons_rp_query = $wpdb->get_results( $coupons_rp_sql, ARRAY_A );
    
    if( ! empty( $coupons_rp_query ) ) {

        // Total download log Count
        $total_coupons_rp   = (int) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
    }

    // Assign some data into results array
    $results['data']    = $coupons_rp_query;
    $results['count']   = ! empty( $total_coupons_rp ) ? $total_coupons_rp : 0;

    return $results;
}
?>