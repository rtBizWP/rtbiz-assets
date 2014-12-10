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

			add_action( 'admin_menu', array( $this, 'register_menu' ), 1 );

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		}

		/**
		 * Initialize the global variables for all rtbiz-assets classes
		 *
		 * @since rt-Assets 0.1
		 */
		function init_globals() {
			global $rt_asset_module, $rt_asset_bundle_module, $rt_asset_device_type, $rt_asset_dashboard, $rt_asset_acl, $rt_asset_setting;

			$rt_asset_module      = new RT_Asset_Module();
			$rt_asset_bundle_module = new RT_Asset_Bundle_Module();
			$rt_asset_device_type = new RT_Asset_Device_Type();

			$rt_asset_dashboard = new RT_Asset_Dashboard();
			$rt_asset_acl       = new RT_Asset_ACL();
			$rt_asset_setting   = new RT_Asset_Settings();

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

			$this->register_bundle_asset_connection();
		}

		/**
		 *  Registers Posts 2 Posts relation for Bundle - Asset
		 */
		function register_bundle_asset_connection() {
			add_action( 'p2p_init', array( $this, 'bundle_asset_connection' ) );
		}

		/**
		 *  Bundle - Asset Connection for Posts 2 Posts
		 */
		function bundle_asset_connection() {
			p2p_register_connection_type(
				array(
					'name' => RT_Asset_Bundle_Module::$post_type . '_to_' . RT_Asset_Module::$post_type,
					'from' => RT_Asset_Bundle_Module::$post_type,
					'to'   => RT_Asset_Module::$post_type,
				)
			);
		}

		/**
		 *  This establishes a connection between any entiy ( either organization - from / person - to )
		 *  acording to the parameters passed.
		 *
		 * @param string $from - Organization
		 * @param string $to   - Person
		 */
		function connect_bundle_to_asset( $from = '', $to = '' ) {

			if ( ! p2p_connection_exists( RT_Asset_Bundle_Module::$post_type . '_to_' . RT_Asset_Module::$post_type, array(
				'from' => $from,
				'to'   => $to,
			) )
			) {
				p2p_create_connection( RT_Asset_Bundle_Module::$post_type . '_to_' . RT_Asset_Module::$post_type, array(
					'from' => $from,
					'to'   => $to,
				) );
			}
		}

		/**
		 *  Returns all the connected posts to the passed parameter entity object.
		 *  It can be either an organization object or a person object.
		 *
		 *  It will return the other half objects of the connection.
		 *
		 * @param $connected_items - Organization / Person Object
		 *
		 * @return array
		 */
		function get_bundle_to_asset_connection( $connected_items ) {
			return get_posts(
				array(
					'connected_type'   => RT_Asset_Bundle_Module::$post_type . '_to_' . RT_Asset_Module::$post_type,
					'connected_items'  => $connected_items,
					'nopaging'         => true,
					'suppress_filters' => false,
					'post_status' => 'any',
				)
			);
		}

		/**
		 *  Registers all the menus/submenus for rtBiz-assets
		 */
		function register_menu() {
			global $rt_asset_dashboard, $rt_asset_module, $rt_asset_bundle_module, $rt_asset_device_type;

			$settings = rtasset_get_redux_settings();

			$menu_position = 41;
			$logo_url               = isset( $settings['rtasset_logo_url'] ) ? $settings['rtasset_logo_url']['url'] : RT_ASSET_URL . 'app/assets/img/asset-16X16.png';
			$menu_label             = isset( $settings['rtasset_menu_label'] ) ? $settings['rtasset_menu_label'] : 'rtAsset';

			$bundle_label = $rt_asset_bundle_module->get_custom_labels();
			$asset_label = $rt_asset_module->get_custom_labels();
			$devicetype_label = $rt_asset_device_type->get_custom_labels();

			$rt_asset_dashboard->screen_id = add_menu_page( $menu_label, $menu_label, rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' ), RT_Asset_Dashboard::$dashboard_slug, array(
				$rt_asset_dashboard,
				'dashboard_ui',
			), $logo_url, $menu_position );

			add_submenu_page( RT_Asset_Dashboard::$dashboard_slug, $bundle_label['name'], $bundle_label['name'], rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' ), 'edit.php?post_type=' . RT_Asset_Bundle_Module::$post_type );
			add_submenu_page( RT_Asset_Dashboard::$dashboard_slug, $bundle_label['add_new'], '--- '.$bundle_label['add_new'], rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' ), 'post-new.php?post_type=' . RT_Asset_Bundle_Module::$post_type );

			add_submenu_page( RT_Asset_Dashboard::$dashboard_slug, $asset_label['name'], $asset_label['name'], rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' ), 'edit.php?post_type=' . RT_Asset_Module::$post_type );
			add_submenu_page( RT_Asset_Dashboard::$dashboard_slug, $asset_label['add_new'], '--- '.$asset_label['add_new'], rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' ), 'post-new.php?post_type=' . RT_Asset_Module::$post_type );

			add_submenu_page( RT_Asset_Dashboard::$dashboard_slug, $devicetype_label['name'], $devicetype_label['name'], rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'editor' ), 'edit-tags.php?taxonomy=' . rtasset_attribute_taxonomy_name( $rt_asset_device_type->slug ) . '&post_type=' . RT_Asset_Module::$post_type );
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
