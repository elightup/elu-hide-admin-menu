<?php
// Load plugin textdomain
add_action( 'plugins_loaded', 'ham_load_textdomain' );

/**
 * Load plugin textdomain
 */
function ham_load_textdomain() {
	load_plugin_textdomain( 'elu-hide-admin-menu', false, basename( HAM_DIR ) . '/languages/' );
}

/**
 * Remove in an array all elements which are arrays
 * Used in array_intersection() to not generate PHP notice
 *
 * @see https://bugs.php.net/bug.php?id=60198
 *
 * @param array $var
 *
 * @return array
 */
function ham_normalize_array( $var ) {
	foreach ( $var as $k => $v ) {
		if ( is_array( $v ) ) {
			unset( $var[ $k ] );
		}
	}

	return $var;
}

/**
 * Convert user level to capability
 * Some BAD plugins/themes still use user level to add admin page
 *
 * @param int $level
 *
 * @return string
 */
function ham_user_level_to_capability( $level ) {
	$conversion = array(
		0  => 'read',                  // Subscriber
		1  => 'edit_posts',            // Contributor
		2  => 'publish_posts',         // Author
		3  => 'edit_pages',            // Editor
		4  => 'edit_pages',            // Editor
		5  => 'edit_pages',            // Editor
		6  => 'edit_pages',            // Editor
		7  => 'edit_pages',            // Editor
		8  => 'manage_options',        // Administrator
		9  => 'manage_options',        // Administrator
		10 => 'manage_options',        // Administrator
	);

	return $conversion[ $level ];
}
