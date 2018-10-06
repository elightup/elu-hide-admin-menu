<?php
// Prevent hacks
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete plugin options
delete_option( HAM_SETTING_MENU );
delete_option( HAM_SETTING_BAR );
