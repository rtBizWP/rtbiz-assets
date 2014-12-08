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

if ( ! class_exists( 'RT_Meta_Box_Assets_Assignee' ) ) {
	/**
	 * Class RT_Meta_Box_Assets_Assignee
	 *
	 * @author Dipesh
	 * @since  rt-Assets 0.1
	 */
	class RT_Meta_Box_Assets_Assignee {

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

			<?php
			do_action( 'rt_asset_after_assets_assignee', $post );

		}

		/**
		 * Save meta box data
		 *
		 * @since rt-Assets 0.1
		 */
		public static function save( $post_id, $post ) {
			if ( ! in_array( get_post_type( $post_id ), array( RT_Asset_Bundle_Module::$post_type, RT_Asset_Module::$post_type ) ) ){
				return;
			}
		}
	}
}