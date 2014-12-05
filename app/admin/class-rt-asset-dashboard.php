<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_Asset_Dashboard' ) ) {
	/**
	 * Class RT_Asset_Dashboard
	 * Dashboard for Assets
	 * render charts on deshboad
	 *
	 * @since rt-Assets 0.1
	 *
	 * @author Dipesh
	 */
	class RT_Asset_Dashboard {

		/**
		 * @var string screen id for dashboard
		 *
		 * @since rt-Assets 0.1
		 */
		var $screen_id;
		/**
		 * @var array store charts
		 *
		 * @since rt-Assets 0.1
		 */
		var $charts = array();

		/**
		 * Construct
		 *
		 * @since rt-Assets 0.1
		 */
		public function __construct() {
			$this->screen_id = '';
			$this->hook();
		}

		/**
		 * Hook
		 *
		 * @since rt-Assets 0.1
		 */
		public function hook() {
			add_action( 'admin_menu', array( $this, 'register_dashboard' ), 1 );
		}

		/**
		 * Register dashboard for custom page & hook for MetaBox on it
		 *
		 * @since rt-Assets 0.1
		 */
		function register_dashboard() {

			$author_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' );

			$this->screen_id = add_submenu_page( 'edit.php?post_type=' . esc_html( RT_Asset_Module::$post_type ), __( 'Dashboard', RT_ASSET_TEXT_DOMAIN ), __( 'Dashboard', RT_ASSET_TEXT_DOMAIN ), $author_cap, 'rtasset-' . esc_html( RT_Asset_Module::$post_type ) . '-dashboard', array(
				$this,
				'dashboard_ui',
			) );

		}

		/**
		 * render dashboard template for given post type
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $post_type
		 */
		function dashboard_ui( $post_type ) {

		}


	}
}
