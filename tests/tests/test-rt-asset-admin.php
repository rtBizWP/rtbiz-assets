<?php
/**
 * Created by PhpStorm.
 * User: sai
 * Date: 4/12/14
 * Time: 12:13 PM
 */

class test_RT_Asset_Admin extends RT_WP_TestCase {

	var $rtassetAdmin;

	/**
	 * Setup Class Object and Parent Test Suite
	 *
	 */
	function setUp() {
		parent::setUp();
		$this->rtassetAdmin = new RT_Asset_Admin();
	}

	/**
	 * Ensure that required function exist
	 */
	function  test_check_function() {
		$this->assertTrue( method_exists( $this->rtassetAdmin, 'load_styles_scripts' ), 'Class RT_Asset_Admin does not have method load_styles_scripts' );
		$this->assertTrue( method_exists( $this->rtassetAdmin, 'localize_scripts' ), 'Class RT_Asset_Admin does not have method localize_scripts' );
	}

}
 