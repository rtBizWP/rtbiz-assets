<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

/**
 * Description of class-rt-entity
 *
 * @author udit
 */
if ( ! class_exists( 'RT_Asset_Entity' ) ) {

	/**
	 * Class RT_Asset_Entity
	 *
	 * An abstract class for RT_Bundle & RT_Assets - Core Modules of rtBiz-Assets.
	 * This will handle most of the functionalities of these two entities.
	 *
	 * If at all any individual entity wants to change the behavior for itself
	 * then it will override that particular method in the its child class
	 */
	abstract class RT_Asset_Entity {

		/**
		 * This array will hold all the post types that are meant to be connected with Bundle / Asset
		 * Other plugin addons will register their useful post type here in the array and accordingly will be connected
		 * with person / organization via Posts 2 Posts
		 *
		 * @var array
		 */
		public $enabled_post_types = array();

		/**
		 * @var - string used in mail subject title - to detect whether it's a Assets mail or not. So no translation
		 */
		public  $name = 'Assets';

		/**
		 * @var - Entity Core Post Type (Bundle / Asset)
		 */
		public $post_type_slug;

		/**
		 * @var - Post Type Labels (Bundle / Asset)
		 */
		public $labels;

		/**
		 * @var - Post statuses Labels (Bundle / Asset)
		 */
		public $statuses;

		/**
		 * @param $post_type_slug
		 */
		public function __construct( $post_type ) {
			$this->post_type_slug = $post_type;

			$this->get_custom_statuses();
			$this->hooks();
		}

		/**
		 * get rtbiz-Assets CPT [ Asset ] statuses
		 *
		 * @since rt-Assets 0.1
		 *
		 * @return array
		 */
		function get_custom_statuses() {
			$this->statuses = array(
				array(
					'slug'        => 'asset-assigned',
					'name'        => __( 'Assigned', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Device healthy and is assigned to a user.', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 3px; background: #FDD7E4; color: red; border: 1px solid red; border-radius: 5px;margin-top:15px;display:inline-block;',
				),
				array(
					'slug'        => 'asset-unassigned',
					'name'        => __( 'Unassigned', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Device healthy and is not assigned to any user. ', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 3px; background: #99FF99; color: #006600; border: 1px solid #006600; border-radius: 5px;margin-top:15px;display:inline-block;',
				),
				array(
					'slug'        => 'asset-faulty',
					'name'        => __( 'Faulty', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Unassigned Device, not healthy, needs replacement', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 3px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;margin-top:15px;display:inline-block;',
				),
				array(
					'slug'        => 'asset-needfix',
					'name'        => __( 'NeedFix', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Assigned Device, not healthy, needs replacement', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 3px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;margin-top:15px;display:inline-block;',
				),
				array(
					'slug'        => 'asset-expired',
					'name'        => __( 'Expired', RT_ASSET_TEXT_DOMAIN ),
					'description' => __( 'Device is out of warranty', RT_ASSET_TEXT_DOMAIN ),
					'style'       => 'padding: 3px; background: #CCCCCC; color: #404040; border: 1px solid #404040; border-radius: 5px;margin-top:15px;display:inline-block;',
				),
			);

			return $this->statuses;
		}

		/**
		 *  Register Rt_Entity Core Post Type
		 */
		function init_entity() {
			$this->register_post_type( $this->post_type_slug, $this->labels );
			foreach ( $this->statuses as $status ) {
				$this->register_custom_statuses( $status );
			}
		}

		/**
		 * Register CPT ( Assets )
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $name, $labels
		 *
		 * @return object|\WP_Error
		 */

		function register_post_type( $name, $labels = array() ) {
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true, // Show the UI in admin panel
				'show_in_nav_menus' => false,
				'show_in_menu' => false,
				'show_in_admin_bar' => false,
				'has_archive'        => true,
				'rewrite'            => array(
					'slug'       => strtolower( $this->labels['name'] ),
					'with_front' => false,
				),
				'supports'           => array( 'title', 'editor', 'comments', 'revisions', 'thumbnail', ),
				'capability_type'    => $this->post_type_slug,
			);
			register_post_type( $name, $args );
		}

		/**
		 * Register Custom statuses for CPT ( Assets )
		 *
		 * @since rt-Assets 0.1
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
		 *  Actions/Filtes used by Rt_Entity
		 */
		function hooks() {
			add_action( 'init', array( $this, 'init_entity' ) );

			add_action( 'wp_before_admin_bar_render', array( $this, 'assets_chnage_action_publish_update' ), 11 );

			// CPT List View
			add_filter( 'manage_edit-' . $this->post_type_slug . '_columns', array( $this, 'edit_custom_columns' ) );
			add_filter( 'manage_edit-' . $this->post_type_slug . '_sortable_columns', array( $this, 'sortable_column' ) );
			add_action( 'manage_' . $this->post_type_slug . '_posts_custom_column', array( $this, 'manage_custom_columns' ), 2 );

			// CPT Edit/Add View
			add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
			add_action( 'rt_asset_process_' . $this->post_type_slug . '_meta', array( $this, 'save_rt_assets_meta_boxes' ), 10, 2 );

			add_action( 'wp_before_admin_bar_render', array( $this, 'custom_post_status_rendar' ), 10 );
		}

		/**
		 * Change the publish action to update on Cpt-assets add/edit page
		 *
		 * @since rt-Assets 0.1
		 *
		 * @global type $pagenow
		 * @global type $post
		 */
		function assets_chnage_action_publish_update() {
			global $pagenow, $post;
			if ( get_post_type() == $this->post_type_slug && ( 'post.php' === $pagenow || 'edit.php' === $pagenow || 'post-new.php' === $pagenow || 'edit' == ( isset( $_GET['action'] ) && $_GET['action'] ) ) ) {
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
		 * Save custom meta boxes Values on Edit post View for Assets
		 *
		 * @since  rt-Assets 0.1
		 *
		 * @param $post_id
		 * @param $post
		 */
		public function save_meta_boxes( $post_id, $post ) {
			//global $rt_hd_module;
			// $post_id and $post are required
			if ( empty( $post_id ) || empty( $post ) ) {
				return;
			}

			// Dont' save meta boxes for revisions or autosaves
			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
			if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}

			// Check user has permission to edit
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check the post type
			if ( $post->post_type != $this->post_type_slug ) {
				return;
			}

			do_action( 'rt_asset_process_' . $this->post_type_slug . '_meta', $post_id, $post );;
			if ( 'trash' == $post->post_status ) {

				$url = add_query_arg( array( 'post_type' => $this->post_type_slug ), admin_url( 'edit.php' ) );
				wp_safe_redirect( $url );
				die();
			}

		}

		/**
		 * Render UI for custom post status
		 *
		 * @since rt-Assets 0.1
		 */
		public function custom_post_status_rendar() {
			global $post, $pagenow;
			$flag = false;
			if ( isset( $post ) && ! empty( $post ) && $post->post_type === $this->post_type_slug ) {
				if ( 'edit.php' == $pagenow || 'post-new.php' == $pagenow ) {
					$flag = true;
				}
			}
			if ( isset( $post ) && ! empty( $post ) && 'post.php' == $pagenow && get_post_type( $post->ID ) === $this->post_type_slug ) {
				$flag = true;
			}
			if ( $flag ) {
				$option      = '';
				foreach ( $this->statuses as $status ) {
					if ( $post->post_status == $status['slug'] ) {
						$complete = " selected='selected'";
					} else {
						$complete = '';
					}
					$option .= "<option value='" . $status['slug'] . "' " . $complete . '>' . $status['name'] . '</option>';
				}

				if ( $post->post_status == 'draft' ) {
					$complete = " selected='selected'";
				} else {
					$complete = '';
				}
				$option .= "<option value='draft' " . $complete . '>Draft</option>';

				echo '<script>
                        jQuery(document).ready(function($) {
                            $("select#post_status").html("' . balanceTags( $option ) . '");
                            $(".inline-edit-status select").html("' . balanceTags( $option ) . '");

                            $(document).on("change","#rthd_post_status",function(){
                                $("#post_status").val($(this).val());
                            });
                               });
                        </script>';
			}
		}

	}

}
