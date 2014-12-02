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
	 * @since 0.1
	 */
	class RT_Asset_Device_Type {

		/**
		 * @var string Stores Post Type
		 *
		 * @since 0.1
		 */
		var $slug = 'device_type';

		/**
		 * @var array Labels for Device Type taxonomy
		 *
		 * @since 0.1
		 */
		var $labels = array();

		/**
		 * Construct
		 *
		 * @since 0.1
		 */
		public function __construct() {
			$this->get_custom_labels();
			$this->hook();
		}

		/**
		 * get Device Type taxonomy labels
		 *
		 * @since 0.1
		 *
		 * @return array
		 */
		function get_custom_labels() {
			$this->labels = array(
				'name' => __( 'Device Type' ),
				'search_items' => __( 'Search Device Type' ),
				'all_items' => __('All Device Types'),
				'edit_item' => __('Edit Device Type'),
				'update_item' => __('Update Device Type'),
				'add_new_item' => __('Add New Device Type'),
				'new_item_name' => __('New Device Type'),
				'menu_name' => __('Device Types'),
				'choose_from_most_used' => __('Choose from the most used Device Types'),
			);
			return $this->labels;
		}

		public function hook(){
			add_action( 'init', array( $this, 'register_device_type' ) );
		}

		public function register_device_type(){

			$editor_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'editor' );
			$author_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' );

			register_taxonomy(rtasset_attribute_taxonomy_name( $this->slug ), array( RT_Asset_Module::$post_type ), array(
				'hierarchical' => false,
				'labels' => $this->labels,
				'show_ui' => true,
				'query_var' => true,
				'update_count_callback' => 'rtasset_update_post_term_count',
				'rewrite' => array('slug' => rtasset_attribute_taxonomy_name('closing_reason')),
				'capabilities' => array(
					'manage_terms' => $editor_cap,
					'edit_terms' => $editor_cap,
					'delete_terms' => $editor_cap,
					'assign_terms' => $author_cap,
				),
			));
		}

		function save_closing_reason( $post_id, $newAsset ) {
			if ( !isset( $newAsset[ $this->slug ] ) ) {
				$newAsset[ $this->slug ] = array();
			}
			$device_types = array_map( 'intval', $newAsset[ $this->slug ] );
			$device_types = array_unique( $device_types );
			wp_set_post_terms( $post_id, $device_types, rtcrm_attribute_taxonomy_name( $this->slug ) );
		}
	}
}