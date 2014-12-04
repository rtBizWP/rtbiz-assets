<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 4/12/14
 * Time: 12:24 PM
 */

class test_RT_Asset_CPT_Assets extends RT_WP_TestCase {
	var $rtassetCptAssets;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtassetCptAssets = new RT_Asset_CPT_Assets();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtassetCptAssets, 'edit_custom_columns' ), 'Class RT_Asset_CPT_Assets does not have method edit_custom_columns' );
		$this->assertTrue( method_exists( $this->rtassetCptAssets, 'sortable_column' ), 'Class RT_Asset_CPT_Assets does not have method sortable_column' );
		$this->assertTrue( method_exists( $this->rtassetCptAssets, 'manage_custom_columns' ), 'Class RT_Asset_CPT_Assets does not have method manage_custom_columns' );
		$this->assertTrue( method_exists( $this->rtassetCptAssets, 'remove_meta_boxes' ), 'Class RT_Asset_CPT_Assets does not have method remove_meta_boxes' );
		$this->assertTrue( method_exists( $this->rtassetCptAssets, 'add_meta_boxes' ), 'Class RT_Asset_CPT_Assets does not have method add_meta_boxes' );
		$this->assertTrue( method_exists( $this->rtassetCptAssets, 'save_meta_boxes' ), 'Class RT_Asset_CPT_Assets does not have method save_meta_boxes' );

		$this->assertTrue( class_exists( 'RT_Meta_Box_Assets_Info' ), 'Class RT_Meta_Box_Assets_Info does not found' );
		$this->assertTrue( method_exists( 'RT_Meta_Box_Assets_Info', 'save' ), 'Class RT_Meta_Box_Assets_Info does not have method save' );
		$this->assertTrue( method_exists( 'RT_Meta_Box_Assets_Info', 'ui' ), 'Class RT_Meta_Box_Assets_Info does not have method ui' );
		$this->assertTrue( method_exists( 'RT_Meta_Box_Assets_Info', 'custom_post_status_rendar' ), 'Class RT_Meta_Box_Assets_Info does not have method custom_post_status_rendar' );

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
				'taxonomy-rt_device-type' => 'taxonomy-rt_device-type',
				'comments' => 'comments',
				'date' => 'date',
			),
			$this->rtassetCptAssets->edit_custom_columns( array(
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
				'taxonomy-rt_device-type' => 'rt_device-type',
				'rtasset_asset_id' => 'post_id',
			),
			$this->rtassetCptAssets->sortable_column( array(
				'title' => 'title',
			) )
		);
	}
}
 