<?php

/**
 * Admin Page Class
 * Create menu and admin page
 *
 * @package Peace Framework
 */
class Peace_Admin_Page {
	/**
	 * Page hook
	 *
	 * @var string
	 */
	protected $page_hook;

	/**
	 * ID of admin page
	 *
	 * @var string
	 */
	protected $page_id;

	/**
	 * Page options
	 *
	 * @var string
	 */
	protected $page_options;

	/**
	 * Call this method in subclass constructor to add admin page
	 *
	 * @param string $page_id ID or slug of admin page
	 * @param array  $page_options Page options like page_title, menu_title, capability....
	 */
	public function create( $page_id, $page_options = array() ) {
		// Requires page ID and menu title or page title
		if (
			empty( $page_id ) ||
			( empty( $page_options['page_title'] ) && empty( $page_options['menu_title'] ) )
		) {
			wp_die( __( 'Page ID and page title or menu title required', 'peace' ) );
		}

		// Setup the properties value
		$this->page_id      = $page_id;
		$this->page_options = wp_parse_args(
			$page_options,
			array(
				'multisite' => false,
			)
		);

		// Add hooks
		$this->hooks();
	}

	/**
	 * Add hooks
	 */
	public function hooks() {
		// Create the menu or sub-menu
		if ( is_network_admin() && $this->page_options['multisite'] ) {
			$menu_hook   = 'network_admin_menu';
			$notice_hook = 'network_admin_notices';
		} else {
			$menu_hook   = 'admin_menu';
			$notice_hook = 'admin_notices';
		}
		add_action( $menu_hook, array( $this, 'admin_menu' ) );
		add_action( $notice_hook, array( $this, 'admin_notices' ) );
	}

	/**
	 * Add top level menu or sub-menu. Depend on page options
	 */
	public function admin_menu() {
		// Note that one of menu title and page title can be empty
		// So we make sure they have values
		if ( empty( $this->page_options['menu_title'] ) ) {
			$this->page_options['menu_title'] = $this->page_options['page_title'];
		} elseif ( empty( $this->page_options['page_title'] ) ) {
			$this->page_options['page_title'] = $this->page_options['menu_title'];
		}

		$menu = wp_parse_args(
			$this->page_options,
			array(
				'page_title' => '',
				'menu_title' => '',
				'capability' => 'edit_theme_options',
				'icon_url'   => '',
				'position'   => null,
				'parent'     => '', // ID of parent page. Optional
				'submenu'    => '', // Name of submenu. Optional
			)
		);

		// Add top level menu
		if ( empty( $this->page_options['parent'] ) ) {
			$this->page_hook = add_menu_page(
				$menu['page_title'],
				$menu['menu_title'],
				$menu['capability'],
				$this->page_id,
				array(
					$this,
					'show',
				),
				$menu['icon_url'],
				$menu['position']
			);

			// If this menu has a default sub-menu
			if ( ! empty( $this->page_options['submenu'] ) ) {
				add_submenu_page(
					$this->page_id,
					$menu['page_title'],
					$this->page_options['submenu'],
					$menu['capability'],
					$this->page_id,
					array(
						$this,
						'show',
					)
				);
			}
		} // Add sub-menu
		else {
			$this->page_hook = add_submenu_page(
				$this->page_options['parent'],
				$menu['page_title'],
				$menu['menu_title'],
				$menu['capability'],
				$this->page_id,
				array(
					$this,
					'show',
				)
			);
		}

		add_action( "admin_print_styles-{$this->page_hook}", array( $this, 'enqueue' ), 10, 1 );

		// Custom actions when page is load
		if ( method_exists( $this, 'load' ) ) {
			add_action( "load-{$this->page_hook}", array( $this, 'load' ) );
		}

		// Add contextual help
		if ( method_exists( $this, 'help' ) ) {
			add_action( "load-{$this->page_hook}", array( $this, 'help' ) );
		}
	}

	/**
	 * Output the main admin page
	 * Overwrite this public function in subclass to show page content
	 */
	public function show() {
	}

	/**
	 * Enqueue scripts and styles for admin page
	 * Overwrite this public function in subclass to add its own scripts and styles
	 */
	public function enqueue() {
	}

	/**
	 * Display notices
	 */
	public function admin_notices() {
		$screen = get_current_screen();

		// Use this check to make sure page hook is detected in network admin
		if ( 0 === strpos( $screen->id, $this->page_hook ) ) {
			settings_errors( $this->page_id );
		}
	}
}
