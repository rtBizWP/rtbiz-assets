<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description of RT_Asset_ACL
 *
 * @author Dipesh
 * @since  rt-Assets 0.1
 */
if ( ! class_exists( 'RT_Asset_ACL' ) ) {
	/**
	 * Class RT_Asset_ACL
	 * Add ACL(access control list) support to Assets plugin
	 *
	 * @since rt-Assets 0.1
	 */
	class RT_Asset_ACL {
		/**
		 * Hook for register rtbiz-Assets module with rtbiz
		 *
		 * @since 0.1
		 */
		public function __construct() {
			add_filter( 'rt_biz_modules', array( $this, 'register_rt_asset_module' ) );
		}

		/**
		 * Register module rtbiz-Assets
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $modules
		 *
		 * @return mixed
		 */
		function register_rt_asset_module( $modules ) {
			$settings               = rtasset_get_redux_settings();
			$module_key             = rt_biz_sanitize_module_key( RT_ASSET_TEXT_DOMAIN );
			$modules[ $module_key ] = array(
				'label'      => isset( $settings['rtasset_menu_label'] ) ? $settings['rtasset_menu_label'] : 'rtAssets',
				'post_types' => array( RT_Asset_Module::$post_type, RT_Asset_Bundle_Module::$post_type ),
				'require_user_groups' => false,
				'require_product_sync' => false,
			);

			return $modules;
		}
	}
}
