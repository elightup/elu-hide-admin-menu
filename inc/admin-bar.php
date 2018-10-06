<?php

/**
 * Admin bar management class
 */
class HAM_Admin_Bar extends Peace_Admin_Page {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$page_options = array(
			'menu_title' => __( 'Admin Bar', 'ham' ),
			'page_title' => __( 'Hide Admin Bar', 'ham' ),
			'parent'     => 'hide-admin-menu',
			'capability' => 'manage_options',
		);

		$this->create( 'hide-admin-menu-bar', $page_options );
	}

	/**
	 * Add hooks
	 */
	public function hooks() {
		parent::hooks();

		// Register plugin setting
		add_action( 'admin_init', array( $this, 'register_setting' ) );

		// Hide admin bar items
		add_action( 'wp_before_admin_bar_render', array( $this, 'hide' ), 10000 );
	}

	/**
	 * Register plugin setting
	 */
	public function register_setting() {
		register_setting( HAM_SETTING_BAR, HAM_SETTING_BAR, array( $this, 'sanitize' ) );
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

			add_settings_error( $this->page_id, '', __( 'Settings reset.' ), 'updated' );
		} else {
			add_settings_error( $this->page_id, '', __( 'Settings updated.' ), 'updated' );
		}

		return $settings;
	}

	/**
	 * Show config page for admin bar
	 */
	public function show() {
		include_once HAM_ADMIN_PAGES_DIR . 'admin-bar.php';
	}

	/**
	 * Enqueue scripts and styles for 'admin bar' configuration page
	 */
	public function enqueue() {
		wp_enqueue_style( 'ham', HAM_CSS_URL . 'style.css' );

		wp_register_script( 'ham-jquery-tablescroll', HAM_JS_URL . 'tablescroll-min.js', array( 'jquery' ) );
		wp_enqueue_script( 'ham', HAM_JS_URL . 'script.js', array( 'ham-jquery-tablescroll' ) );
	}

	/**
	 * Hide admin bar items
	 */
	public function hide() {
		global $wp_admin_bar, $ham_nodes;

		// Backup nodes before hiding
		$ham_nodes = $nodes = $wp_admin_bar->get_nodes();

		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $node ) {
			$this->maybe_hide( $node );
		}
	}

	/**
	 * Hide specific admin bar item if needed
	 *
	 * @param object $node Node object
	 */
	public function maybe_hide( $node ) {
		global $wp_admin_bar;
		$user = wp_get_current_user();

		$option = get_option( HAM_SETTING_BAR );
		if ( empty( $option ) ) {
			$option = array();
		}

		$config = empty( $option[ $node->id ] ) ? array() : $option[ $node->id ];

		// Check for user role
		$has_role = array_intersect( $user->roles, $config );
		if ( ! empty( $has_role ) ) {
			$wp_admin_bar->remove_node( $node->id );
		}

		// Remove sub items
		if ( ! empty( $node->children ) ) {
			foreach ( $node->children as $child_node ) {
				$this->maybe_hide( $child_node );
			}
		}
	}
}
