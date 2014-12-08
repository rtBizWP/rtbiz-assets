<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description of RT_Asset_Admin
 * RT_Asset_Admin is main class for admin backend and UI.
 *
 * @author Dipesh
 * @since  rt-Assets 0.1
 */
if ( ! class_exists( 'RT_Asset_Admin' ) ) {
	/**
	 * Class RT_Asset_Admin
	 */
	class RT_Asset_Admin {
		/**
		 * @var $admin_cap : capabilities for admin
		 * @var $editor_cap : capabilities for editor
		 * @var $author_cap : capabilities for author
		 */
		private $admin_cap, $editor_cap, $author_cap;

		/**
		 * construct
		 *
		 * @since rt-Assets 0.1
		 */
		public function __construct() {

			$this->admin_cap  = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'admin' );
			$this->editor_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'editor' );
			$this->author_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' );

			if ( is_admin() ) {
				$this->hooks();
			}
		}

		/**
		 * Hooks
		 *
		 * @since rt-Assets 0.1
		 */
		function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
		}

		/**
		 * Register CSS and JS
		 *
		 * @since rt-Assets 0.1
		 */
		function load_styles_scripts() {
			global $post, $pagenow, $wp_scripts;

			if ( isset( $post->post_type ) && in_array( $post->post_type, array( RT_Asset_Module::$post_type, RT_Asset_Bundle_Module::$post_type ) ) ) {

				wp_enqueue_script( 'jquery-ui-timepicker-addon', RT_ASSET_URL . 'app/assets/javascripts/jquery-ui-timepicker-addon.js', array(
					'jquery-ui-datepicker',
					'jquery-ui-slider',
				), RT_ASSET_VERSION, true );

				if ( ! wp_script_is( 'jquery-ui-datepicker' ) ) {
					wp_enqueue_script( 'jquery-ui-datepicker' );
				}
				if ( ! wp_script_is( 'jquery-ui-autocomplete' ) ) {
					wp_enqueue_script( 'jquery-ui-autocomplete', '', array(
						'jquery-ui-widget',
						'jquery-ui-position',
					), '1.9.2' );
				}

				wp_enqueue_script( 'moment-js', RT_ASSET_URL . 'app/assets/javascripts/moment.js', array( 'jquery' ), RT_ASSET_VERSION, true );
			}
			wp_enqueue_style( 'rthd_date_styles', '//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css', array(), RT_ASSET_VERSION );
			wp_enqueue_style( 'rtasset_admin_styles', RT_ASSET_URL . 'app/assets/css/admin_new.css', array(), RT_ASSET_VERSION );

			wp_enqueue_script( 'jquery-tiptip', RT_ASSET_URL . 'app/assets/javascripts/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), RT_ASSET_VERSION, true );
			wp_enqueue_script( 'rtasset-admin-js', RT_ASSET_URL . 'app/assets/javascripts/admin_new.js', array( 'jquery-tiptip' ), RT_ASSET_VERSION, true );

			$this->localize_scripts();
		}

		/**
		 * Passes data to JS
		 *
		 * @since rt-Assets 0.1
		 */
		function localize_scripts() {
			global $pagenow, $rt_asset_dashboard;
			if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
				$user_edit = false;
				$rtasset_post_type = isset( $_GET['post'] ) ? get_post_type( $_GET['post'] ) : '';
				if ( ! in_array( $rtasset_post_type, array( RT_Asset_Module::$post_type, RT_Asset_Bundle_Module::$post_type ) ) ){
					return;
				}
				if ( current_user_can( 'edit_' . $rtasset_post_type ) ) {
					$user_edit = true;
				}
				if ( isset( $_REQUEST['post'] ) ) {
					wp_localize_script( 'rtasset-admin-js', 'rtbiz_asset_dashboard_screen', $rt_asset_dashboard->screen_id );
				}
				wp_localize_script( 'rtasset-admin-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
				wp_localize_script( 'rtasset-admin-js', 'rtasset_post_type', $rtasset_post_type );
				wp_localize_script( 'rtasset-admin-js', 'rtasset_user_edit', array( $user_edit ) );
			} else {
				wp_localize_script( 'rtasset-admin-js', 'rtasset_user_edit', array( '' ) );
			}
		}
	}
}