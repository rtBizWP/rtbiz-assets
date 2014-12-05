<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 4/12/14
 * Time: 11:58 AM
 */

class test_RT_Asset_ACL extends RT_WP_TestCase {
	var $rtassetAcl;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtassetAcl = new RT_Asset_ACL();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtassetAcl, 'register_rt_asset_module' ), 'Class RT_Asset_ACL does not have method register_rt_asset_module' );
	}

	/**
	 * Ensure that required Class exist
	 */
	function test_check_class_exist() {
		$this->assertTrue( class_exists( 'RT_Asset_Module' ), 'Class RT_Asset_Module does not exist' );
	}

	/**
	 * Test register_rt_asset_module
	 */
	function  test_register_rt_asset_module() {
		$this->assertEquals(
				array(
					'rtbiz_assets' => array(
						'label' => 'rtAssets',
						'post_types' => array( RT_Asset_Module::$post_type ),
						'require_user_groups' => false,
						'require_product_sync' => false,
					)
				),
				$this->rtassetAcl->register_rt_asset_module( array() ) );
	}
}
 