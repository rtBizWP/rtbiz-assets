<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 4/12/14
 * Time: 1:56 PM
 */

class test_RT_Asset_Module extends RT_WP_TestCase {
	var $rtassetModule;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtassetModule = new RT_Asset_Module();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtassetModule, 'get_custom_labels' ), 'Class RT_Asset_Module does not have method get_custom_labels' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'get_custom_statuses' ), 'Class RT_Asset_Module does not have method get_custom_statuses' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'get_custom_menu_order' ), 'Class RT_Asset_Module does not have method get_custom_menu_order' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'init_asset' ), 'Class RT_Asset_Module does not have method init_asset' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'register_custom_post' ), 'Class RT_Asset_Module does not have method register_custom_post' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'register_custom_statuses' ), 'Class RT_Asset_Module does not have method register_custom_statuses' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'hooks' ), 'Class RT_Asset_Module does not have method hooks' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'custom_pages_order' ), 'Class RT_Asset_Module does not have method custom_pages_order' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'assets_chnage_action_publish_update' ), 'Class RT_Asset_Module does not have method assets_chnage_action_publish_update' );
	}

	/**
	 * Test Class variable
	 */
	function  test_class_local_variable() {
		$this->assertEquals( 'rtbiz_asset_assets', RT_Asset_Module::$post_type );
		$this->assertEquals( 'Assets', $this->rtassetModule->name );
	}

	/**
	 * Test get_custom_labels
	 */
	function  test_get_custom_labels() {
		$this->assertTrue( is_array( $this->rtassetModule->labels ) );
	}

	/**
	 * Test get_custom_statuses
	 */
	function  test_get_custom_statuses() {
		$this->assertTrue( is_array( $this->rtassetModule->statuses ) );
	}

	/**
	 * Test get_custom_menu_order
	 */
	function  test_get_custom_menu_order() {
		$this->assertTrue( is_array( $this->rtassetModule->custom_menu_order ) );
	}

	/**
	 * Test register_custom_post
	 */
	function  test_register_custom_post() {
		$this->assertTrue( post_type_exists( RT_Asset_Module::$post_type ) );
	}

	/**
	 * Test register_custom_statuses
	 */
	function  test_register_custom_statuses() {
		$status = array(
			'slug'        => 'Demo',
			'name'        => __( 'Demo', RT_ASSET_TEXT_DOMAIN ),
			'description' => __( 'Testiing', RT_ASSET_TEXT_DOMAIN ),
		);
		$this->assertTrue( is_object( $this->rtassetModule->register_custom_statuses( $status ) ) );
	}

}
 