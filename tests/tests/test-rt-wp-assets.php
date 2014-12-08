<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 4/12/14
 * Time: 2:24 PM
 */

class test_RT_WP_Assets extends RT_WP_TestCase {
	var $rtwpAssets;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtwpAssets = new RT_WP_Assets();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtwpAssets, 'init_globals' ), 'Class RT_WP_Assets does not have method init_globals' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'admin_init' ), 'Class RT_WP_Assets does not have method admin_init' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'init' ), 'Class RT_WP_Assets does not have method init' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'register_menu' ), 'Class RT_WP_Assets does not have method register_menu' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'register_bundle_asset_connection' ), 'Class RT_WP_Assets does not have method register_bundle_asset_connection' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'bundle_asset_connection' ), 'Class RT_WP_Assets does not have method bundle_asset_connection' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'connect_bundle_to_asset' ), 'Class RT_WP_Assets does not have method connect_bundle_to_asset' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'get_bundle_to_asset_connection' ), 'Class RT_WP_Assets does not have method get_bundle_to_asset_connection' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'load_scripts' ), 'Class RT_WP_Assets does not have method load_scripts' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'localize_scripts' ), 'Class RT_WP_Assets does not have method localize_scripts' );
		$this->assertTrue( method_exists( $this->rtwpAssets, 'assets_flush_rewrite_rules' ), 'Class RT_WP_Assets does not have method assets_flush_rewrite_rules' );
	}

	/**
	 * Ensure that required Class exist
	 */
	function test_check_class_exist() {
		$this->assertTrue( class_exists( 'RT_Asset_Module' ), 'Class RT_Asset_Module does not exist' );
		$this->assertTrue( class_exists( 'RT_Asset_Device_Type' ), 'Class RT_Asset_Device_Type does not exist' );
		$this->assertTrue( class_exists( 'RT_Asset_Bundle_Module' ), 'Class RT_Asset_Bundle_Module does not exist' );
		$this->assertTrue( class_exists( 'RT_Asset_Dashboard' ), 'Class RT_Asset_Dashboard does not exist' );
		$this->assertTrue( class_exists( 'RT_Asset_ACL' ), 'Class RT_Asset_ACL does not exist' );
		$this->assertTrue( class_exists( 'RT_Asset_Admin' ), 'Class RT_Asset_Admin does not exist' );
	}

}