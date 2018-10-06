<?php

/**
 * Import & Export class
 */
class HAM_Imex extends Peace_Admin_Page {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$page_options = array(
			'menu_title' => __( 'Import & Export', 'elu-hide-admin-menu' ),
			'parent'     => 'hide-admin-menu',
			'capability' => 'manage_options',
			'multisite'  => true,
		);
		$this->create( 'hide-admin-menu-imex', $page_options );
	}

	/**
	 * Show import & export page
	 */
	public function show() {
		include_once HAM_ADMIN_PAGES_DIR . 'imex.php';
	}

	/**
	 * Import or export base on submit
	 */
	public function load() {
		if ( isset( $_POST['export'] ) ) {
			$this->export();
		} elseif ( isset( $_POST['import'] ) ) {
			$this->import();
		}
	}

	/**
	 * Export plugin settings
	 */
	public function export() {
		check_admin_referer( 'ham-export' );

		if ( is_network_admin() ) {
			$menu_option = get_site_option( HAM_SETTING_MENU );
			$bar_option  = ''; // We just don't have network settings for admin bar
			$file_name   = 'hide-admin-menu-network.txt';
		} else {
			$menu_option = get_option( HAM_SETTING_MENU );
			$bar_option  = get_option( HAM_SETTING_BAR );
			$file_name   = 'hide-admin-menu.txt';
		}

		$menu_option = serialize( $menu_option );
		$bar_option  = serialize( $bar_option );

		$content = $menu_option . '##RW##' . $bar_option;

		$this->download( $content, $file_name );
	}

	/**
	 * Import plugin settings
	 */
	public function import() {
		check_admin_referer( 'ham-import' );

		$error = $menu_option = $bar_option = '';
		if ( ! isset( $_FILES['setting']['error'] ) || $_FILES['setting']['error'] != UPLOAD_ERR_OK ) {
			$error = __( 'Error uploading file. Please try again.', 'elu-hide-admin-menu' );
		} else {
			$content = file_get_contents( $_FILES['setting']['tmp_name'] );

			// No file content or error reading
			if ( ! $content ) {
				$error = __( 'Cannot read file content. Please try again.', 'elu-hide-admin-menu' );
			}

			list( $menu_option, $bar_option ) = explode( '##RW##', $content );

			// Not a valid setting file
			// Make sure no error is echoed
			$menu_option = @unserialize( $menu_option );
			$bar_option  = @unserialize( $bar_option );

			if ( false === $menu_option || false === $bar_option ) {
				$error = __( 'Invalid file content. Please try again.', 'elu-hide-admin-menu' );
			}
		}

		if ( $error ) {
			add_settings_error( $this->page_id, '', $error );
		} else {
			if ( is_network_admin() ) {
				update_site_option( HAM_SETTING_MENU, $menu_option );
			} else {
				update_option( HAM_SETTING_MENU, $menu_option );
				update_option( HAM_SETTING_BAR, $bar_option );
			}

			add_settings_error( $this->page_id, '', __( 'Settings imported successfully.', 'elu-hide-admin-menu' ), 'updated' );
		}
	}

	/**
	 * Output string as a file download, simplified from http://goo.gl/sXQOV
	 *
	 * @param string $str File content
	 * @param string $name File name
	 */
	public function download( $str, $name ) {
		$size = strlen( $str );
		$name = rawurldecode( $name );

		// Required for IE, otherwise Content-Disposition may be ignored
		if ( ini_get( 'zlib.output_compression' ) ) {
			ini_set( 'zlib.output_compression', 'Off' );
		}

		header( 'Content-Type: application/force-download' );
		header( "Content-Disposition: attachment; filename=\"{$name}\"" );
		header( 'Content-Transfer-Encoding: binary' );
		header( "Content-Length: {$size}" );

		// Make the download non-cacheable
		header( 'Cache-control: private' );
		header( 'Pragma: private' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );

		echo $str;
		exit;
	}
}
