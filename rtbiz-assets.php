<?php

/**
 * Plugin Name: rtBiz Assets
 * Plugin URI: http://rtcamp.com/
 * Description: Assets System for handle & track Assets of organization
 * Version: 0.1
 * Author: rtCamp
 * Author URI: http://rtcamp.com
 * License: GPL
 * Text Domain: rtbiz_assets
 * Contributors: Dipesh<dipesh.kakadiya@rtcamp.com>
 */

if ( ! defined( 'RT_ASSET_VERSION' ) ) {
	/**
	 * Defines RT_ASSET_VERSION if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_VERSION', '0.1' );
}
if ( ! defined( 'RT_ASSET_TEXT_DOMAIN' ) ) {
	/**
	 * Defines RT_ASSET_TEXT_DOMAIN if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_TEXT_DOMAIN', 'rtbiz_assets' );
}
if ( ! defined( 'RT_ASSET_PATH' ) ) {
	/**
	 * Defines RT_ASSET_PATH if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'RT_ASSET_URL' ) ) {
	/**
	 * Defines RT_ASSET_URL if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'RT_ASSET_PATH_APP' ) ) {
	/**
	 * Defines app folder path if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_PATH_APP', plugin_dir_path( __FILE__ ) . 'app/' );
}
if ( ! defined( 'RT_ASSET_PATH_ADMIN' ) ) {
	/**
	 *  Defines app/admin path if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_PATH_ADMIN', plugin_dir_path( __FILE__ ) . 'app/admin/' );
}
if ( ! defined( 'RT_ASSET_PATH_HELPER' ) ) {
	/**
	 *  Defines app/vendor path if it does not exits.
	 *
	 * @since rt-Assets 0.1
	 */
	define( 'RT_ASSET_PATH_HELPER', plugin_dir_path( __FILE__ ) . 'app/helper/' );
}

include_once RT_ASSET_PATH_HELPER . 'rtasset-functions.php';

/**
 * Using rt-lib [ RT_WP_Autoload ] class, Includes all files & external Require Libraries with in given directory.
 *
 * @since rt-Assets 0.1
 */
function rt_asset_include() {

	//require_once RT_ASSET_PATH_VENDOR . 'redux/ReduxCore/framework.php';

	global $rtast_app_autoload, $rtast_admin_autoload, $rtast_admin_metabox_autoload;
	$rtast_app_autoload           = new RT_WP_Autoload( RT_ASSET_PATH_APP );
	$rtast_admin_autoload         = new RT_WP_Autoload( RT_ASSET_PATH_ADMIN );
	$rtast_admin_metabox_autoload = new RT_WP_Autoload( RT_ASSET_PATH_ADMIN . 'meta-box/' );

}

/**
 * Main function that initiate rt-assets plugin
 *
 * @since rt-Assets 0.1
 */
function rt_asset_init() {

	rt_asset_include();

	global $rt_wp_ast;
	$rt_wp_ast = new RT_WP_Assets();
}

add_action( 'rt_biz_init', 'rt_asset_init', 1 );

/**
 * RT_ASSET_check_dependency check for rtbiz-Assets dependency
 * dependencies are require to run file else this plugin can't function
 *
 * @since rt-Assets 0.1
 */
add_action( 'init', 'rtasset_check_plugin_dependecy' );


register_activation_hook( __FILE__, 'init_call_flush_rewrite_rules' );
/**
 * Flush rule on plugin activation
 *
 * @since rt-Assets 0.1
 */
function init_call_flush_rewrite_rules() {
	add_option( 'rtasset_flush_rewrite_rules', 'true' );
}
