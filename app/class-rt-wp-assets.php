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
	 * @since  rt-Assets 0.1
	 *
	 * @author dipesh
	 */
	class RT_WP_Assets {

		/**
		 * @var $templateURL is used to set template's root path
		 *
		 * @since rt-Assets 0.1
		 */
		public $templateURL;

		/**
		 * Constructor of RT_WP_Assets checks dependency and initialize all classes and set all hooks for this class
		 *
		 * @since rt-Assets 0.1
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
		 * @since rt-Assets 0.1
		 */
		function init_globals() {
			global $rt_asset_module, $rt_asset_device_type, $rt_asset_cpt_assets, $rt_asset_dashboard, $rt_asset_acl;

			$rt_asset_module      = new RT_Asset_Module();
			$rt_asset_device_type = new RT_Asset_Device_Type();
			$rt_asset_cpt_assets  = new RT_Asset_CPT_Assets();

			$rt_asset_dashboard = new RT_Asset_Dashboard();
			$rt_asset_acl       = new RT_Asset_ACL();

			$taxonomy_metadata = new Rt_Lib_Taxonomy_Metadata\Taxonomy_Metadata();
			$taxonomy_metadata->activate();
		}

		/**
		 * Admin_init sets admin UI and functionality,
		 * initialize the database,
		 *
		 * @since rt-Assets 0.1
		 */
		function admin_init() {
			$this->templateURL = apply_filters( 'rtasset_template_url', 'rtasset/' );

			global $rt_asset_admin;
			$rt_asset_admin = new RT_Asset_Admin();
		}


		/**
		 * Initialize the frontend
		 *
		 * @since rt-Assets 0.1
		 */
		function init() {
			add_action( 'init', array( $this, 'assets_flush_rewrite_rules' ), 15 );
		}

		/**
		 * Flush the rule
		 *
		 * @since rt-Assets 0.1
		 */
		function assets_flush_rewrite_rules() {
			if ( is_admin() && 'true' == get_option( 'rtasset_flush_rewrite_rules' ) ) {
				flush_rewrite_rules();
				delete_option( 'rtasset_flush_rewrite_rules' );
			}
		}

		/**
		 * Register all js
		 *
		 * @since rt-Assets 0.1
		 */
		function load_scripts() {

			$this->localize_scripts();
		}

		/**
		 * This is functions localize values for JScript
		 * @since rt-Assets 0.1
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
