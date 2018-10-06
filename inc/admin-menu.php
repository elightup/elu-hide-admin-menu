<?php

/**
 * Admin menu management class
 */
class HAM_Admin_Menu extends Peace_Admin_Page {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$page_options = array(
			'menu_title' => __( 'Hide Menu', 'ham' ),
			'page_title' => __( 'Hide Admin Menu', 'ham' ),
			'submenu'    => __( 'Admin Menu', 'ham' ),
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-hidden',
			'multisite'  => true,
		);

		$this->create( 'hide-admin-menu', $page_options );
	}

	/**
	 * Add hooks
	 */
	public function hooks() {
		parent::hooks();

		// Register plugin setting
		add_action( 'admin_init', array( $this, 'register_setting' ) );

		// Handle actions for network admin page
		if ( is_network_admin() ) {
			// Backup current menu admin for show in the form
			add_action( 'network_admin_menu', array( $this, 'backup' ) );
		} // For normal admin page
		else {
			// Backup current menu admin for show in the form
			add_action( 'admin_menu', array( $this, 'backup' ), 9999 );

			// Hide main menu items
			// The priority needs to be high because it works with saved values
			add_action( 'admin_menu', array( $this, 'hide' ), 10000 );
		}
	}

	/**
	 * Register plugin setting
	 */
	public function register_setting() {
		register_setting( HAM_SETTING_MENU, HAM_SETTING_MENU, array( $this, 'sanitize' ) );
	}

	/**
	 * Serialize plugin settings
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function sanitize( $settings ) {
		// Reset
		if ( ! empty( $_POST['reset'] ) ) {
			$settings = array();

			add_settings_error( $this->page_id, '', __( 'Settings reset.', 'ham' ), 'updated' );
		} else {
			add_settings_error( $this->page_id, '', __( 'Settings updated.', 'ham' ), 'updated' );
		}

		return $settings;
	}

	/**
	 * Show admin page
	 */
	public function show() {
		include_once HAM_ADMIN_PAGES_DIR . 'admin-menu.php';

		// Fix for GravityForms
		if ( class_exists( 'GFForms' ) ) {
			remove_filter( 'role_has_cap', array( $this, 'role_has_cap' ), 10 );
		}
		if ( class_exists( 'WPCF7_ContactForm' ) ) {
			remove_filter( 'role_has_cap', array( $this, 'role_has_cap_CF7' ), 11 );
		}
	}

	/**
	 * Enqueue scripts and styles for 'menu' configuration page
	 */
	public function enqueue() {
		wp_enqueue_style( 'ham', HAM_CSS_URL . 'style.css' );

		wp_register_script( 'ham-jquery-tablescroll', HAM_JS_URL . 'tablescroll-min.js', array( 'jquery' ) );
		wp_enqueue_script( 'ham', HAM_JS_URL . 'script.js', array( 'ham-jquery-tablescroll' ) );
	}

	/**
	 * Backup the menu, submenu
	 * Used to show all items in config page
	 */
	public function backup() {
		global $menu, $submenu, $ham_menu, $ham_submenu;

		// Load menu of blogs in network and save in $menu if in network settings page
		if ( is_network_admin() ) {
			$tmp1 = $menu;
			$tmp2 = $submenu;
			$menu = $submenu = array();
			require ABSPATH . 'wp-admin/menu.php';
		}

		// Backup
		$ham_menu    = $menu;
		$ham_submenu = $submenu;

		// Restore the current network admin
		if ( is_network_admin() ) {
			$menu    = $tmp1;
			$submenu = $tmp2;
		}

		// Fix for GravityForms
		if ( class_exists( 'GFForms' ) ) {
			add_filter( 'role_has_cap', array( $this, 'role_has_cap' ), 10, 3 );
		}
		// Fix for Contact Form7
		if ( class_exists( 'WPCF7_ContactForm' ) ) {
			add_filter( 'role_has_cap', array( $this, 'role_has_cap_CF7' ), 11, 3 );
		}
	}

	/**
	 * Fix capabilities for GravityForms
	 *
	 * @see  GFForms::user_has_cap()
	 *
	 * @param  array  $all_caps
	 * @param  string $cap
	 * @param  string $name
	 *
	 * @return array
	 */
	public function role_has_cap( $all_caps, $cap, $name ) {
		// Add cap 'gform_full_access' to administrators only
		if ( $cap != 'gform_full_access' || $name != 'administrator' ) {
			return $all_caps;
		}

		// If the members plugin is not installed
		if ( ! function_exists( 'members_get_capabilities' ) ) {
			$all_caps['gform_full_access'] = true;

			return $all_caps;
		}

		// Checking if user has any GF permission.
		$gf_caps    = GFCommon::all_caps();
		$has_gf_cap = false;
		foreach ( $gf_caps as $gf_cap ) {
			if ( rgar( $all_caps, $gf_cap ) ) {
				$has_gf_cap = true;
				break;
			}
		}

		// Give full access to administrators if none of the GF permissions are active by the Members plugin
		if ( ! $has_gf_cap ) {
			$all_caps['gform_full_access'] = true;
		}

		return $all_caps;
	}

	/**
	 * Fix capabilities for Contact Form 7
	 *
	 * @param  array  $all_caps
	 * @param  string $cap
	 * @param  string $name
	 *
	 * @return array
	 */
	public function role_has_cap_CF7( $all_caps, $cap, $name ) {
		// Add cap 'gform_full_access' to administrators only
		if ( ( $cap != 'wpcf7_read_contact_forms' && $cap != 'wpcf7_edit_contact_forms' ) || $name != 'administrator' ) {
			return $all_caps;
		}

		// Give full access to administrators if none of the CF permissions are active by the Members plugin
		$all_caps['wpcf7_read_contact_forms'] = true;
		$all_caps['wpcf7_edit_contact_forms'] = true;

		return $all_caps;
	}

	/**
	 * Hide main menu items
	 * Prioritize network settings over site settings
	 */
	public function hide() {
		if ( is_multisite() ) {
			$this->do_hide( get_site_option( HAM_SETTING_MENU ) );
		}
		$this->do_hide( get_option( HAM_SETTING_MENU ) );
	}

	/**
	 * Hide admin menu based on saved option
	 *
	 * @param array $option Option which saves hidden menus, submenus
	 */
	public function do_hide( $option ) {
		global $menu, $submenu, $_wp_menu_nopriv, $_wp_submenu_nopriv;

		if ( empty( $option ) ) {
			return;
		}

		$user = wp_get_current_user();

		foreach ( $menu as $i => $top_item ) {
			$top_page = $top_item[2];
			if ( empty( $option[ $top_page ] ) || ! is_array( $option[ $top_page ] ) ) {
				continue;
			}

			$option_top = $option[ $top_page ];

			// Remove top menu item
			$has_role = array_intersect( $user->roles, ham_normalize_array( $option_top ) );
			if ( ! empty( $has_role ) ) {
				$_wp_menu_nopriv[ $top_page ] = true; // No access
				unset( $menu[ $i ] );                 // Remove
			}

			if ( empty( $submenu[ $top_page ] ) ) {
				continue;
			}

			// Remove submenu items
			foreach ( $submenu[ $top_page ] as $j => $sub_item ) {
				$sub_page = $sub_item[2];
				if ( empty( $option_top[ $sub_page ] ) || ! is_array( $option_top[ $sub_page ] ) ) {
					continue;
				}
				$has_role = array_intersect( $user->roles, $option_top[ $sub_page ] );
				if ( ! empty( $has_role ) ) {
					if ( ! isset( $_wp_submenu_nopriv[ $top_page ] ) ) {
						$_wp_submenu_nopriv[ $top_page ] = array();
					}

					$_wp_submenu_nopriv[ $top_page ][ $sub_page ] = true; // No access
					unset( $submenu[ $top_page ][ $j ] );                 // Remove
				}
			}
		}
	}

	/**
	 * Save network option
	 */
	public function load() {
		if (
			! is_network_admin() // If in normal admin page
			|| ( empty( $_POST['submit'] ) && empty( $_POST['reset'] ) ) // Or not by submitting
		) {
			return;
		}

		$setting = isset( $_POST[ HAM_SETTING_MENU ] ) ? $_POST[ HAM_SETTING_MENU ] : array();

		// Call sanitize function, will add update message by the way
		$setting = $this->sanitize( $setting );
		update_site_option( HAM_SETTING_MENU, $setting );
	}
}
