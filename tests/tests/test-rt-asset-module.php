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
		$this->assertTrue( method_exists( $this->rtassetModule, 'hooks' ), 'Class RT_Asset_Module does not have method hooks' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'init_entity' ), 'Class RT_Asset_Module does not have method init_entity' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'register_post_type' ), 'Class RT_Asset_Module does not have method register_post_type' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'register_custom_statuses' ), 'Class RT_Asset_Module does not have method register_custom_statusesregister_custom_statuses' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'assets_chnage_action_publish_update' ), 'Class RT_Asset_Module does not have method assets_chnage_action_publish_update' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'save_meta_boxes' ), 'Class RT_Asset_Module does not have method save_meta_boxesv' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'custom_post_status_rendar' ), 'Class RT_Asset_Module does not have method custom_post_status_rendar' );

		$this->assertTrue( method_exists( $this->rtassetModule, 'edit_custom_columns' ), 'Class RT_Asset_Module does not have method edit_custom_columns' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'sortable_column' ), 'Class RT_Asset_Module does not have method sortable_column' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'manage_custom_columns' ), 'Class RT_Asset_Module does not have method manage_custom_columns' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'remove_meta_boxes' ), 'Class RT_Asset_Module does not have method remove_meta_boxes' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'add_meta_boxes' ), 'Class RT_Asset_Module does not have method add_meta_boxes' );
		$this->assertTrue( method_exists( $this->rtassetModule, 'save_rt_assets_meta_boxes' ), 'Class RT_Asset_Module does not have method save_rt_assets_meta_boxes' );
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

	/**
	 * Test edit_custom_columns
	 */
	function test_edit_custom_columns(){
		$this->assertEquals(
			array(
				'cb' => 'cb',
				'rtasset_asset_id' => '<span class="assetid_head tips" data-tip="Unique ID">ID</span>',
				'title' => 'title',
				'rtasset_asset_status' => '<span class="status_head tips" data-tip="Status">Status</span>',
				'rtasset_assignee' => '<span class="assignee_head tips" data-tip="Assignee">Assigned To</span>',
				'taxonomy-rt_device-type' => 'taxonomy-rt_device-type',
				'comments' => 'comments',
				'date' => 'date',
			),
			$this->rtassetModule->edit_custom_columns( array(
				'title' => 'title',
				'date' => 'date',
				'comments' => 'comments',
				'taxonomy-rt_device-type' => 'taxonomy-rt_device-type',
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
				'taxonomy-rt_device-type' => 'rt_device-type',
				'rtasset_asset_id' => 'post_id',
			),
			$this->rtassetModule->sortable_column( array(
				'title' => 'title',
			) )
		);
	}

}
 