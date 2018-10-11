<?php
/**
 * Plugin Name: ELU Hide Admin Menu
 * Plugin URI: https://elightup.com
 * Description: Hide unneeded items in admin menu and admin bar. Customizable by user role.
 * Version: 1.0.0
 * Author: eLightUp
 * Author URI: https://elightup.com
 */

// Define plugin URLs, for fast enqueuing scripts and styles
define( 'HAM_URL', plugin_dir_url( __FILE__ ) );
define( 'HAM_JS_URL', trailingslashit( HAM_URL . 'js' ) );
define( 'HAM_CSS_URL', trailingslashit( HAM_URL . 'css' ) );
define( 'HAM_IMG_URL', trailingslashit( HAM_URL . 'img' ) );

// Plugin paths, for including files
define( 'HAM_DIR', plugin_dir_path( __FILE__ ) );
define( 'HAM_INC_DIR', trailingslashit( HAM_DIR . 'inc' ) );
define( 'HAM_CLASSES_DIR', trailingslashit( HAM_INC_DIR . 'classes' ) );
define( 'HAM_ADMIN_PAGES_DIR', trailingslashit( HAM_INC_DIR . 'admin-pages' ) );

// Plugin setting
define( 'HAM_SETTING_MENU', 'elu_hide_admin_menu' );
define( 'HAM_SETTING_BAR', 'elu_hide_admin_bar' );

// Load files
require_once HAM_INC_DIR . 'functions.php';
require_once HAM_INC_DIR . 'classes/admin-page.php';

// Admin menu and Import & Export pages are in the back-end only
// @link http://www.deluxeblogtips.com/?p=345
if ( is_admin() ) {
	include_once HAM_INC_DIR . 'admin-menu.php';
	new HAM_Admin_Menu();

	include_once HAM_INC_DIR . 'imex.php';
	new HAM_Imex();
}

// Admin bar works in both front-end and back-end
require_once HAM_INC_DIR . 'admin-bar.php';
new HAM_Admin_Bar();
