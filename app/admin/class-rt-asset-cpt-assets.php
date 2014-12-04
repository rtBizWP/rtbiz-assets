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
	 * @since  rt-Assets 0.1
	 *
	 * @author dipesh
	 */
	class RT_Asset_CPT_Assets {

		/**
		 * Apply hook
		 *
		 * @since  rt-Assets 0.1
		 */
		function __construct() {

			// CPT List View
			add_filter( 'manage_edit-' . RT_Asset_Module::$post_type . '_columns', array( $this, 'edit_custom_columns' ) );
			add_filter( 'manage_edit-' . RT_Asset_Module::$post_type . '_sortable_columns', array( $this, 'sortable_column' ) );
			add_action( 'manage_' . RT_Asset_Module::$post_type . '_posts_custom_column', array( $this, 'manage_custom_columns' ), 2 );

			// CPT Edit/Add View
			add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

			add_action( 'rt_asset_process_' . RT_Asset_Module::$post_type . '_meta', 'RT_Meta_Box_Assets_Info::save', 10, 2 );

			add_action( 'wp_before_admin_bar_render', 'RT_Meta_Box_Assets_Info::custom_post_status_rendar', 10 );
		}

		/**
		 * Edit Column list view on Assets List view page
		 *
		 * @param $cols
		 *
		 * @since  rt-Assets 0.1
		 *
		 * @return array
		 */
		public function edit_custom_columns( $cols ) {
			global $rt_asset_device_type;
			$columns = array();

			$columns['cb']                                                                         = $cols['cb'];
			$columns['rtasset_asset_id']                                                           = '<span class="assetid_head tips" data-tip="' . esc_attr__( 'Unique ID', RT_ASSET_TEXT_DOMAIN ) . '">' . esc_attr__( 'ID', RT_ASSET_TEXT_DOMAIN ) . '</span>';
			$columns['title']                                                                      = $cols['title'];
			$columns['rtasset_asset_status']                                                       = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', RT_ASSET_TEXT_DOMAIN ) . '">' . esc_attr__( 'Status', RT_ASSET_TEXT_DOMAIN ) . '</span>';
			$columns[ 'taxonomy-' . rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) ] = $cols[ 'taxonomy-' . rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) ];
			$columns['comments']                                                                   = $cols['comments'];
			$columns['date']                                                                       = $cols['date'];

			unset( $cols['cb'] );
			unset( $cols['title'] );
			unset( $cols['comments'] );
			unset( $cols['date'] );
			unset( $cols[ 'taxonomy-' . rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) ] );

			$columns = array_merge( $columns, $cols );

			return $columns;
		}

		/**
		 * Define new sortable columns for Assets list view
		 *
		 * @since 0.1
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function sortable_column( $columns ) {
			global $rt_asset_device_type;
			$columns['rtasset_asset_status']                                                         = 'post_status';
			$columns[ 'taxonomy-' . rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) ] = rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug );
			$columns['rtasset_asset_id']                	                                         = 'post_id';
			return $columns;
		}

		/**
		 * Edit Content of List view Columns
		 *
		 * @since  0.1
		 *
		 * @param $column
		 */
		function manage_custom_columns( $column ) {

			global $post, $rt_asset_module;

			switch ( $column ) {

				case 'rtasset_asset_status' :
					$post_status_list = $rt_asset_module->get_custom_statuses();
					$post_status      = $post->post_status;
					$style            = 'padding: 5px; border: 1px solid black; border-radius: 5px;';
					$flag             = false;
					foreach ( $post_status_list as $status ) {
						if ( $status['slug'] == $post->post_status ) {
							$post_status = $status['name'];
							if ( ! empty( $status['style'] ) ) {
								$style = $status['style'];
							}
							$flag = true;
							break;
						}
					}
					if ( ! $flag ) {
						$post_status = ucfirst( $post->post_status );
					}
					printf( '<mark style="%s" class="%s tips" data-tip="%s">%s</mark>', $style, $post_status, esc_html__( $post_status, RT_HD_PATH_ADMIN ), esc_html__( $post_status, RT_HD_PATH_ADMIN ) );
					break;

				case 'rtasset_asset_id':
					echo esc_attr( $post->ID );
					break;

			}
		}

		/**
		 * Remove Default meta boxes on Edit post View for Assets
		 *
		 * @since  rt-Assets 0.1
		 */
		public function remove_meta_boxes() {
			remove_meta_box( 'revisionsdiv', RT_Asset_Module::$post_type, 'normal' );
			remove_meta_box( 'slugdiv', RT_Asset_Module::$post_type, 'normal' );
		}

		/**
		 * Add custom meta boxes on Edit post View for Assets
		 *
		 * @since  rt-Assets 0.1
		 */
		public function add_meta_boxes() {
			add_meta_box( 'rt-asset-ticket-data', __( 'Asset Information', RT_ASSET_TEXT_DOMAIN ), 'RT_Meta_Box_Assets_Info::ui', RT_Asset_Module::$post_type, 'side', 'default' );

		}

		/**
		 * Save custom meta boxes Values on Edit post View for Assets
		 *
		 * @since  rt-Assets 0.1
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