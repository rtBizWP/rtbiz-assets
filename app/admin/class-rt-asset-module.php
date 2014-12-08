<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_Asset_Module' ) ) {
	/**
	 * Class RT_Asset_Module
	 * Register rtbiz-Assets CPT [ Assets ] & statuses
	 *
	 * @since  rt-Assets 0.1
	 *
	 * @author Dipesh
	 */
	class RT_Asset_Module extends RT_Asset_Entity {

		/**
		 * @var string Stores Post Type
		 *
		 * @since rt-Assets 0.1
		 */
		static $post_type = 'rtbiz_asset_assets';

		/**
		 * initiate class local Variables
		 *
		 * @since rt-Assets 0.1
		 */
		public function __construct() {
			parent::__construct( self::$post_type );

			$this->get_custom_labels();
		}

		/**
		 * get rtbiz-Assets CPT [ Assets ] labels
		 *
		 * @since rt-Assets 0.1
		 *
		 * @return array
		 */
		function get_custom_labels() {
			$settings     = rtasset_get_redux_settings();
			$this->labels = array(
				'name'          => __( 'Asset', RT_ASSET_TEXT_DOMAIN ),
				'singular_name' => __( 'Asset', RT_ASSET_TEXT_DOMAIN ),
				'menu_name'     => isset( $settings['rtasset_menu_label'] ) ? $settings['rtasset_menu_label'] : 'rtAssets',
				'all_items'     => __( 'All Assets', RT_ASSET_TEXT_DOMAIN ),
				'add_new'       => __( 'Add Asset', RT_ASSET_TEXT_DOMAIN ),
				'add_new_item'  => __( 'Add Asset', RT_ASSET_TEXT_DOMAIN ),
				'new_item'      => __( 'Add Asset', RT_ASSET_TEXT_DOMAIN ),
				'edit_item'     => __( 'Edit Asset', RT_ASSET_TEXT_DOMAIN ),
				'view_item'     => __( 'View Asset', RT_ASSET_TEXT_DOMAIN ),
				'search_items'  => __( 'Search Assets', RT_ASSET_TEXT_DOMAIN ),
				'not_found'  => __( 'No Assets found', RT_ASSET_TEXT_DOMAIN ),
				'not_found_in_trash'  => __( 'No Assets found in Trash', RT_ASSET_TEXT_DOMAIN ),
			);

			return $this->labels;
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

			global $post;

			switch ( $column ) {

				case 'rtasset_asset_status' :
					$post_status_list = $this->get_custom_statuses();
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
					printf( '<mark style="%s" class="%s tips" data-tip="%s">%s</mark>', $style, $post_status, esc_html__( $post_status, RT_ASSET_TEXT_DOMAIN ), esc_html__( $post_status, RT_ASSET_TEXT_DOMAIN ) );
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
			remove_meta_box( 'revisionsdiv', self::$post_type, 'normal' );
			remove_meta_box( 'slugdiv', self::$post_type, 'normal' );
		}

		/**
		 * Add custom meta boxes on Edit post View for Assets
		 *
		 * @since  rt-Assets 0.1
		 */
		public function add_meta_boxes() {
			add_meta_box( 'rt-asset-asset-assignee', __( 'Asset Assignee', RT_ASSET_TEXT_DOMAIN ), 'RT_Meta_Box_Assets_Assignee::ui', self::$post_type, 'side', 'default' );
			add_meta_box( 'rt-asset-asset-info', __( 'Asset Information', RT_ASSET_TEXT_DOMAIN ), 'RT_Meta_Box_Assets_Info::ui', self::$post_type, 'side', 'default' );

		}

		public function save_rt_assets_meta_boxes( $post_id, $post ){
			if ( get_post_type( $post_id ) != RT_Asset_Module::$post_type ){
				return;
			}
			RT_Meta_Box_Assets_Info::save( $post_id, $post );
		}

	}
}