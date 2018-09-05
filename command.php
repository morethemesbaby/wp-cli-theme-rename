<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Says "Hello World" to new users
 *
 * @when before_wp_load
 */
$theme_rename_command = function() {
	WP_CLI::success( "Theme rename." );
};
WP_CLI::add_command( 'theme-rename', $theme_rename_command );
