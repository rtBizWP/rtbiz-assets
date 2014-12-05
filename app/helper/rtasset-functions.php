<?php
/**
 * Helper functions for rt-assets
 * @author Dipesh
 */


function rtasset_sanitize_taxonomy_name( $taxonomy ) {
	$taxonomy = strtolower( stripslashes( strip_tags( $taxonomy ) ) );
	$taxonomy = preg_replace( '/&.+?;/', '', $taxonomy ); // Kill entities
	$taxonomy = str_replace( array( '.', '\'', '"' ), '', $taxonomy ); // Kill quotes and full stops.
	$taxonomy = str_replace( array( ' ', '_' ), '-', $taxonomy ); // Replace spaces and underscores.

	return $taxonomy;
}

function rtasset_attribute_taxonomy_name( $name ) {
	return 'rt_' . rtasset_sanitize_taxonomy_name( $name );
}

/**
 * Get user
 *
 * @return mixed
 *
 * @since rt-Helpdesk 0.1
 */
function get_rtasset_rtcamp_user() {
	$users = rt_biz_get_module_users( RT_ASSET_TEXT_DOMAIN );

	return $users;
}

function rtasset_update_post_term_count( $terms, $taxonomy ) {
	global $wpdb;

	$object_types = (array) $taxonomy->object_type;

	foreach ( $object_types as &$object_type ) {
		list( $object_type ) = explode( ':', $object_type );
	}

	$object_types = array_unique( $object_types );

	if ( false !== ( $check_attachments = array_search( 'attachment', $object_types ) ) ) {
		unset( $object_types[ $check_attachments ] );
		$check_attachments = true;
	}

	if ( $object_types ) {
		$object_types = esc_sql( array_filter( $object_types, 'post_type_exists' ) );
	}

	foreach ( (array) $terms as $term ) {
		$count = 0;

		// Attachments can be 'inherit' status, we need to base count off the parent's status if so
		if ( $check_attachments ) {
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id  AND post_type = 'attachment' AND term_taxonomy_id = %d", $term ) );
		}

		if ( $object_types ) {
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id  AND post_type IN ('" . implode( "', '", $object_types ) . "') AND term_taxonomy_id = %d", $term ) );
		}

		do_action( 'edit_term_taxonomy', $term, $taxonomy );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
		do_action( 'edited_term_taxonomy', $term, $taxonomy );
	}
}

// Setting ApI
function rtasset_get_redux_settings() {
	if ( ! isset( $GLOBALS['redux_assets_settings'] ) ) {
		$GLOBALS['redux_assets_settings'] = get_option( 'redux_assets_settings', array() );
	}

	return $GLOBALS['redux_assets_settings'];
}

/**
 * check for rt biz dependency and if it does not find any single dependency then it returns false
 *
 * @since 0.1
 *
 * @return bool
 */
function rtasset_check_plugin_dependecy() {

	global $rtasset_plugin_check;
	$rtasset_plugin_check = array(
		'rtbiz' => array(
			'project_type' => 'all',
			'name' => esc_html__( 'WordPress for Business.', RT_ASSET_TEXT_DOMAIN ),
			'active' => class_exists( 'Rt_Biz' ),
			'filename' => 'index.php',
		),
	);

	$flag = true;

	if ( ! class_exists( 'Rt_Biz' ) || ! did_action( 'rt_biz_init' ) ) {
		$flag = false;
	}

	if ( ! $flag ) {
		add_action( 'admin_enqueue_scripts', 'rtasset_plugin_check_enque_js' );
		add_action( 'wp_ajax_rtasset_activate_plugin', 'rtasset_activate_plugin_ajax' );
		add_action( 'admin_notices', 'rtasset_admin_notice_dependency_not_installed' );
	}

	return $flag;
}


function rtasset_plugin_check_enque_js() {
	wp_enqueue_script( 'rtbiz-asset-plugin-check', RT_ASSET_URL . 'app/assets/javascripts/rtasset_plugin_check.js', '', false, true );
	wp_localize_script( 'rtbiz-asset-plugin-check', 'rtasset_ajax_url', admin_url( 'admin-ajax.php' ) );
}

/**
 * if rtbiz plugin is not installed or activated it gives notification to user to do so.
 *
 * @since 0.1
 */
function rtasset_admin_notice_dependency_not_installed() {
	if ( ! rtasset_is_plugin_installed( 'rtbiz' ) ) {
		$path  = rtasset_get_path_for_plugin( 'rtbiz' );
		?>
		<div class="error rtasset-plugin-not-installed-error">
			<p>
				<b><?php _e( 'rtBiz Assets:' ) ?></b> <?php _e( esc_attr( $path ) . ' plugin is not found on this site. Please install & activate it in order to use this plugin.', RT_ASSET_TEXT_DOMAIN ); ?>
			</p>
		</div>
	<?php
	} else {
		if ( rtasset_is_plugin_installed( 'rtbiz' ) && ! rtasset_is_plugin_active( 'rtbiz' ) ) {
			$path  = rtasset_get_path_for_plugin( 'rtbiz' );
			$nonce = wp_create_nonce( 'rtasset_activate_plugin_' . $path );
			?>
			<div class="error rtasset-plugin-not-installed-error">
				<p><b><?php _e( 'rtBiz Assets:' ) ?></b> <?php _e( 'Click' ) ?>
					<a href="#" onclick="activate_rtasset_plugin('<?php echo esc_attr( $path ); ?>','rtasset_activate_plugin','<?php echo esc_attr( $nonce ); ?>')">here</a> <?php _e( 'to activate rtBiz.', 'rtbiz' ) ?>
				</p>
			</div>
		<?php
		}
	}
}

function rtasset_get_path_for_plugin( $slug ) {
	global $rtasset_plugin_check;
	$filename = ( ! empty( $rtasset_plugin_check[ $slug ]['filename'] ) ) ? $rtasset_plugin_check[ $slug ]['filename'] : $slug . '.php';

	return $slug . '/' . $filename;
}

function rtasset_is_plugin_active( $slug ) {
	global $rtasset_plugin_check;
	if ( empty( $rtasset_plugin_check[ $slug ] ) ) {
		return false;
	}

	return $rtasset_plugin_check[ $slug ]['active'];
}

function rtasset_is_plugin_installed( $slug ) {
	global $rtasset_plugin_check;
	if ( empty( $rtasset_plugin_check[ $slug ] ) ) {
		return false;
	}

	if ( rtasset_is_plugin_active( $slug ) || file_exists( WP_PLUGIN_DIR . '/' . rtasset_get_path_for_plugin( $slug ) ) ) {
		return true;
	}

	return false;
}

/**
 * ajax call for active plugin
 */
function rtasset_activate_plugin_ajax() {
	if ( empty( $_POST['path'] ) ) {
		die( __( 'ERROR: No slug was passed to the AJAX callback.', 'rt_biz' ) );
	}
	check_ajax_referer( 'rtasset_activate_plugin_' . $_POST['path'] );

	if ( ! current_user_can( 'activate_plugins' ) ) {
		die( __( 'ERROR: You lack permissions to activate plugins.', 'rt_biz' ) );
	}

	rtasset_activate_plugin( $_POST['path'] );

	echo 'true';
	die();
}

/**
 * @param $plugin_path
 * ajax call for active plugin calls this function to active plugin
 */
function rtasset_activate_plugin( $plugin_path ) {

	$activate_result = activate_plugin( $plugin_path );
	if ( is_wp_error( $activate_result ) ) {
		die( sprintf( __( 'ERROR: Failed to activate plugin: %s', 'rt_biz' ), $activate_result->get_error_message() ) );
	}
}
