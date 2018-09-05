<?php
/**
 * Theme Rename WP-CLI package
 *
 * @package WPCLIThemeRename
 * @since 1.0.0
 */

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

if ( ! class_exists( 'WP_CLI_Theme_Rename_Command' ) ) {
	/**
	 * The main class.
	 */
	class WP_CLI_Theme_Rename_Command extends WP_CLI_Command {
		/**
		 * Renames a theme
		 *
		 * ## OPTIONS
		 *
		 * <path-to-old-theme>
		 * : The path to the old theme
		 *
		 * <slug>
		 * : The new theme slug
		 *
		 * <name>
		 * : The new theme name
		 *
		 * ## EXAMPLE
		 *
		 *  $ wp theme-rename wp-content/themes/old-theme new-theme 'New Theme'
		 *
		 * @when after_wp_load
		 *
		 * @param array $args       Required arguments.
		 * @param array $assoc_args Optional arguments.
		 */
		public function __invoke( $args, $assoc_args ) {
			$path_to_old_theme = $args[0];
			$slug              = $args[1];
			$name              = $args[2];

			if ( empty( $path_to_old_theme ) || empty( $slug ) || empty( $name ) ) {
				WP_CLI::error( 'No argument should be empty.' );
				return;
			}

			$arguments = $this->parse_arguments( $path_to_old_theme, $slug, $name );
		}

		/**
		 * Parse arguments
		 *
		 * @param  string $path_to_old_theme Path to the old theme.
		 * @param  string $slug              The new theme slug.
		 * @param  string $name              The new theme name.
		 * @return array                    The arguments parsed.
		 */
		private function parse_arguments( $path_to_old_theme, $slug, $name ) {
			$ret = [];
			return $ret;
		}
	}

	WP_CLI::add_command( 'theme-rename', 'WP_CLI_Theme_Rename_Command' );
}
