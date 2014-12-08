<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 9/12/14
 * Time: 12:17 AM
 */

class test_RT_Asset_Bundle_Module extends PHPUnit_Framework_TestCase {
	var $rtassetBundleModule;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtassetBundleModule = new RT_Asset_Bundle_Module();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'get_custom_labels' ), 'Class RT_Asset_Module does not have method get_custom_labels' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'get_custom_statuses' ), 'Class RT_Asset_Module does not have method get_custom_statuses' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'hooks' ), 'Class RT_Asset_Module does not have method hooks' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'init_entity' ), 'Class RT_Asset_Module does not have method init_entity' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'register_post_type' ), 'Class RT_Asset_Module does not have method register_post_type' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'register_custom_statuses' ), 'Class RT_Asset_Module does not have method register_custom_statusesregister_custom_statuses' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'assets_chnage_action_publish_update' ), 'Class RT_Asset_Module does not have method assets_chnage_action_publish_update' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'save_meta_boxes' ), 'Class RT_Asset_Module does not have method save_meta_boxesv' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'custom_post_status_rendar' ), 'Class RT_Asset_Module does not have method custom_post_status_rendar' );

		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'edit_custom_columns' ), 'Class RT_Asset_Module does not have method edit_custom_columns' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'sortable_column' ), 'Class RT_Asset_Module does not have method sortable_column' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'manage_custom_columns' ), 'Class RT_Asset_Module does not have method manage_custom_columns' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'remove_meta_boxes' ), 'Class RT_Asset_Module does not have method remove_meta_boxes' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'add_meta_boxes' ), 'Class RT_Asset_Module does not have method add_meta_boxes' );
		$this->assertTrue( method_exists( $this->rtassetBundleModule, 'save_rt_assets_meta_boxes' ), 'Class RT_Asset_Module does not have method save_rt_assets_meta_boxes' );
	}

	/**
	 * Test Class variable
	 */
	function  test_class_local_variable() {
		$this->assertEquals( 'rtbiz_asset_bundle', RT_Asset_Bundle_Module::$post_type );
		$this->assertEquals( 'Assets', $this->rtassetBundleModule->name );
	}

	/**
	 * Test get_custom_labels
	 */
	function  test_get_custom_labels() {
		$this->assertTrue( is_array( $this->rtassetBundleModule->labels ) );
	}

	/**
	 * Test get_custom_statuses
	 */
	function  test_get_custom_statuses() {
		$this->assertTrue( is_array( $this->rtassetBundleModule->statuses ) );
	}

	/**
	 * Test register_custom_post
	 */
	function  test_register_custom_post() {
		$this->assertTrue( post_type_exists( RT_Asset_Bundle_Module::$post_type ) );
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
		$this->assertTrue( is_object( $this->rtassetBundleModule->register_custom_statuses( $status ) ) );
	}

	/**
	 * Test edit_custom_columns
	 */
	function test_edit_custom_columns(){
		$this->assertEquals(
			array(
				'cb' => 'cb',
				'title' => 'title',
				'rtasset_asset_status' => '<span class="status_head tips" data-tip="Status">Status</span>',
				'rtasset_assignee' => '<span class="assignee_head tips" data-tip="Assignee">Assigned To</span>',
				'comments' => 'comments',
				'date' => 'date',
			),
			$this->rtassetBundleModule->edit_custom_columns( array(
				'title' => 'title',
				'date' => 'date',
				'comments' => 'comments',
				'cb' => 'cb',
			) )
		);
	}

	/**
	 * Test sortable_column
	 */
	function test_sortable_column(){
		$this->assertEquals(
			array(
				'title' => 'title',
				'rtasset_asset_status' => 'post_status',
				'rtasset_assignee' => 'post_author',
			),
			$this->rtassetBundleModule->sortable_column( array(
				'title' => 'title',
			) )
		);
	}
}
 