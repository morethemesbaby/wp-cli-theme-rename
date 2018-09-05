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
		 * <slug>
		 * : The new theme slug
		 *
		 * <name>
		 * : The new theme name
		 *
		 * ## EXAMPLE
		 *
		 *  $ wp theme-rename old-theme new-theme 'New Theme'
		 *
		 * @when after_wp_load
		 *
		 * @param array $args       Required arguments.
		 * @param array $assoc_args Optional arguments.
		 */
		public function __invoke( $args, $assoc_args ) {
			$arguments = $this->parse_arguments( $args );
			print_r($arguments);
		}

		/**
		 * Parse arguments
		 *
		 * @param  array $args The arguments.
		 * @return array       The arguments parsed.
		 */
		private function parse_arguments( $args ) {
			if ( $this->arguments_empty( $args ) ) {
				WP_CLI::error( 'Argument cannot be empty' );
				return;
			}

			if ( ! $this->theme_exists( $args[0] ) ) {
				WP_CLI::error( 'Old theme cannot be found' );
				return;
			}

			$theme = wp_get_theme( $args[0] );

			return array(
				'path-to-theme'  => $theme->theme_root . '/' . $args[0],
				'old-name'       => $theme->get( 'Name' ),
				'old-textdomain' => $theme->get( 'TextDomain' ),
				'old-slug'       => $args[0],
				'slug'           => $args[1],
				'name'           => $args[2],
			);
		}

		/**
		 *  Check if a theme exists.
		 *
		 * @param string $theme_slug The theme slug.
		 * @return boolval           If the theme exists.
		 */
		private function theme_exists( $theme_slug ) {
			$theme = wp_get_theme( $theme_slug );
			return $theme->exists();
		}

		/**
		 * Check fo empty arguments.
		 *
		 * @param  array $args The arguments.
		 * @return boolval     If an argument is empty
		 */
		private function arguments_empty( $args ) {
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
