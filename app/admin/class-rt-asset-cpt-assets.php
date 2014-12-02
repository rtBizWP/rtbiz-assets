<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_Asset_CPT_Assets' ) ) {
	/**
	 * Class RT_Asset_CPT_Assets
	 * Customise Assets CPT Add/edit Post view
	 *
	 * @since  0.1
	 *
	 * @author dipesh
	 */
	class RT_Asset_CPT_Assets {

		/**
		 * Apply hook
		 *
		 * @since  0.1
		 */
		function __construct() {

			// CPT Edit/Add View
			add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

			add_action( 'rt_asset_process_' . RT_Asset_Module::$post_type . '_meta', 'RT_Meta_Box_Assets_Info::save', 10, 2 );

			add_action( 'wp_before_admin_bar_render', 'RT_Meta_Box_Assets_Info::custom_post_status_rendar', 10 );
		}


		/**
		 * Remove Default meta boxes on Edit post View for ticket
		 *
		 * @since  0.1
		 */
		public function remove_meta_boxes() {
			remove_meta_box( 'revisionsdiv', RT_Asset_Module::$post_type, 'normal' );
			remove_meta_box( 'slugdiv', RT_Asset_Module::$post_type, 'normal' );
		}

		/**
		 * Add custom meta boxes on Edit post View for ticket
		 *
		 * @since  0.1
		 */
		public function add_meta_boxes() {
			add_meta_box( 'rt-asset-ticket-data', __( 'Asset Information', RT_ASSET_TEXT_DOMAIN ), 'RT_Meta_Box_Assets_Info::ui', RT_Asset_Module::$post_type, 'side', 'default' );

		}

		/**
		 * Save custom meta boxes Values on Edit post View for ticket
		 *
		 * @since  0.1
		 *
		 * @param $post_id
		 * @param $post
		 */
		public function save_meta_boxes( $post_id, $post ) {
			//global $rt_hd_module;
			// $post_id and $post are required
			if ( empty( $post_id ) || empty( $post ) ) {
				return;
			}

			// Dont' save meta boxes for revisions or autosaves
			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
			if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}

			// Check user has permission to edit
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check the post type
			if ( ! in_array( $post->post_type, array( RT_Asset_Module::$post_type ) ) ) {
				return;
			}

			do_action( 'rt_asset_process_' . $post->post_type . '_meta', $post_id, $post );;
			if ( 'trash' == $post->post_status ) {

				$url = add_query_arg( array( 'post_type' => RT_Asset_Module::$post_type ), admin_url( 'edit.php' ) );
				wp_safe_redirect( $url );
				die();
			}

		}
	}
}