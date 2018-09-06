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
		 * <new-slug>
		 * : The new theme slug
		 *
		 * <new-name>
		 * : The new theme name
		 *
		 * ## EXAMPLE
		 *
		 *   $ wp theme-rename old-theme new-theme 'New Theme'
		 *
		 * @when after_wp_load
		 *
		 * @param array $args       Required arguments.
		 * @param array $assoc_args Optional arguments.
		 */
		public function __invoke( $args, $assoc_args ) {
			$arguments = $this->parse_arguments( $args );

			$this->create_new_folder( $arguments );
			$this->copy_theme_files( $arguments );
			$this->replace_texts( $arguments );
			$this->rename_files( $arguments );
			$this->replace_shortcodes( $arguments );

			WP_CLI::success( 'All done' );
		}

		/**
		 * Replaces shorcodes.
		 *
		 * The replace is done inside page content, for example.
		 *
		 * @param array $arguments The arguments.
		 * @return void
		 */
		private function replace_shortcodes( $arguments ) {
			$old_value = $arguments['old-textdomain'];
			$new_value = $arguments['new-textdomain'];

			WP_CLI::warning( 'If there are shortcodes used please consider a database replace. ' );
			WP_CLI::warning( "$ wp search-replace '{$old_value}' '{$new_value}'" );
		}

		/**
		 * Renames files.
		 *
		 * @param  array $arguments The arguments.
		 * @return void
		 */
		private function rename_files( $arguments ) {
			$path_to_new_folder = $arguments['path-to-new-folder'];

			$folders = array(
				'assets/images',
				'assets/js',
				'inc',
				'languages',
				'template-parts',
			);

			$old_value = $arguments['old-textdomain'];
			$new_value = $arguments['new-textdomain'];

			foreach ( $folders as $folder ) {
				WP_CLI::log( "Renaming filenames from $old_value to $new_value in $path_to_new_folder/$folder" );

				passthru( "cd {$path_to_new_folder} && find {$folder}/. -type f -exec rename 's/{$old_value}/{$new_value}/' {} \;", $result );

				if ( ( 0 !== $result ) ) {
					WP_CLI::error( 'Renaming file error' );
				}
			}
		}

		/**
		 * Replaces texts in files.
		 *
		 * @link https://stackoverflow.com/questions/15920276/find-and-replace-string-in-all-files-recursive-using-grep-and-sed
		 * @param  array $arguments The arguments.
		 * @return void
		 */
		private function replace_texts( $arguments ) {
			$path_to_new_folder = $arguments['path-to-new-folder'];

			$replacements = array(
				array(
					$arguments['old-name'],
					$arguments['new-name'],
				),
				array(
					$arguments['old-packagename'],
					$arguments['new-packagename'],
				),
				array(
					$arguments['old-functionname'],
					$arguments['new-functionname'],
				),
				array(
					$arguments['old-textdomain'],
					$arguments['new-textdomain'],
				),
			);

			foreach ( $replacements as $replacement ) {
				$old_value = $replacement[0];
				$new_value = $replacement[1];

				WP_CLI::log( "Replacing $old_value with $new_value in $path_to_new_folder" );

				passthru( "cd {$path_to_new_folder} && grep -rl '{$old_value}' . | xargs sed -i 's@{$old_value}@{$new_value}@g'", $result );

				if ( ( 0 !== $result ) ) {
					WP_CLI::error( 'Renaming text error' );
				}
			}
		}

		/**
		 * Copies theme files from the old folder to the new folder.
		 *
		 * @param  array $arguments The arguments.
		 * @return void.
		 */
		private function copy_theme_files( $arguments ) {
			$path_to_new_folder = $arguments['path-to-new-folder'];
			$mask_to_old_folder = $arguments['path-to-old-folder'] . '/*';

			passthru( "cp -Rf $mask_to_old_folder $path_to_new_folder", $result );

			if ( ( 0 !== $result ) ) {
				WP_CLI::error( 'Cannot copy old files to new folder: ' . $path_to_new_folder );
			}
		}

		/**
		 * Creates a new folder.
		 *
		 * @param  array $arguments The arguments.
		 * @return void.
		 */
		private function create_new_folder( $arguments ) {
			$path_to_new_folder = $arguments['path-to-new-folder'];

			passthru( "mkdir $path_to_new_folder", $result );

			if ( ( 0 !== $result ) ) {
				WP_CLI::error( 'Cannot create new folder: ' . $path_to_new_folder );
			}
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
			}

			$old_slug = $args[0];
			$new_slug = $args[1];
			$new_name = $args[2];

			if ( ! $this->theme_exists( $old_slug ) ) {
				WP_CLI::error( 'Old theme cannot be found' );
			}

			$theme = wp_get_theme( $args[0] );

			return array(
				'old-slug'           => $old_slug,
				'new-slug'           => $new_slug,
				'path-to-theme'      => $theme->theme_root,
				'path-to-new-folder' => $theme->theme_root . '/' . $new_slug,
				'path-to-old-folder' => $theme->theme_root . '/' . $old_slug,
				'old-name'           => $theme->get( 'Name' ),
				'old-textdomain'     => $theme->get( 'TextDomain' ),
				'old-packagename'    => str_replace( ' ', '_', $theme->get( 'Name' ) ),
				'old-functionname'   => str_replace( '-', '_', $theme->get( 'TextDomain' ) ),
				'new-name'           => $new_name,
				'new-textdomain'     => str_replace( '-', '-', $new_slug ),
				'new-packagename'    => str_replace( ' ', '_', $new_name ),
				'new-functionname'   => str_replace( '-', '_', $new_slug ),
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
