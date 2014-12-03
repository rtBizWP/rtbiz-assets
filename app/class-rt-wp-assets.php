<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_WP_Assets' ) ) {

	/**
	 * Class RT_WP_Assets
	 * Check Dependency
	 * Main class that initialize the rt-assets Classes.
	 * Load Css/Js for front end
	 *
	 * @since  0.1
	 *
	 * @author dipesh
	 */
	class RT_WP_Assets {

		/**
		 * @var $templateURL is used to set template's root path
		 *
		 * @since 0.1
		 */
		public $templateURL;

		/**
		 * Constructor of RT_WP_Assets checks dependency and initialize all classes and set all hooks for this class
		 *
		 * @since 0.1
		 */
		public function __construct() {

			if ( ! rtasset_check_plugin_dependecy() ) {
				return false;
			}

			$this->init_globals();

			add_action( 'init', array( $this, 'admin_init' ), 5 );
			add_action( 'init', array( $this, 'init' ), 6 );

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		}

		/**
		 * Initialize the global variables for all rtbiz-assets classes
		 *
		 * @since 0.1
		 */
		function init_globals() {
			global $rt_asset_module, $rt_asset_device_type, $rt_asset_cpt_assets, $rt_asset_dashboard, $rt_asset_acl;

			$rt_asset_module      = new RT_Asset_Module();
			$rt_asset_device_type = new RT_Asset_Device_Type();
			$rt_asset_cpt_assets  = new RT_Asset_CPT_Assets();

			$rt_asset_dashboard = new RT_Asset_Dashboard();
			$rt_asset_acl       = new RT_Asset_ACL();
		}

		/**
		 * Admin_init sets admin UI and functionality,
		 * initialize the database,
		 *
		 * @since 0.1
		 */
		function admin_init() {
			$this->templateURL = apply_filters( 'rtasset_template_url', 'rtasset/' );

			global $rt_asset_admin;
			$rt_asset_admin = new RT_Asset_Admin();
		}


		/**
		 * Initialize the frontend
		 *
		 * @since 0.1
		 */
		function init() {

		}

		/**
		 * Register all js
		 *
		 * @since 0.1
		 */
		function load_scripts() {

			$this->localize_scripts();
		}

		/**
		 * This is functions localize values for JScript
		 * @since 0.1
		 */
		function localize_scripts() {

			global $post;

			if ( empty( $post ) ) {
				return;
			}

			$user_edit = false;

			if ( wp_script_is( 'rtasset-app-js' ) ) {
				wp_localize_script( 'rtasset-app-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
				wp_localize_script( 'rtasset-app-js', 'rtasset_post_type', get_post_type( $post->ID ) );
				wp_localize_script( 'rtasset-app-js', 'rtasset_user_edit', array( $user_edit ) );
			}

			return true;
		}
	}
}
