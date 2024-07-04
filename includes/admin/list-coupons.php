<?php
/**
* Coupon List Table Page
*
* @package WP Rahul Prajapati
* @since 1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

// Taking some variables
$coupons_rp_tbl         = WPRP_CPC_COUPONS_TBL;
$redirect_url 			= add_query_arg(array(
				                'message' => urlencode('Succesfully.'),
				                'type' => 'success'
				            ), admin_url('admin.php?page=wprp-coupons'));

// delete functionality
if ( isset( $_GET['action'] ) ) {

    switch ( $_GET['action'] ) {

        /* delete action */
        case 'delete':

            $cols_id = array();
            if ( isset( $_REQUEST['id'] ) ) {

                check_admin_referer( 'wprp_col_delete' );
                $cols_id = (array) $_REQUEST['id'];
            } elseif( isset( $_REQUEST['item'] ) )  {
                check_admin_referer( 'bulk-cols' );
                $cols_id = $_REQUEST['item'];
            }

            if ( count( $cols_id ) ) {
                foreach ( $cols_id as $col_id ) {
                    $wpdb->delete( $coupons_rp_tbl,
                        array( 'id' => $col_id ),
                        array( '%d' )
                    );
                }
            }
            wp_redirect($redirect_url);
            exit;
        break;
    }
}

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_RP_Coupons_List_Table extends WP_List_Table {

	var $redirect_url;

	var $sortable_columns 		= array();
	var $actions 				= array();
	var $columns 				= array();
	var $bulk_actions 			= array();
	var $default_sorting_field 	= '';

	function __construct( $args = array() ){

		$args = wp_parse_args( $args, array(
			'singular'  => __( 'Wprp Coupons', 'wprp-cpc' ),
			'plural'    => __( 'Wprp Coupons', 'wprp-cpc' ),
			'ajax'      => false
		) );

		$this->redirect_url		= add_query_arg( array('page' => 'wprp-coupons'), admin_url('admin.php') );

		parent::__construct( $args );		
	}

	function __call( $name, $arguments ) {
		return call_user_func_array( array( $this, $name ), $arguments );
	}

	function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	function column_default( $item, $column_name ) {

		if( isset( $item[ $column_name ] ) ) {
			return $item[ $column_name ];
		} else {
			return '';
		}
	}

	function set_sortable_columns( $args = array() ) {

		$return_args = array();

		foreach( $args as $k=>$val ) {

			if( is_numeric( $k ) ) {
				$return_args[ $val ] 	= array( $val, $val == $this->default_sorting_field );
			} else if( is_string( $k ) ) {
				$return_args[ $k ] 		= array( $val, $k == $this->default_sorting_field );
			} else {
				continue;
			}
		}

		$this->sortable_columns 	= $return_args;

		return $this;
	}

	function get_sortable_columns() {

		return $this->sortable_columns;
	}

	function set_columns( $args = array() ) {

		if( count( $this->bulk_actions ) ) {
			$args = array_merge( array( 'cb' => '<input type="checkbox" />' ), $args );
		}

		$this->columns = $args;

		return $this;
	}

    function column_cb( $item ) {
        return '<input type="checkbox" name="item[]" value="' . $item['id'] . '" />';
    }

	function get_columns() {

		return $this->columns;
	}

	function set_actions( $args = array() ) {

		$this->actions = $args;
		return $this;
	}

	function get_actions() {

		return $this->actions;
	}

	function set_bulk_actions( $args = array() ) {

		$this->bulk_actions 	= $args;
		return $this;
	}

	function get_bulk_actions() {

		return $this->bulk_actions;
	}

	function column_id( $item ) {

		$actions['edit'] = '<a href="admin.php?page=wprp-add-coupon&action=edit&coupon_id='
            . $item['id'] . '&_wpnonce=' . wp_create_nonce( 'wprp_col_edit'
            . $item['id'] . get_current_user_id() ) . '">' . __( 'Edit', 'wprp-cpc' ) . '</a>';

        $delete_coupon 	= add_query_arg( array( 'action' => 'delete', 'id' => $item['id'], '_wpnonce' => wp_create_nonce('wprp_col_delete') ), $this->redirect_url );

        $actions['delete']	= sprintf( '<a onclick=\'return confirm("'
            . __( 'Are you sure to delete this Item?', 'wprp-cpc' )
            . '");\' href="%s">'.esc_html__('Delete', 'wprp-cpc').'</a>', esc_url( $delete_coupon ) );

        return sprintf('%1$s %2$s', $item['id'] , $this->row_actions( $actions ) );
	}

	function column_title( $item ) {

		return $item['title'];
	}

	function column_coupon_amount( $item ) {

		return $item['coupon_amount'];
	}

	function column_category( $item ) {

		return $item['category'];
	}

	function column_availaibility( $item ) {

		return $item['availaibility'];
	}

	function column_images( $item ) {
		
		$item_html = '';
		if ( ! empty( $item['images'] ) ) {

			$item_html = '<img src="'.esc_url($item['images']).'" width="100px" height="100px"/>';
		}

		return $item_html;
	}
	
	/**
     * Generate the table navigation above or below the table
     */
    function display_tablenav( $which ) {
    	
        if ( 'top' == $which || 'bottom' == $which )
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions bulkactions">
                <?php $this->bulk_actions(); ?>
            </div>
        <?php
            $this->pagination( $which );
        ?>            
        </div>
    <?php
    }

	function wpc_set_pagination_args( $attr = array() ) {
		$this->set_pagination_args( $attr );
	}
}

//order
$order_by = 'id';
if ( isset( $_GET['orderby'] ) ) {

	switch( $_GET['orderby'] ) {
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
}

$order 		= ( isset( $_GET['order'] ) && 'asc' ==  strtolower( $_GET['order'] ) ) ? 'ASC' : 'DESC';
$search_key = ! empty( $_GET['s'] )				? $_GET['s']			: '';

$ListTable 	= new WP_RP_Coupons_List_Table( array(
    'singular'  => 'Col',
    'plural'    => 'Cols',
    'ajax'      => false
));

$paged		= $ListTable->get_pagenum();
$per_page	= 2;


$ListTable->set_sortable_columns( array(
	'id'				=> 'id',
	'title'				=> 'title',
	'coupon_amount'		=> 'coupon_amount'
) );

$ListTable->set_bulk_actions( array(
    'delete'        => __( 'Delete', 'wprp-cpc' ),
));

// Set Columns
$ListTable->set_columns(array(
	'id'				=> __( 'No', 'wprp-cpc' ),
	'images'			=> __( 'Image', 'wprp-cpc' ),
	'title'				=> __( 'Title', 'wprp-cpc' ),
	'coupon_amount'		=> __( 'Coupon Amount', 'wprp-cpc' ),
	'category'			=> __( 'Category', 'wprp-cpc' ),
	'availaibility'		=> __( 'Availaibility', 'wprp-cpc' ),
));

// Assign some values into `coupon_args` array
$coupon_args 	= array(
				'per_page'		=> $per_page,
				'paged'			=> $paged,
				'order_by'		=> $order_by,
				'order'			=> $order,				
				's'				=> $search_key,
			);

// Get coupon data
$get_coupon_lists	= wprp_get_coupons_data( $coupon_args );
$get_coupon_data	= ! empty( $get_coupon_lists['data'] )	? $get_coupon_lists['data']		: array();
$items_count		= ! empty( $get_coupon_lists['count'] )	? $get_coupon_lists['count']	: 0;

$ListTable->prepare_items();
$ListTable->items 	= $get_coupon_data;
$ListTable->wpc_set_pagination_args( array( 'total_items' => $items_count, 'per_page' => $per_page ) );
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Coupons List', 'wprp-cpc' ); ?></h1>
	<a href="<?php echo admin_url('admin.php?page=wprp-add-coupon'); ?>" class="page-title-action"><?php esc_html_e( 'Add Coupon', 'wprp-cpc' ); ?></a>
	
	<?php
    // Display validation message if set
    if (isset($_GET['message'])) {
    	
        $type = isset($_GET['type']) ? $_GET['type'] : 'info';
        ?>
        <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
            <p><?php echo esc_html($_GET['message']); ?></p>
        </div>
        <?php
    }

    ?>
	<div id="wprp_container">
		<div class="wprp_container_block">
			<form action="" method="get" name="wprp_coupon_form" id="wprp_coupon_form" style="width: 100%;">
				<input type="hidden" name="page" value="wprp-coupons" />
				<?php $ListTable->search_box( esc_html__( 'Search', 'wprp-cpc' ), 'wprp-cpc' ); ?>
				<?php $ListTable->display(); ?>
			</form>
		</div>	
	</div>
</div>