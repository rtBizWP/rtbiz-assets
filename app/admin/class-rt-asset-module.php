<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_Asset_Module' ) ) {
	/**
	 * Class RT_Asset_Module
	 * Register rtbiz-Assets CPT [ Ticket ] & statuses
	 * Define connection with other post type [ person, organization ]
	 *
	 * @since  0.1
	 *
	 * @author Dipesh
	 */
	class RT_Asset_Module {

		/**
		 * @var string Stores Post Type
		 *
		 * @since 0.1
		 */
		static $post_type = 'rtbiz_asset_assets';
		/**
		 * @var string used in mail subject title - to detect whether it's a Assets mail or not. So no translation
		 *
		 * @since 0.1
		 */
		static $name = 'Assets';
		/**
		 * @var array Labels for rtbiz-Assets CPT [ Ticket ]
		 *
		 * @since 0.1
		 */
		var $labels = array();
		/**
		 * @var array statuses for rtbiz-Assets CPT [ Ticket ]
		 *
		 * @since 0.1
		 */
		var $statuses = array();
		/**
		 * @var array Menu order for rtbiz-Assets
		 *
		 * @since 0.1
		 */
		var $custom_menu_order = array();

		/**
		 * initiate class local Variables
		 *
		 * @since 0.1
		 */
		public function __construct() {
			$this->get_custom_labels();
			$this->get_custom_statuses();
			$this->get_custom_menu_order();
			add_action( 'init', array( $this, 'init_asset' ) );
			$this->hooks();
		}

		/**
		 * get rtbiz-Assets CPT [ Ticket ] labels
		 *
		 * @since 0.1
		 *
		 * @return array
		 */
		function get_custom_labels() {
			$settings     = rtasset_get_redux_settings();
			$this->labels = array(
				'name'          => __( 'Asset', RT_ASSET_TEXT_DOMAIN ),
				'singular_name' => __( 'Asset', RT_ASSET_TEXT_DOMAIN ),
				'menu_name'     => isset( $settings['rtasset_menu_label'] ) ? $settings['rtasset_menu_label'] : 'rtAssets',
				'all_items'     => __( 'Assets', RT_ASSET_TEXT_DOMAIN ),
				'add_new'       => __( 'Add Asset', RT_ASSET_TEXT_DOMAIN ),
				'add_new_item'  => __( 'Add Asset', RT_ASSET_TEXT_DOMAIN ),
				'new_item'      => __( 'Add Asset', RT_ASSET_TEXT_DOMAIN ),
				'edit_item'     => __( 'Edit Asset', RT_ASSET_TEXT_DOMAIN ),
				'view_item'     => __( 'View Asset', RT_ASSET_TEXT_DOMAIN ),
				'search_items'  => __( 'Search Assets', RT_ASSET_TEXT_DOMAIN ),
			);

			return $this->labels;
		}

		/**
		 * get rtbiz-Assets CPT [ Asset ] statuses
		 *
		 * @since 0.1
		 *
		 * @return array
		 */
		function get_custom_statuses() {
			$this->statuses = array(
				array(
					'slug'        => 'asset-assigned',
					'name'        => __( 'Assigned', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Device healthy and is assigned to a user.', RT_ASSET_TEXT_DOMAIN ),
				    'style'       => 'padding: 5px; background: #FDD7E4; color: red; border: 1px solid red; border-radius: 5px;'
				),
				array(
					'slug'        => 'asset-unassigned',
					'name'        => __( 'Unassigned', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Device healthy and is not assigned to any user. ', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 5px; background: #99FF99; color: #006600; border: 1px solid #006600; border-radius: 5px;'
				),
				array(
					'slug'        => 'asset-faulty',
					'name'        => __( 'faulty', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Unassigned Device, not healthy, needs replacement', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 5px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;'
				),
				array(
					'slug'        => 'asset-needfix',
					'name'        => __( 'NeedFix', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Assigned Device, not healthy, needs replacement', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 5px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;'
				),
				array(
					'slug'        => 'asset-expired',
					'name'        => __( 'Expired', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Device is out of warranty', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 5px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;'
				),
			);

			return $this->statuses;
		}

		/**
		 * get menu order for rtbiz-Assets
		 *
		 * @since 0.1
		 */
		function get_custom_menu_order() {
			global $rt_hd_attributes;
			$this->custom_menu_order = array(
				'rtasset-' . self::$post_type . '-dashboard',
			);

			return $this->statuses;
		}

		/**
		 * register rtbiz-Assets CPT [ Asset ] & define connection with other post type [ person, organization ]
		 *
		 * @since 0.1
		 */
		function init_asset() {
			$menu_position = 41;
			$this->register_custom_post( $menu_position );

			foreach ( $this->statuses as $status ) {
				$this->register_custom_statuses( $status );
			}

			//rt_biz_register_person_connection( self::$post_type, $this->labels['name'] );

			//rt_biz_register_organization_connection( self::$post_type, $this->labels['name'] );

		}

		/**
		 * Register CPT ( ticket )
		 *
		 * @since 0.1
		 *
		 * @param $menu_position
		 *
		 * @return object|\WP_Error
		 */
		function register_custom_post( $menu_position ) {
			$settings = rtasset_get_redux_settings();

			$args = array(
				'labels'             => $this->labels,
				'public'             => true,
				'publicly_queryable' => true,
				'has_archive'        => true,
				'rewrite'            => array(
					'slug'       => strtolower( $this->labels['name'] ),
				    'with_front' => false,
				),
				'show_ui'            => true, // Show the UI in admin panel
				'menu_icon'          => $settings['rtasset_logo_url']['url'],
				'menu_position'      => $menu_position,
				'supports'           => array( 'title', 'editor', 'comments', 'revisions', 'thumbnail' ),
				'capability_type'    => self::$post_type,
			);

			return register_post_type( self::$post_type, $args );
		}

		/**
		 * Register Custom statuses for CPT ( ticket )
		 *
		 * @since 0.1
		 *
		 * @param $status
		 *
		 * @return array|object|string
		 */
		function register_custom_statuses( $status ) {

			return register_post_status( $status['slug'], array(
				'label'       => $status['name'],
				'public'      => true,
				'exclude_from_search' => false,
				'label_count' => _n_noop( "{$status['name']} <span class='count'>(%s)</span>", "{$status['name']} <span class='count'>(%s)</span>" ),
			) );

		}

		/**
		 * set hooks
		 *
		 * @since 0.1
		 */
		function hooks() {
			add_filter( 'custom_menu_order', array( $this, 'custom_pages_order' ) );

			add_action( 'wp_before_admin_bar_render', array( $this, 'ticket_chnage_action_publish_update' ), 11 );
		}

		/**
		 * Change the publish action to update on Cpt-ticket add/edit page
		 *
		 * @since 0.1
		 *
		 * @global type $pagenow
		 * @global type $post
		 */
		function ticket_chnage_action_publish_update() {
			global $pagenow, $post;
			if ( get_post_type() == self::$post_type && (  'post.php' === $pagenow ||'edit.php' === $pagenow || 'post-new.php' === $pagenow || 'edit' == ( isset( $_GET['action'] ) && $_GET['action'] ) ) ) {
				if ( ! isset( $post ) ) {
					return;
				}
				echo '
				<script>
				jQuery(document).ready(function($){
					$("#publishing-action").html("<span class=\"spinner\"> <\/span><input name=\"original_publish\" type=\"hidden\" id=\"original_publish\" value=\"Update\"><input type=\"submit\" id=\"save-publish\" class=\"button button-primary button-large\" value=\"Update\" ><\/input>");
					$(".save-post-status").click(function(){
						$("#publish").hide();
						$("#publishing-action").html("<span class=\"spinner\"><\/span><input name=\"original_publish\" type=\"hidden\" id=\"original_publish\" value=\"Update\"><input type=\"submit\" id=\"save-publish\" class=\"button button-primary button-large\" value=\"Update\" ><\/input>");
					});
					$("#save-publish").click(function(){
						$("#publish").click();
					});
					$("#post-status-select").removeClass("hide-if-js");
				});
				</script>';
			}
		}

		/**
		 * Customize menu item order
		 *
		 * @since 0.1
		 *
		 * @param $menu_order
		 *
		 * @return mixed
		 */
		function custom_pages_order( $menu_order ) {
			global $submenu;
			global $menu;
			if ( isset( $submenu[ 'edit.php?post_type=' . self::$post_type ] ) && ! empty( $submenu[ 'edit.php?post_type=' . self::$post_type ] ) ) {
				$module_menu = $submenu[ 'edit.php?post_type=' . self::$post_type ];
				unset( $submenu[ 'edit.php?post_type=' . self::$post_type ] );
				//unset($module_menu[5]);
				//unset($module_menu[10]);
				$new_index = 5;
				foreach ( $this->custom_menu_order as $item ) {
					foreach ( $module_menu as $p_key => $menu_item ) {
						$out = array_filter( $menu_item, function( $in ) { return true !== $in; } );
						if ( in_array( $item, $out ) ) {
							$submenu[ 'edit.php?post_type=' . self::$post_type ][ $new_index ] = $menu_item;
							unset( $module_menu[ $p_key ] );
							$new_index += 5;
							break;
						}
					}
				}
				foreach ( $module_menu as $p_key => $menu_item ) {
//					if ( $menu_item[2] != Redux_Framework_Helpdesk_Config::$page_slug ) {
//						$menu_item[0] = '--- ' . $menu_item[0];
//					}
					$submenu[ 'edit.php?post_type=' . self::$post_type ][ $new_index ] = $menu_item;
					unset( $module_menu[ $p_key ] );
					$new_index += 5;
				}
			}

			return $menu_order;
		}
	}
}