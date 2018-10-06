<?php
global $ham_nodes, $wp_roles, $roles, $ham_displayed, $alternate, $option;

$option = get_option( HAM_SETTING_BAR );
if ( empty( $option ) ) {
	$option = array();
}

$roles = $wp_roles->get_names();

// For rows
$alternate = true;

// Save displayed items
$ham_displayed = array();

// Setup children for nodes
foreach ( $ham_nodes as $node ) {
	if ( false == $node->parent || ! isset( $ham_nodes[ $node->parent ] ) ) {
		continue;
	}

	$parent = &$ham_nodes[ $node->parent ];
	if ( ! isset( $parent->children ) ) {
		$parent->children = array();
	}

	$parent->children[] = $node;
}

// Remove children nodes
foreach ( $ham_nodes as $k => $node ) {
	if ( false != $node->parent ) {
		unset( $ham_nodes[ $k ] );
	}
}
?>

	<div class="wrap">
		<h2><?php _e( 'Hide Admin Bar', 'elu-hide-admin-menu' ); ?></h2>

		<div class="notice notice-warning">
			<p><?php esc_html_e( 'Checking a checkbox disables the access to the menu item for the corresponding role.', 'elu-hide-admin-menu' ); ?></p>
			<p><?php esc_html_e( 'If no checkbox is available, it means the corresponding role cannot access the menu item by default (and you can\'t enable it with this plugin either).', 'elu-hide-admin-menu' ); ?></p>
		</div>

		<form method="post" action="options.php">

			<?php settings_fields( HAM_SETTING_BAR ); ?>

			<table class="widefat" id="ham-selection">
				<thead>
				<tr>
					<th><?php _e( 'Menu Item', 'elu-hide-admin-menu' ); ?></th>
					<?php
					foreach ( $roles as $role => $name ) {
						echo "<th class='ham-center'>{$name}</th>";
					}
					?>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $ham_nodes as $k => $node ) {
					ham_show_admin_bar_item( $node );
				}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th><?php _e( 'Menu Item', 'elu-hide-admin-menu' ); ?></th>
					<?php
					foreach ( $roles as $role => $name ) {
						echo "<th class='ham-center'>{$name}</th>";
					}
					?>
				</tr>
				</tfoot>
			</table>
			<p class="submit">
				<?php submit_button( __( 'Save Settings', 'elu-hide-admin-menu' ), 'primary', 'submit', false ); ?>
				<?php submit_button( __( 'Reset', 'elu-hide-admin-menu' ), 'primary', 'reset', false ); ?>
			</p>
		</form>
	</div>

<?php
/**
 * Show a table row for an item
 *
 * @param object $node
 * @param int    $depth
 */
function ham_show_admin_bar_item( $node, $depth = 0 ) {
	global $roles, $alternate, $ham_displayed, $option;

	// Check if item is displayed
	// If not, then not increase $depth
	$is_displayed = false;

	// Display item only it has title
	// And it hasn't been displayed
	if ( ! $node->group && ! in_array( $node->id, $ham_displayed ) ) {
		$class = "ham-item-level-{$depth}";
		if ( $alternate ) {
			$class .= ' alternate';
		}

		// Get menu item title
		// Treat specially for special items
		switch ( $node->id ) {
			case 'wp-logo':
				$title = __( 'WordPress Logo', 'elu-hide-admin-menu' );
				break;
			case 'comments':
				$title = __( 'Comments', 'elu-hide-admin-menu' );
				break;
			case 'updates':
				$title = __( 'Updates', 'elu-hide-admin-menu' );
				break;
			case 'my-account':
				$title = __( 'My Account', 'elu-hide-admin-menu' );
				break;
			case 'user-info':
				$title = __( 'User Info', 'elu-hide-admin-menu' );
				break;
			default:
				$title = wp_strip_all_tags( $node->title );
		}

		if ( $depth ) {
			$title = "— {$title}";
		}

		echo "
			<tr class='{$class}' data-id='{$node->id}' data-parent='{$node->parent}'>
				<td>{$title}</td>
		";

		$checked_roles = empty( $option[ $node->id ] ) ? array() : $option[ $node->id ];
		foreach ( $roles as $role => $name ) {
			echo '<td class="ham-center">';

			echo "<input type='checkbox' name='" . HAM_SETTING_BAR . "[{$node->id}][]' value='{$role}'" . checked( in_array( $role, $checked_roles ), true, false ) . ' />';

			echo '</td>';
		}

		// Don't display the item in the future
		$ham_displayed[] = $node->id;

		// Change row class
		$alternate = ! $alternate;

		// Update item displayed status
		$is_displayed = true;
	}

	// Display child nodes
	if ( ! empty( $node->children ) ) {
		if ( $is_displayed ) {
			$depth ++;
		}
		foreach ( $node->children as $child_node ) {
			ham_show_admin_bar_item( $child_node, $depth );
		}
	}
}
