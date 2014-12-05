<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description of RT_Asset_Admin_Meta_Boxes
 *
 * @since rt-Assets 0.1
 */

if ( ! class_exists( 'RT_Meta_Box_Assets_Info' ) ) {
	/**
	 * Class RT_Meta_Box_Assets_Info
	 *
	 * @author Dipesh
	 * @since  rt-Assets 0.1
	 */
	class RT_Meta_Box_Assets_Info {

		/**
		 * Metabox Ui for Assets info
		 *
		 * @since rt-Assets 0.1
		 */
		public static function ui( $post ) {

			$rtcamp_users       = get_rtasset_rtcamp_user();
			$arrAssigneeUser[] = array();
			$author             = get_user_by( 'id', $post->post_author );
			if ( ! empty( $rtcamp_users ) ) {
				foreach ( $rtcamp_users as $user ) {
					$arrAssigneeUser[] = array(
						'id' => $user->ID,
						'label' => $user->display_name,
						'imghtml' => get_avatar( $user->user_email, 24 ),
						'user_edit_link' => get_edit_user_link( $user->ID ),
					);
				}
			}

			$arrVendorUser = $arrAssigneeUser;

			$unique_id  = get_post_meta( $post->ID, '_rtbiz_asset_unique_id', true );

			$serial_number  = get_post_meta( $post->ID, '_rtbiz_asset_serial_number', true );
			$invoice_number = get_post_meta( $post->ID, '_rtbiz_asset_invoice_number', true );

			$purchase_date = get_post_meta( $post->ID, '_rtbiz_asset_purchase_date', true );
			$expiry_date   = get_post_meta( $post->ID, '_rtbiz_asset_expiry_date', true );

			$warranty = get_post_meta( $post->ID, '_rtbiz_asset_warranty', true );

			$vendor = get_user_by( 'id', get_post_meta( $post->ID, '_rtbiz_asset_vendor', true ) );

			$purchase_date = empty( $purchase_date ) ? current_time( 'mysql' ) : $purchase_date;
			$expiry_date   = empty( $expiry_date ) ? current_time( 'mysql' ) : $expiry_date;

			$purchase_date = new DateTime( $purchase_date );
			$expiry_date   = new DateTime( $expiry_date );

			$purchase_date = $purchase_date->format( 'M d, Y h:i A' );
			$expiry_date   = $expiry_date->format( 'M d, Y h:i A' );

			?>

			<style type="text/css">
				.hide {
					display: none;
				}
			</style>

			<style type="text/css">
				#minor-publishing-actions, #visibility, .misc-pub-curtime, #post-status-display, .edit-post-status, .save-post-status.hide-if-no-js.button, .cancel-post-status, #misc-publishing-actions label {
					display: none
				}
			</style>
			<input type="hidden" name="rtasset_check_matabox" value="true">


			<div class="row_group">
				<span class="prefix" title="<?php _e( 'Unique ID', RT_ASSET_TEXT_DOMAIN ); ?>"><label><strong><?php _e( 'Unique ID : ', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
			<?php if ( ! empty( $unique_id ) ){ ?>
				<span style="padding: 0px 3px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;"><?php echo esc_attr( $unique_id ); ?></span>
			<?php } else { ?>
				<br/>
				<spna>Note:: it will created after assets created !!</spna>
			<?php } ?>
			</div>
			<div class="row_group">
				<span class="prefix"
				      title="<?php _e( 'Assigned To', RT_ASSET_TEXT_DOMAIN ); ?>"><label><strong><?php _e( 'Assigned To', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input type="text" name="author" id="rt-asset-assignee" class="user-autocomplete"
				       placeholder="Search for User"/>
				<script>
					var arr_assignee_user =<?php echo json_encode( $arrAssigneeUser ); ?>;
				</script>
				<div id="selected_assignee"><?php
			if ( ! empty( $author ) ) { ?>
					<div id="rt-asset-assignee-<?php echo esc_attr( $author->ID ); ?>" class='assignee-list'>
						<?php echo get_avatar( $author->user_email, 25 ) ?>
						<a class='assignee-title heading' target='_blank' href=''><?php echo esc_html( $author->display_name ); ?></a><input type='hidden' name='post_author' value='<?php echo esc_attr( $author->ID ); ?>'/>
						<a href='#removeAssignee' class='delete_row'>X</a>
					</div><?php
			} ?>

				</div>
			</div>

			<div class="row_group">
                    <span class="prefix" title="<?php _e( 'Serial Number', RT_ASSET_TEXT_DOMAIN ); ?>"><label
		                    for="post[rtasset_serial_number]"><strong><?php _e( 'Serial Number', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input type="text" name="post[rtasset_serial_number]" placeholder="Enter Serial number"
				       value="<?php echo esc_attr( ( isset( $serial_number ) ) ? $serial_number : '' ); ?>"
				       title="<?php echo esc_attr( ( isset( $serial_number ) ) ? $serial_number : '' ); ?>">
			</div>

			<div class="row_group">
                    <span class="prefix" title="<?php _e( 'Invoice Number', RT_ASSET_TEXT_DOMAIN ); ?>"><label
		                    for="post[rtasset_invoice_number]"><strong><?php _e( 'Invoice Number', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input type="text" name="post[rtasset_invoice_number]" placeholder="Enter Invoice number"
				       value="<?php echo esc_attr( ( isset( $invoice_number ) ) ? $invoice_number : '' ); ?>"
				       title="<?php echo esc_attr( ( isset( $invoice_number ) ) ? $invoice_number : '' ); ?>">
			</div>

			<div class="row_group">
				<span class="prefix"
				      title="<?php _e( 'Purchase Date', RT_ASSET_TEXT_DOMAIN ); ?>"><label><strong><?php _e( 'Purchase Date ', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input class="datetimepicker moment-from-now" type="text" placeholder="Select Purchase Date"
				       value="<?php echo esc_attr( ( isset( $purchase_date ) ) ? $purchase_date : '' ); ?>"
				       title="<?php echo esc_attr( ( isset( $purchase_date ) ) ? $purchase_date : '' ); ?>"> <input
					name="post[rtasset_purchase_date]" type="hidden"
					value="<?php echo esc_attr( ( isset( $purchase_date ) ) ? $purchase_date : '' ); ?>"/>
			</div>

			<div class="row_group">
				<span class="prefix"
				      title="<?php _e( 'Expiry Date', RT_ASSET_TEXT_DOMAIN ); ?>"><label><strong><?php _e( 'Expiry Date', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input class="datetimepicker moment-from-now" type="text" placeholder="Select Expiry Date"
				       value="<?php echo esc_attr( ( isset( $expiry_date ) ) ? $expiry_date : '' ); ?>"
				       title="<?php echo esc_attr( ( isset( $expiry_date ) ) ? $expiry_date : '' ); ?>"> <input
					name="post[rtasset_expiry_date]" type="hidden"
					value="<?php echo esc_attr( ( isset( $expiry_date ) ) ? $expiry_date : '' ); ?>"/>
			</div>

			<div class="row_group">
                    <span class="prefix" title="<?php _e( 'Warranty Detail', RT_ASSET_TEXT_DOMAIN ); ?>"><label
		                    for="post[rtasset_warranty]"><strong><?php _e( 'Warranty Detail', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input type="text" name="post[rtasset_warranty]" placeholder="Enter Warranty Detail"
				       value="<?php echo esc_attr( ( isset( $warranty ) ) ? $warranty : '' ); ?>"
				       title="<?php echo esc_attr( ( isset( $warranty ) ) ? $warranty : '' ); ?>">
			</div>

			<div class="row_group">
				<span class="prefix"
				      title="<?php _e( 'Vendor', RT_ASSET_TEXT_DOMAIN ); ?>"><label><strong><?php _e( 'Vendor', RT_ASSET_TEXT_DOMAIN ); ?></strong></label></span>
				<input type="text" name="vendor" id="rt-asset-vendor" class="user-autocomplete rt-asset-vendor"
				       placeholder="Search for User"/>
				<script>
					var arr_vendor_user =<?php echo json_encode( $arrVendorUser ); ?>;
				</script>
				<div id="selected_vendor"><?php
			if ( ! empty( $vendor ) ) { ?>
					<div id="rt-asset-vendor-<?php echo esc_attr( $vendor->ID ); ?>" class='vendor-list'>
						<?php echo get_avatar( $author->user_email, 25 ) ?>
						<a class='vendor-title heading' target='_blank' href=''><?php echo esc_html( $vendor->display_name ); ?></a><input type='hidden' name='post[rtasset_vendor]' value='<?php echo esc_attr( $vendor->ID ); ?>'/>
						<a href='#removeVendor' class='delete_row'>X</a>
					</div><?php
			} ?>
				</div>
			</div>

			<?php
			do_action( 'rt_hd_after_ticket_information', $post );

		}

		/**
		 * Save meta box data
		 *
		 * @since rt-Assets 0.1
		 */
		public static function save( $post_id, $post ) {
			global $rt_asset_device_type;

			if ( isset( $_REQUEST['rtasset_check_matabox'] ) && 'true' == $_REQUEST['rtasset_check_matabox'] ) {
				$newAsset = $_POST['post'];
			}

			$newAsset = ( array ) $newAsset;

			//Purchase Date
			$purchase_date = $newAsset['rtasset_purchase_date'];
			if ( isset( $purchase_date ) && $purchase_date != '' ) {
				try {
					$dr                                  = date_create_from_format( 'M d, Y h:i A', $purchase_date );
					$timeStamp                           = $dr->getTimestamp();
					$newAsset['rtasset_purchase_date'] = gmdate( 'Y-m-d H:i:s', ( intval( $timeStamp ) ) );
				} catch ( Exception $e ) {
					$newAsset['rtasset_purchase_date'] = current_time( 'mysql' );
				}
			} else {
				$newAsset['rtasset_purchase_date'] = current_time( 'mysql' );
			}

			//Purchase Date
			$expiry_date = $newAsset['rtasset_expiry_date'];
			if ( isset( $expiry_date ) && $expiry_date != '' ) {
				try {
					$dr                                = date_create_from_format( 'M d, Y h:i A', $expiry_date );
					$timeStamp                         = $dr->getTimestamp();
					$newAsset['rtasset_expiry_date'] = gmdate( 'Y-m-d H:i:s', ( intval( $timeStamp ) ) );
				} catch ( Exception $e ) {
					$newAsset['rtasset_expiry_date'] = current_time( 'mysql' );
				}
			} else {
				$newAsset['rtasset_expiry_date'] = current_time( 'mysql' );
			}

			$metaArray = array(
				'_rtbiz_asset_serial_number' => $newAsset['rtasset_serial_number'],
				'_rtbiz_asset_invoice_number' => $newAsset['rtasset_invoice_number'],
				'_rtbiz_asset_purchase_date' => $newAsset['rtasset_purchase_date'],
				'_rtbiz_asset_expiry_date' => $newAsset['rtasset_expiry_date'],
				'_rtbiz_asset_warranty' => $newAsset['rtasset_warranty'],
				'_rtbiz_asset_vendor' => ! empty ( $newAsset['rtasset_vendor'] ) ? $newAsset['rtasset_vendor'] : '',
			);

			//Unique ID
			if ( ! empty( $_POST['tax_input'][ rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) ][1] )  ){
				$devicetype_id = $_POST['tax_input'][ rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) ] [1];
				$unique_prefix = Rt_Lib_Taxonomy_Metadata\get_term_meta( $devicetype_id, rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) . '_unique_prefix', true );
				$next_id = Rt_Lib_Taxonomy_Metadata\get_term_meta( $devicetype_id, rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) . '_next_id', true );
				$unique_id  = get_post_meta( $post->ID, '_rtbiz_asset_unique_id', true );
				if ( empty( $unique_id ) || ! substr( $unique_id, 0, strlen( $unique_prefix ) ) == $unique_prefix ){
					update_post_meta( $post_id, '_rtbiz_asset_unique_id', $unique_prefix . '_' . $next_id );
					Rt_Lib_Taxonomy_Metadata\update_term_meta( $devicetype_id, rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) . '_next_id', $next_id + 1, $next_id );
				}
			}

			foreach ( $metaArray as $metakey => $metaval ) {
				if ( ! empty( $metaval ) ) {
					update_post_meta( $post_id, $metakey, $metaval );
				}
			}

			//child updated
			if ( in_array( $_POST['post_status'], array( 'asset-unassigned', 'asset-assigned' ) ) ){
				$args = array(
					'post_parent' => $post_id,
					'posts_per_page' => -1,
					'post_type' => RT_Asset_Module::$post_type,
				);
				$childrens = get_children( $args,ARRAY_A );
				foreach ( $childrens as $child ){
					$child_args = array(
						'ID' => $child['ID'],
						'post_status' => $_POST['post_status'],
						'post_author' => $_POST['post_author'],
					);

					wp_update_post( $child_args );
				}
			}
		}

		/**
		 * Render UI for custom post status
		 *
		 * @since rt-Assets 0.1
		 */
		public static function custom_post_status_rendar() {
			global $post, $pagenow, $rt_asset_module;
			$flag = false;
			if ( isset( $post ) && ! empty( $post ) && $post->post_type === RT_Asset_Module::$post_type ) {
				if ( 'edit.php' == $pagenow || 'post-new.php' == $pagenow ) {
					$flag = true;
				}
			}
			if ( isset( $post ) && ! empty( $post ) && 'post.php' == $pagenow && get_post_type( $post->ID ) === RT_Asset_Module::$post_type ) {
				$flag = true;
			}
			if ( $flag ) {
				$option      = '';
				$post_status = $rt_asset_module->get_custom_statuses();
				foreach ( $post_status as $status ) {
					if ( $post->post_status == $status['slug'] ) {
						$complete = " selected='selected'";
					} else {
						$complete = '';
					}
					$option .= "<option value='" . $status['slug'] . "' " . $complete . '>' . $status['name'] . '</option>';
				}

				if ( $post->post_status == 'draft' ) {
					$complete = " selected='selected'";
				} else {
					$complete = '';
				}
				$option .= "<option value='draft' " . $complete . '>Draft</option>';

				echo '<script>
                        jQuery(document).ready(function($) {
                            $("select#post_status").html("' . balanceTags( $option ) . '");
                            $(".inline-edit-status select").html("' . balanceTags( $option ) . '");

                            $(document).on("change","#rthd_post_status",function(){
                                $("#post_status").val($(this).val());
                            });
                               });
                        </script>';
			}
		}
	}
}