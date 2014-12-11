<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RT_Asset_Device_Type' ) ) {
	/**
	 * Class RT_Asset_Device_Type
	 * Device Type for Assets
	 *
	 * @since rt-Assets 0.1
	 *
	 * @author Dipesh
	 */
	class RT_Asset_Device_Type {

		/**
		 * @var string Stores Post Type
		 *
		 * @since rt-Assets 0.1
		 */
		var $slug = 'device_type';

		/**
		 * @var array Labels for Device Type taxonomy
		 *
		 * @since rt-Assets 0.1
		 */
		var $labels = array();

		/**
		 * Construct
		 *
		 * @since rt-Assets 0.1
		 */
		public function __construct() {
			$this->get_custom_labels();
			$this->hook();
		}

		/**
		 * get Device Type taxonomy labels
		 *
		 * @since rt-Assets 0.1
		 *
		 * @return array
		 */
		function get_custom_labels() {
			$this->labels = array(
				'name' => __( 'Device Type', RT_ASSET_TEXT_DOMAIN ),
				'search_items' => __( 'Search Device Type', RT_ASSET_TEXT_DOMAIN ),
				'all_items' => __( 'All Device Types', RT_ASSET_TEXT_DOMAIN ),
				'edit_item' => __( 'Edit Device Type', RT_ASSET_TEXT_DOMAIN ),
				'update_item' => __( 'Update Device Type', RT_ASSET_TEXT_DOMAIN ),
				'add_new_item' => __( 'Add New Device Type', RT_ASSET_TEXT_DOMAIN ),
				'new_item_name' => __( 'New Device Type', RT_ASSET_TEXT_DOMAIN ),
				'menu_name' => __( 'Device Types', RT_ASSET_TEXT_DOMAIN ),
				'choose_from_most_used' => __( 'Choose from the most used Device Types', RT_ASSET_TEXT_DOMAIN ),
			);
			return $this->labels;
		}

		/**
		 * Hook
		 *
		 * @since rt-Assets 0.1
		 */
		public function hook() {
			add_action( 'init', array( $this, 'register_device_type' ) );

			add_filter( 'manage_edit-' . rtasset_attribute_taxonomy_name( $this->slug ) . '_columns', array( $this, 'add_stock_column_header' ) );
			add_filter( 'manage_' . rtasset_attribute_taxonomy_name( $this->slug ) . '_custom_column', array( $this, 'add_stock_column_body' ), 10, 3 );

			add_action( rtasset_attribute_taxonomy_name( $this->slug ) . '_edit_form_fields', array( $this, 'add_taxonomy_custom_fields' ), 10, 2 );
			add_action( rtasset_attribute_taxonomy_name( $this->slug ) . '_add_form_fields', array( $this, 'add_taxonomy_custom_fields' ), 10, 2 );

			add_action( 'edited_' . rtasset_attribute_taxonomy_name( $this->slug ), array( $this, 'save_taxonomy_custom_fields' ), 10, 2 );
			add_action( 'created_' . rtasset_attribute_taxonomy_name( $this->slug ), array( $this, 'save_taxonomy_custom_fields' ), 10, 2 );

			add_action( 'delete_' . rtasset_attribute_taxonomy_name( $this->slug ), array( $this, 'delete_taxonomy_custom_fields' ), 10, 2 );
		}

		/**
		 * Register Device type taxonomy for Assets
		 *
		 * @since rt-Assets 0.1
		 */
		public function register_device_type() {

			$editor_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'editor' );
			$author_cap = rt_biz_get_access_role_cap( RT_ASSET_TEXT_DOMAIN, 'author' );

			register_taxonomy( rtasset_attribute_taxonomy_name( $this->slug ), array( RT_Asset_Module::$post_type ), array(
				'hierarchical' => true,
				'labels' => $this->labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'query_var' => true,
				'update_count_callback' => 'rtasset_update_post_term_count',
				'rewrite' => array( 'slug' => rtasset_attribute_taxonomy_name( $this->slug ) ),
				'capabilities' => array(
					'manage_terms' => $editor_cap,
					'edit_terms' => $editor_cap,
					'delete_terms' => $editor_cap,
					'assign_terms' => $author_cap,
				),
			));
		}

		/**
		 * Method for save device type for post
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $post_id
		 * @param $newAsset
		 */
		function save_device_type( $post_id, $newAsset ) {
			if ( ! isset( $newAsset[ $this->slug ] ) ) {
				$newAsset[ $this->slug ] = array();
			}
			$device_types = array_map( 'intval', $newAsset[ $this->slug ] );
			$device_types = array_unique( $device_types );
			wp_set_post_terms( $post_id, $device_types, rtasset_attribute_taxonomy_name( $this->slug ) );
		}

		/**
		 * Method to add custom columns header on list view
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function add_stock_column_header( $columns ) {
			$columns['stock'] = __( 'In Stock' );
			$columns['prefix'] = __( 'Prefix' );
			$columns['nextid'] = __( 'Next ID' );
			return $columns;
		}

		/**
		 * Method to add custom columns body on list view
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $out
		 * @param $column_name
		 * @param $devicetype_id
		 *
		 * @return string
		 */
		function add_stock_column_body( $out, $column_name, $devicetype_id ) {
			$device_type = get_term( $devicetype_id, rtasset_attribute_taxonomy_name( $this->slug ) );
			switch ( $column_name ) {
				case 'stock':
					$stock = $this->get_stock( $device_type->slug );
					$out .= "<a href='edit.php?rt_device-type=" . $device_type->slug . '&post_type=' . RT_Asset_Module::$post_type . "&post_status=asset-unassigned'>" . $stock . '</a>';
					break;
				case 'prefix':
					$unique_prefix = Rt_Lib_Taxonomy_Metadata\get_term_meta( $devicetype_id, rtasset_attribute_taxonomy_name( $this->slug ) . '_unique_prefix', true );
					$out .= $unique_prefix;
					break;
				case 'nextid':
					$next_id = Rt_Lib_Taxonomy_Metadata\get_term_meta( $devicetype_id, rtasset_attribute_taxonomy_name( $this->slug ) . '_next_id', true );
					$out .= $next_id;
					break;
				default:
					break;
			}

			return $out;
		}

		/**
		 * Method to get stock of devices
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $device_type
		 *
		 * @return mixed
		 */
		function get_stock( $device_type ){
			$args       = array(
				'post_type' => RT_Asset_Module::$post_type,
				rtasset_attribute_taxonomy_name( $this->slug ) => $device_type,
				'post_status' => 'asset-unassigned',
			);
			$stockquery = new WP_Query( $args );
			return $stockquery->found_posts;
		}

		/**
		 * Method to add custom field for taxonomy
		 *
		 * @since rt-Assets 0.1
		 *
		 * @param $tag
		 *
		 * @return mixed
		 */
		function add_taxonomy_custom_fields( $tag ) {
			$unique_prefix = '';
			if ( is_object( $tag ) ){
				$term_id = $tag->term_id;
				$unique_prefix = Rt_Lib_Taxonomy_Metadata\get_term_meta( $term_id, $tag->taxonomy . '_unique_prefix', true );
			}?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="unique_prefix"><?php _e( 'Prefix For Unique ID:' ); ?></label>
				</th>
				<td>
					<input type="text" name="term_meta[unique_prefix]" id="unique_prefix" value="<?php echo esc_attr( $unique_prefix ?  $unique_prefix  : '' ); ?>"><br />
					<span class="description"><?php _e( "if it's blank it will created automatically  [ First three characters of taxonomy name ] <br/> once it was created you can't change it." ); ?></span>
				</td>
			</tr> <?php
		}

		function save_taxonomy_custom_fields( $term_id ) {
			if ( isset( $_POST['term_meta'] ) ) {
				if ( empty( $_POST['name'] ) ){
					$_POST['name'] = $_POST['tag-name'];
				}
				if ( empty( $_POST['term_meta']['unique_prefix'] ) ){
					$_POST['term_meta']['unique_prefix'] = trim( substr( $_POST['name'], 0, 3 ) );
				}

				$unique_prefix = Rt_Lib_Taxonomy_Metadata\get_term_meta( $term_id, $_POST['taxonomy'] . '_unique_prefix', true );

				if ( $unique_prefix != $_POST['term_meta']['unique_prefix'] ){
					Rt_Lib_Taxonomy_Metadata\update_term_meta( $term_id, $_POST['taxonomy'] . '_unique_prefix', $_POST['term_meta']['unique_prefix'], $unique_prefix );
					if ( $_POST['action'] == 'editedtag' ){
						$args       = array(
							'post_type' => RT_Asset_Module::$post_type,
							rtasset_attribute_taxonomy_name( $this->slug ) => $_POST['slug'],
						);
						$asset_query = new WP_Query( $args );
						if ( $asset_query->have_posts() ) {
							while ( $asset_query->have_posts() ) {
								$asset_query->the_post();
								$old_unique_id = get_post_meta( get_the_ID(), '_rtbiz_asset_unique_id', true );
								$new_unique_id = str_replace( $unique_prefix, $_POST['term_meta']['unique_prefix'], $old_unique_id );
								update_post_meta( get_the_ID(), '_rtbiz_asset_unique_id', $new_unique_id, $old_unique_id );
							}
						}
					}
				}

				$next_id = Rt_Lib_Taxonomy_Metadata\get_term_meta( $term_id, $_POST['taxonomy'] . '_next_id', true );
				if ( empty( $next_id ) ){
					Rt_Lib_Taxonomy_Metadata\add_term_meta( $term_id, $_POST['taxonomy'] . '_next_id', '1' );
				}
			}
		}

		function delete_taxonomy_custom_fields( $term_id, $tt_id ){
			Rt_Lib_Taxonomy_Metadata\delete_term_meta( $term_id, rtasset_attribute_taxonomy_name( $this->slug ) . '_next_id' );
			Rt_Lib_Taxonomy_Metadata\delete_term_meta( $term_id, rtasset_attribute_taxonomy_name( $this->slug ) . '_unique_prefix' );
		}

	}
}