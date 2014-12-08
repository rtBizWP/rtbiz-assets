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
		 * @var string - rtBiz-asset Dashboard Page Slug
		 */
		public static $dashboard_slug = 'rt-biz-assets-dashboard';

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
