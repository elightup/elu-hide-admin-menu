<?php
// Note that when we first hook to 'admin_menu', we save all current menu items to $ham_menu, $ham_submenu
global $ham_menu, $ham_submenu, $wp_roles;

$network_option = get_site_option( HAM_SETTING_MENU, array() );
$option         = get_option( HAM_SETTING_MENU, array() );

$action_page = is_network_admin() ? '' : 'options.php';

$roles = $wp_roles->get_names();

$alternate = true;
?>
<div class="wrap">
	<h1><?php _e( 'Hide Admin Menu', 'ham' ); ?></h1>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'Checking a checkbox disables the access to the menu item for the corresponding role.', 'ham' ); ?></p>
		<p><?php esc_html_e( 'If no checkbox is available, it means the corresponding role cannot access the menu item by default (and you can\'t enable it with this plugin either).', 'ham' ); ?></p>
		<?php if ( is_multisite() ) : ?>
			<p><?php esc_html_e( 'Disabled (grey) checkbox means the corresponding menu is hidden network-wide.', 'ham' ); ?></p>
		<?php endif; ?>
		<p><strong><?php esc_html_e( 'If you\'re the Administrator, don\'t check any checkboxes unless you really want to lock yourself out of that menu item.', 'ham' ); ?></strong></p>
	</div>

	<form method="post" action="<?php echo $action_page; ?>">

		<?php settings_fields( HAM_SETTING_MENU ); ?>

		<table class="widefat" id="ham-selection">
			<thead>
			<tr>
				<th><?php _e( 'Menu Item', 'ham' ); ?></th>
				<?php
				foreach ( $roles as $role => $name ) {
					echo "<th class='ham-center'>{$name}</th>";
				}
				?>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $ham_menu as $top_item ) {
				$top_text = $top_item[0];
				if ( $top_text == '' ) {
					// If this is a separator
					if ( in_array( 'wp-menu-separator', $top_item ) || in_array( 'wp-menu-separator-last', $top_item ) ) {
						$top_text = __( 'Separator', 'ham' );
					}
					// Otherwise, the top menu has no title, it still appears in WP admin area, so we don't ignore it
				}

				$top_page   = $top_item[2];
				$capability = $top_item[1];
				if ( is_numeric( $capability ) ) {
					$capability = ham_user_level_to_capability( $capability );
				}

				echo "
					<tr class='ham-item-level-0" . ( $alternate ? ' alternate' : '' ) . "' data-id='{$top_page}'>
						<td>{$top_text}</td>
				";

				$network_option_top = empty( $network_option[ $top_page ] ) ? array() : $network_option[ $top_page ];
				$option_top         = empty( $option[ $top_page ] ) ? array() : $option[ $top_page ];

				$top_tpl = '<input type="checkbox" name="%s[%s][]" value="%s"%s%s>';
				$sub_tpl = '<input type="checkbox" name="%s[%s][%s][]" value="%s"%s%s>';

				foreach ( $roles as $role => $name ) {
					echo '<td class="ham-center">';

					// Show checkbox only if current role has capability
					$role_object = get_role( $role );
					if ( $role_object->has_cap( $capability ) ) {
						$item_tpl = sprintf( $top_tpl, HAM_SETTING_MENU, $top_page, $role, '%s', '%s' );

						// Escape URL which contains '%'
						$item_tpl = str_replace( '%', '%%', $item_tpl );
						$item_tpl = str_replace( '%%s', '%s', $item_tpl );

						if ( is_multisite() ) {
							// In network admin config page
							if ( is_network_admin() ) {
								printf( $item_tpl, checked( in_array( $role, $network_option_top ), true, false ), '' );
							} // Hidden network-wide
							elseif ( in_array( $role, $network_option_top ) ) {
								printf( $item_tpl, ' checked="checked"', ' disabled="disabled"' );
							} else {
								printf( $item_tpl, checked( in_array( $role, $option_top ), true, false ), '' );
							}
						} else {
							printf( $item_tpl, checked( in_array( $role, $option_top ), true, false ), '' );
						}
					}

					echo '</td>';
				}

				echo '</tr>';

				$alternate = ! $alternate;

				// If current menu item has sub menu, echo its sub menu items
				if ( isset( $ham_submenu[ $top_page ] ) ) {
					foreach ( $ham_submenu[ $top_page ] as $sub_item ) {
						$sub_text   = $sub_item[0];
						$sub_page   = $sub_item[2];
						$capability = $sub_item[1];
						if ( is_numeric( $capability ) ) {
							$capability = ham_user_level_to_capability( $capability );
						}

						// If submenu has no title, just ignore it as it doesn't appear
						if ( $sub_text == '' ) {
							continue;
						}

						$network_option_sub = empty( $network_option_top[ $sub_page ] ) ? array() : $network_option_top[ $sub_page ];
						$option_sub         = empty( $option_top[ $sub_page ] ) ? array() : $option_top[ $sub_page ];

						echo "
							<tr class='ham-item-level-1" . ( $alternate ? ' alternate' : '' ) . "' data-parent='{$top_page}'>
								<td>— {$sub_text}</td>
						";
						if ( strpos( $sub_page, '&amp;' ) ) {
							$sub_page = str_replace( '&amp;', '&amp;amp;', $sub_page );
						}
						foreach ( $roles as $role => $name ) {
							echo '<td class="ham-center">';

							// Show checkbox only if current role has capability
							$role_object = get_role( $role );
							if ( $role_object->has_cap( $capability ) ) {
								$item_tpl = sprintf( $sub_tpl, HAM_SETTING_MENU, $top_page, $sub_page, $role, '%s', '%s' );

								// Escape URL which contains '%'
								$item_tpl = str_replace( '%', '%%', $item_tpl );
								$item_tpl = str_replace( '%%s', '%s', $item_tpl );

								if ( is_multisite() ) {
									// In network admin config page
									if ( is_network_admin() ) {
										printf( $item_tpl, checked( in_array( $role, $network_option_sub ), true, false ), '' );
									} // Hidden network-wide
									elseif ( in_array( $role, $network_option_sub ) ) {
										printf( $item_tpl, ' checked="checked"', ' disabled="disabled"' );
									} else {
										printf( $item_tpl, checked( in_array( $role, $option_sub ), true, false ), '' );
									}
								} else {
									printf( $item_tpl, checked( in_array( $role, $option_sub ), true, false ), '' );
								}
							}

							echo '</td>';
						}

						echo '</tr>';

						$alternate = ! $alternate;
					}
				}
			}
			?>
			</tbody>
			<tfoot>
			<tr>
				<th><?php _e( 'Menu Item', 'ham' ); ?></th>
				<?php
				foreach ( $roles as $role => $name ) {
					echo "<th class='ham-center'>{$name}</th>";
				}
				?>
			</tr>
			</tfoot>
		</table>
		<p class="submit">
			<?php submit_button( __( 'Save Settings', 'ham' ), 'primary', 'submit', false ); ?>
			<?php submit_button( __( 'Reset', 'ham' ), 'primary', 'reset', false ); ?>
		</p>
	</form>
</div>
