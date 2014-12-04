<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_Asset_Device_Type' ) ) {
	/**
	 * Class RT_Asset_Device_Type
	 * Device Type for Assets
	 *
	 * @since rt-Assets 0.1
	 *
	 * @author Dipesh
	 */
	class RT_Asset_Device_Type {

		/**
		 * @var string Stores Post Type
		 *
		 * @since rt-Assets 0.1
		 */
		var $slug = 'device_type';

		/**
		 * @var array Labels for Device Type taxonomy
		 *
		 * @since rt-Assets 0.1
		 */
		var $labels = array();

		/**
		 * Construct
		 *
		 * @since rt-Assets 0.1
		 */
		public function __construct() {
			$this->get_custom_labels();
			$this->hook();
		}

		/**
		 * get Device Type taxonomy labels
		 *
		 * @since rt-Assets 0.1
		 *
		 * @return array
		 */
		function get_custom_labels() {
			$this->labels = array(
				'name' => __( 'Device Type', RT_ASSET_TEXT_DOMAIN ),
				'search_items' => __( 'Search Device Type', RT_ASSET_TEXT_DOMAIN ),
				'all_items' => __( 'All Device Types', RT_ASSET_TEXT_DOMAIN ),
				'edit_item' => __( 'Edit Device Type', RT_ASSET_TEXT_DOMAIN ),
				'update_item' => __( 'Update Device Type', RT_ASSET_TEXT_DOMAIN ),
				'add_new_item' => __( 'Add New Device Type', RT_ASSET_TEXT_DOMAIN ),
				'new_item_name' => __( 'New Device Type', RT_ASSET_TEXT_DOMAIN ),
				'menu_name' => __( 'Device Types', RT_ASSET_TEXT_DOMAIN ),
				'choose_from_most_used' => __( 'Choose from the most used Device Types', RT_ASSET_TEXT_DOMAIN ),
			);
			return $this->labels;
		}

		/**
		 * Hook
		 *
		 * @since rt-Assets 0.1
		 */
		public function hook() {
			add_action( 'init', array( $this, 'register_device_type' ) );

			add_filter( 'manage_edit-' . rtasset_attribute_taxonomy_name( $this->slug ) . '_columns', array( $this, 'add_stock_column_header' ) );
			add_filter( 'manage_' . rtasset_attribute_taxonomy_name( $this->slug ) . '_custom_column', array( $this, 'add_stock_column_body' ), 10, 3 );
		}

		/**
		 * Register Device type taxonomy for Assets
		 *
		 * @since rt-Assets 0.1
		 */
		public function register_device_type() {

			$editor_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'editor' );
			$author_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' );

			register_taxonomy( rtasset_attribute_taxonomy_name( $this->slug ), array( RT_Asset_Module::$post_type ), array(
				'hierarchical' => false,
				'labels' => $this->labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'query_var' => true,
				'update_count_callback' => 'rtasset_update_post_term_count',
				'rewrite' => array( 'slug' => rtasset_attribute_taxonomy_name( $this->slug ) ),
				'capabilities' => array(
					'manage_terms' => $editor_cap,
					'edit_terms' => $editor_cap,
					'delete_terms' => $editor_cap,
					'assign_terms' => $author_cap,
				),
			));
		}

		/**
		 * Method for save device type for post
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $post_id
		 * @param $newAsset
		 */
		function save_device_type( $post_id, $newAsset ) {
			if ( ! isset( $newAsset[ $this->slug ] ) ) {
				$newAsset[ $this->slug ] = array();
			}
			$device_types = array_map( 'intval', $newAsset[ $this->slug ] );
			$device_types = array_unique( $device_types );
			wp_set_post_terms( $post_id, $device_types, rtasset_attribute_taxonomy_name( $this->slug ) );
		}

		/**
		 * Method to add custom columns header on list view
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function add_stock_column_header( $columns ) {
			$columns['stock'] = __( 'In Stock' );
			return $columns;
		}

		/**
		 * Method to add custom columns body on list view
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $out
		 * @param $column_name
		 * @param $devicetype_id
		 *
		 * @return string
		 */
		function add_stock_column_body( $out, $column_name, $devicetype_id ) {
			$device_type = get_term( $devicetype_id, rtasset_attribute_taxonomy_name( $this->slug ) );
			switch ( $column_name ) {
				case 'stock':
					$stock = $this->get_stock( $device_type->name );
					$out .= "<a href='edit.php?rt_device-type=" . $device_type->slug . '&post_type=' . RT_Asset_Module::$post_type . "&post_status=asset-unassigned'>" . $stock . '</a>';
					break;
				default:
					break;
			}

			return $out;
		}

		/**
		 * Method to get stock of devices
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $device_type
		 *
		 * @return mixed
		 */
		function get_stock( $device_type ){
			$args       = array(
				'post_type' => RT_Asset_Module::$post_type,
				rtasset_attribute_taxonomy_name( $this->slug ) => $device_type,
				'post_status' => 'asset-unassigned',
			);
			$stockquery = new WP_Query( $args );
			return $stockquery->found_posts;
		}

	}
}