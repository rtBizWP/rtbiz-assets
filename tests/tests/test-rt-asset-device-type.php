<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 4/12/14
 * Time: 12:48 PM
 */

class test_RT_Asset_Device_Type extends RT_WP_TestCase {
	var $rtassetDevicetype;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtassetDevicetype = new RT_Asset_Device_Type();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtassetDevicetype, 'get_custom_labels' ), 'Class RT_Asset_Device_Type does not have method get_custom_labels' );
		$this->assertTrue( method_exists( $this->rtassetDevicetype, 'hook' ), 'Class RT_Asset_Device_Type does not have method hook' );
		$this->assertTrue( method_exists( $this->rtassetDevicetype, 'register_device_type' ), 'Class RT_Asset_Device_Type does not have method register_device_type' );
		$this->assertTrue( method_exists( $this->rtassetDevicetype, 'add_stock_column_header' ), 'Class RT_Asset_Device_Type does not have method add_stock_column_header' );
		$this->assertTrue( method_exists( $this->rtassetDevicetype, 'add_stock_column_body' ), 'Class RT_Asset_Device_Type does not have method add_stock_column_body' );
		$this->assertTrue( method_exists( $this->rtassetDevicetype, 'get_stock' ), 'Class RT_Asset_Device_Type does not have method get_stock' );
	}

	/**
	 * Ensure that required Class exist
	 */
	function test_check_class_exist() {
		$this->assertTrue( class_exists( 'RT_Asset_Module' ), 'Class RT_Asset_Module does not exist' );
	}

	/**
	 * Test get_custom_labels
	 */
	function test_get_custom_labels(){
		$this->assertEquals(
			array(
				'name' => 'Device Type',
				'search_items' => 'Search Device Type',
				'all_items' => 'All Device Types',
				'edit_item' => 'Edit Device Type',
				'update_item' => 'Update Device Type',
				'add_new_item' => 'Add New Device Type',
				'new_item_name' => 'New Device Type',
				'menu_name' => 'Device Types',
				'choose_from_most_used' => 'Choose from the most used Device Types',
			),
			$this->rtassetDevicetype->get_custom_labels( )
		);
	}

	/**
	 * Test register_device_type
	 */
	function test_register_device_type(){
		$this->rtassetDevicetype->register_device_type();
		$this->assertTrue( taxonomy_exists( 'rt_device-type' ) );
	}

	/**
	 * Test add_stock_column_header
	 */
	function test_add_stock_column_header(){
		$this->assertEquals(
			array(
				'stock' => 'In Stock',
				'prefix' => 'Prefix',
				'nextid' => 'Next ID',
			),
			$this->rtassetDevicetype->add_stock_column_header( array() )
		);
	}

	/**
	 * Test get_stock
	 */
	function  test_get_stock(){
		$term_id = $this->factory->term->create( array( 'taxonomy' => 'rt_device-type' ) );
		$term = get_term_by( 'id', $term_id, 'rt_device-type' );
		$post = $this->factory->post->create( array( 'post_type' => RT_Asset_Module::$post_type, 'post_status' => 'asset-assigned' ) );
		wp_set_post_terms( $post, array( $term_id ), 'rt_device-type' );
		$this->assertEquals( 0, $this->rtassetDevicetype->get_stock( $term->name ) );
		$post2 = $this->factory->post->create( array( 'post_type' => RT_Asset_Module::$post_type, 'post_status' => 'asset-unassigned' ) );
		wp_set_post_terms( $post2, array( $term_id ), 'rt_device-type' );
		$post3 = $this->factory->post->create( array( 'post_type' => RT_Asset_Module::$post_type, 'post_status' => 'asset-unassigned' ) );
		wp_set_post_terms( $post3, array( $term_id ), 'rt_device-type' );
		$this->assertEquals( 2, $this->rtassetDevicetype->get_stock( $term->name ) );
		$post4 = $this->factory->post->create( array( 'post_type' => RT_Asset_Module::$post_type, 'post_status' => 'asset-faulty' ) );
		wp_set_post_terms( $post4, array( $term_id ), 'rt_device-type' );
		$this->assertEquals( 2, $this->rtassetDevicetype->get_stock( $term->name ) );
	}
}
 