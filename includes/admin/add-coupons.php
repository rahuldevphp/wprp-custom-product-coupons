<?php
/**
 * Add/Edit Coupon Page
 *
 * @package WP Rahul Prajapati
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;
$coupons_rp_tbl     = WPRP_CPC_COUPONS_TBL;

// Taking some data
$coupon_id 			= ( ! empty( $_GET['coupon_id'] ) && is_numeric($_GET['coupon_id'] ) ) ? intval( $_GET['coupon_id'] ) : '';
$coupon_data		= wprp_get_coupon_details( $coupon_id );

$coupon_title 		= ( !empty( $coupon_data ) && $coupon_data['title'] ) 			? $coupon_data['title'] 		: ( ( isset( $_POST['coupon_title'] ) ) ? $_POST['coupon_title'] : '' );
$coupon_desc 		= ( !empty( $coupon_data ) && $coupon_data['description'] ) 	? $coupon_data['description'] 	: ( ( isset( $_POST['coupon_desc'] ) ) ? $_POST['coupon_desc'] : '' );
$coupon_cat 		= ( !empty( $coupon_data ) && $coupon_data['category'] ) 		? $coupon_data['category'] 		: ( ( isset( $_POST['coupon_cat'] ) ) ? $_POST['coupon_cat'] : '' );
$coupon_amount 		= ( !empty( $coupon_data ) && $coupon_data['coupon_amount'] ) 	? $coupon_data['coupon_amount'] : ( ( isset( $_POST['coupon_amount'] ) ) ? $_POST['coupon_amount'] : '' );
$coupon_avail 		= ( !empty( $coupon_data ) && $coupon_data['availaibility'] ) 	? $coupon_data['availaibility'] :  ( ( isset( $_POST['coupon_avail'] ) ) ? $_POST['coupon_avail'] : '' );
$coupon_image 		= ( !empty( $coupon_data ) && $coupon_data['images'] ) 			? $coupon_data['images'] 		: ( ( isset( $_POST['coupon_image'] ) ) ? $_POST['coupon_image'] : '' );
$coupon_avail  		= ( !empty( $coupon_avail  ) && !is_array( $coupon_avail ) ) 									? explode( ', ', $coupon_avail ) 	: array();


if ( isset( $_POST['action'] ) && $_POST['action'] === 'save_coupon' ) {

	// Verify nonce
	if ( !isset( $_POST['coupon_nonce_field'] ) || !wp_verify_nonce( $_POST['coupon_nonce_field'], 'coupon_nonce') ) {

	    wp_die('Nonce verification failed.');
	}


	$s_coupon_id          = ( isset($_POST['coupon_id']) )        ? intval( $_POST['coupon_id'])                              : '';   
    $s_coupon_title       = ( isset($_POST['coupon_title']) )     ? sanitize_text_field($_POST['coupon_title'])               : '';   
    $s_coupon_desc        = ( isset($_POST['coupon_desc']) )      ? sanitize_textarea_field($_POST['coupon_desc'])            : '';   
    $s_coupon_amount      = ( isset($_POST['coupon_amount']) )    ? sanitize_text_field($_POST['coupon_amount'])              : '';   
    $s_coupon_cat         = ( isset($_POST['coupon_cat']) )       ? sanitize_text_field($_POST['coupon_cat'])                 : '';   
    $s_coupon_avail       = ( isset($_POST['coupon_avail'])  && is_array($_POST['coupon_avail']) )     ? array_map('sanitize_text_field', $_POST['coupon_avail'])  : '';   
    $s_coupon_image       = ( isset($_POST['coupon_image']) )     ? esc_url_raw($_POST['coupon_image'])                       : '';   
    $s_coupon_avail       = ( !empty( $s_coupon_avail ) )           ? implode(", ",$s_coupon_avail)    : '';
    
    $messages            = array();

    if ( empty( $s_coupon_title ) ) {
        $messages[]      = __('Please enter a title.', 'wprp-cpc' );
    }
    if ( empty( $s_coupon_amount ) ) {
        $messages[]      = __('Please enter a coupon amount.', 'wprp-cpc');
    }else if ( !is_numeric( $s_coupon_amount ) ) {
        $messages[]      = __('Please enter a valid ie.10 .', 'wprp-cpc');            
    } 

    // Check if the record exists in the table
    $existing_record =  $wpdb->get_row(
                            $wpdb->prepare(
                                "SELECT * FROM {$coupons_rp_tbl} WHERE id = %s",
                                $s_coupon_id
                            )
                        );
    if ( empty( $messages ) ) {
    	
	    if ( $existing_record ) {

	        // Record exists, perform update
	        $wpdb->update(
	                    $coupons_rp_tbl,
	                    array(
	                        'title'             => $s_coupon_title,
	                        'description'       => $s_coupon_desc,
	                        'coupon_amount'     => $s_coupon_amount,
	                        'category'          => $s_coupon_cat,
	                        'availaibility'     => $s_coupon_avail,
	                        'images'            => $s_coupon_image
	                    ),
	                    array('id' => $existing_record->id)
	                );
	    } else {

	        $query = $wpdb->prepare("
	                    INSERT IGNORE INTO $coupons_rp_tbl ( title, description, coupon_amount, category, availaibility, images )
	                    VALUES (%s, %s, %s, %s, %s, %s)",
	                    $s_coupon_title, $s_coupon_desc, $s_coupon_amount, $s_coupon_cat, $s_coupon_avail, $s_coupon_image );

	        $wpdb->query($query);
	    }
	    
	    $redirect_url = add_query_arg(array(
	        'message' => urlencode('Successfully'),
	        'type' => 'success'
	    ), admin_url('admin.php?page=wprp-coupons'));
	    wp_redirect($redirect_url);
	    exit; 
    }
}

?>
<div class="wrap wpbaw-settings">

	<a href="<?php echo admin_url('admin.php?page=wprp-coupons'); ?>" class="page-title-action"><?php esc_html_e( 'Coupons List', 'wprp-cpc' ); ?></a>
	<h2>
		<?php 
		if ( ! empty( $_GET['action'] ) &&  $_GET['action'] === 'edit' && !empty( $_GET['coupon_id'] ) ) {
			esc_html_e( 'Edit Coupon', 'wprp-cpc' );
		}else{
			esc_html_e( 'Add Coupon', 'wprp-cpc' );
		}
		?>
	</h2>

	<?php if ( !empty( $messages ) ): ?>
	        <?php foreach ($messages as $key => $msg): ?>
		        <div class="notice notice-error is-dismissible">
		            <p><?php echo esc_html($msg); ?></p>
		        </div>        	
	        <?php endforeach ?>	
	<?php endif ?>

	<form method="POST" id="wprp-coupon-form" class="wprp-coupon-form">

		<!-- add coupon section -->
		<div class="metabox-holder">
			<div class="meta-box-sortables">

				<div class="postbox">

					<div class="postbox-header">
						<h3 class="hndle">
							<span>
								<?php 
								if ( ! empty( $_GET['action'] ) &&  $_GET['action'] === 'edit' && !empty( $_GET['coupon_id'] ) ) {
									esc_html_e( 'Edit Coupon', 'wprp-cpc' );
								}else{
									esc_html_e( 'Add Coupon', 'wprp-cpc' );
								}
								?>
								</span>
						</h3>
					</div>

					<div class="inside">
						<table class="form-table">
							<tbody>
								<tr>
                                    <th> (<span class="msg-required">*</span>) <?php echo esc_html__( 'Required fields', 'wprp-cpc' )?></th>
                                    <td>
                                    	<input type="hidden" name="action" value="save_coupon" />
                                    	<input type="hidden" name="coupon_id" value="<?php echo esc_attr($coupon_id); ?>" />
                                    	<?php wp_nonce_field('coupon_nonce', 'coupon_nonce_field'); ?>
                                    </td>
                                </tr>

								<!--title-->
								<tr>
									<th scope="row">
										<label for="wprp-title"><?php esc_html_e( 'Title', 'wprp-cpc' ); ?></label><span class="msg-required">*</span>
									</th>
									<td>
										<input type="text" name="coupon_title" value="<?php echo esc_attr($coupon_title); ?>" id="coupon_title" class="regular-text" /></br>
										<span class="description"><?php esc_html_e( 'Enter Coupon Title', 'wprp-cpc' ); ?></span>
									</td>
								</tr>

								<!--description-->
								<tr>
									<th scope="row">
										<label for="wprp-desc"><?php esc_html_e( 'Description', 'wprp-cpc' ); ?></label>
									</th>
									<td>
										<textarea name="coupon_desc" id="coupon_desc" class="regular-text" ><?php echo esc_attr($coupon_desc); ?></textarea></br>
										<span class="description"><?php esc_html_e( 'Enter Coupon Description', 'wprp-cpc' ); ?></span>
									</td>
								</tr>

								<!--amount-->
								<tr>
									<th scope="row">
										<label for="wprp-amount"><?php esc_html_e( 'Coupon Amount', 'wprp-cpc' ); ?></label><span class="msg-required">*</span>
									</th>
									<td>
										<input type="number" name="coupon_amount" value="<?php echo esc_attr($coupon_amount); ?>" id="coupon_title" class="regular-text" /></br>
										<span class="description"><?php esc_html_e( 'Enter Coupon Amount', 'wprp-cpc' ); ?></span>
									</td>
								</tr>

								<!--cat-->
								<tr>
									<th scope="row">
										<label for="wprp-cat"><?php esc_html_e( 'Category', 'wprp-cpc' ); ?></label>
									</th>
									<td>
										<select name="coupon_cat" class="regular-text">
											<option value=""><?php esc_html_e( 'Select', 'wprp-cpc' ); ?></option>
											<option value="cat-1" <?php selected( 'cat-1', $coupon_cat ); ?>><?php esc_html_e( 'Cat 1', 'wprp-cpc' ); ?></option>
											<option value="cat-2" <?php selected( 'cat-2', $coupon_cat ); ?>><?php esc_html_e( 'Cat 2', 'wprp-cpc' ); ?></option>
											<option value="cat-3" <?php selected( 'cat-3', $coupon_cat ); ?>><?php esc_html_e( 'Cat 3', 'wprp-cpc' ); ?></option>
										</select></br>
										<span class="description"><?php esc_html_e( 'Select Coupon Category', 'wprp-cpc' ); ?></span>
									</td>
								</tr>

								<!--availability-->
								<tr>
									<th scope="row">
										<label for="wprp-avail"><?php esc_html_e( 'Availability', 'wprp-cpc' ); ?></label>
									</th>
									<td>
										<label><input type="checkbox" name="coupon_avail[]" value="client" <?php checked( in_array('client', $coupon_avail ) ); ?>><?php esc_html_e( 'Client', 'wprp-cpc' ); ?></label> 
										<label><input type="checkbox" name="coupon_avail[]" value="distributor" <?php checked( in_array('distributor', $coupon_avail ) ); ?>> <?php esc_html_e( 'Distributor', 'wprp-cpc' ); ?></label><br>
										<span class="description"><?php esc_html_e( 'Check the Coupon Availability', 'wprp-cpc' ); ?></span>
									</td>
								</tr>

								<!--image-->
								<tr>
									<th scope="row">
										<label for="wprp-image"><?php esc_html_e( 'Coupon Image', 'wprp-cpc' ); ?></label>
									</th>
									<td>
										<input type="text" name="coupon_image" value="<?php echo esc_url( $coupon_image ); ?>" id="coupon_image" class="regular-text coupon_image wprp-img-upload-input" />
										<input type="button" name="wprp_default_img" class="button-secondary wprp-image-upload" value="<?php esc_html_e( 'Upload Image', 'wprp-cpc'); ?>" /> 
										<input type="button" name="wprp_default_img_clear" id="wprp-default-img-clear" class="button button-secondary wprp-image-clear" value="<?php esc_html_e( 'Clear', 'wprp-cpc'); ?>" /> <br/>
										<span class="description"><?php esc_html_e( 'Upload coupon image.', 'wprp-cpc' ); ?></span>
										<?php
											$coupon_image_html = '';
											if( $coupon_image ) { 
												$coupon_image_html = '<img src="'.esc_url( $coupon_image ).'" alt="" />';
											}
										?>
										<div class="wprp-img-view"><?php echo $coupon_image_html; ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input type="submit" name="wpbaw_settings_submit" class="button button-primary right" value="<?php esc_html_e( 'Save Changes','wprp-cpc' ); ?>" />
									</td>
								</tr>
							</tbody>
						</table>
					</div><!-- .inside -->
				</div>
			</div>
		</div>
	</form>
</div>