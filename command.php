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
		 * <old-slug>
		 * : The slug of the old theme
		 *
		 * <old-name>
		 * : The name of the old theme
		 *
		 * <slug>
		 * : The new theme slug
		 *
		 * <name>
		 * : The new theme name
		 *
		 * ## EXAMPLE
		 *
		 *  $ wp theme-rename old-theme 'Old Theme' new-theme 'New Theme'
		 *
		 * @when after_wp_load
		 *
		 * @param array $args       Required arguments.
		 * @param array $assoc_args Optional arguments.
		 */
		public function __invoke( $args, $assoc_args ) {
			$arguments = $this->parse_arguments( $args );
		}

		/**
		 * Parse arguments
		 *
		 * @param  array $args The arguments.
		 * @return array       The arguments parsed.
		 */
		private function parse_arguments( $args ) {
			if ( $this->check_for_empty_arguments( $args ) ) {
				WP_CLI::error( 'Argument cannot be empty' );
				return;
			}

			return array(
				'old-slug' => $args[0],
				'old-name' => $args[1],
				'slug'     => $args[2],
				'name'     => $args[3],
			);
		}

		/**
		 * Check fo empty arguments.
		 *
		 * @param  array $args The arguments.
		 * @return boolval     If an argument is empty
		 */
		private function check_for_empty_arguments( $args ) {
			foreach ( $args as $arg ) {
				if ( empty( $arg ) ) {
					return true;
				}
			}

			return false;
		}
	}

	WP_CLI::add_command( 'theme-rename', 'WP_CLI_Theme_Rename_Command' );
}
