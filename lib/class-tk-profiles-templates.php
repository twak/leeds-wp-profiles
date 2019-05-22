<?php
/**
 * Templates setup for Toolkit Plugins
 * This is intended for generic usage across different plugins.
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_Templates' ) ) {
	/**
	 * Class to ensure correct templates are loaded for the plugin.
	 */
	class TK_Profiles_Templates {
		/**
		 * Set the template directory for the plugin
		 *
		 * @var string $template_dir - partial path to template directory.
		 */
		private $template_dir = 'templates/tk-profiles';

		/**
		 * Taxonomies handled by the plugin
		 *
		 * @var array $taxonomies - needed to determine taxonomy specific templates.
		 */
		private $taxonomies = array( 'tk_profile_category' );

		/**
		 * Checks if the current page is a plugin archive page.
		 */
		private function is_archive() {
			return is_post_type_archive( 'tk_profiles' );
		}

		/**
		 * Checks if the current page is a plugin taxonomy archive page.
		 */
		private function is_tax() {
			return is_tax( 'tk_profile_category' );
		}

		/**
		 * Register all hooks with WordPress API.
		 */
		public function __construct() {
			/**
			 * Add in single and archive templates
			 */
			add_filter( 'single_template', array( $this, 'single_template' ) );
			add_filter( 'archive_template', array( $this, 'archive_template' ) );
			/**
			 * Filter to include templates like get_template_part().
			 */
			add_filter( 'tk_profiles_template', array( $this, 'template_part' ), 10, 2 );
		}

		/**
		 * Ensures template is used from plugin for single event pages.
		 * Used as a filter function on the single_template hook
		 *
		 * @param string $single_template - path to single template file.
		 */
		public function single_template( $single_template ) {
			global $post;
			if ( 'tk_profiles' === $post->post_type ) {
//				$theme_path    = get_stylesheet_directory() . '/' . $this->template_dir . '/single.php';
//				$template_path = get_template_directory() . '/' . $this->template_dir . '/single.php';
				$plugin_path   = dirname( __DIR__ ) . '/' . $this->template_dir . '/single.php';
//				if ( file_exists( $theme_path ) ) {
//                    echo ("what");
//					return $theme_path;
//				if ( file_exists( $template_path ) ) {
//					return $template_path;
//				} else
			    if ( file_exists( $plugin_path ) ) {
					return $plugin_path;
				}
			}


			return $single_template;
		}

		/**
		 * Ensures template is used from plugin for archive pages.
		 * Used as a filter function on the archive_template hook.
		 *
		 * @param string $archive_template - path to archive template file.
		 */
		public function archive_template( $archive_template ) {
			global $wp_query;

			if ( $this->is_archive() || $this->is_tax() ) {

				// Collect candidates for tempaltes here.
				$templates = array();

				/**
				 * Checks for overrides in template and theme for taxonomy archives
				 */
				foreach ( $this->taxonomies as $tax ) {
					if ( is_tax( $tax ) ) {

						/**
						 * First check for templates which are specific to terms
						 * taxonomy-{taxonomy}-{term}.php
						 */
						$qo = get_queried_object();

						if ( $qo->slug ) {
							$templates[] = get_stylesheet_directory() . '/' . $this->template_dir . '/taxonomy-' . $tax . '-' . $qo->slug . '.php';
							$templates[] = get_template_directory() . '/' . $this->template_dir . '/taxonomy-' . $tax . '-' . $qo->slug . '.php';
						}

						/**
						 * Now check for templates which are specific to the taxonomy
						 * taxonomy-{taxonomy}.php
						 */
						$templates[] = get_stylesheet_directory() . '/' . $this->template_dir . '/taxonomy-' . $tax . '.php';
						$templates[] = get_template_directory() . '/' . $this->template_dir . '/taxonomy-' . $tax . '.php';
						$templates[] = dirname( __DIR__ ) . '/' . $this->template_dir . '/taxonomy-' . $tax . '.php';

						/**
						 * Now check for a generic taxonomy
						 */
						$templates[] = get_stylesheet_directory() . '/' . $this->template_dir . '/taxonomy.php';
						$templates[] = get_template_directory() . '/' . $this->template_dir . '/taxonomy.php';
						$templates[] = dirname( __DIR__ ) . '/' . $this->template_dir . '/taxonomy.php';
					}
				}
				/**
				 * Checks for overrides in template and theme for post type archive
				 */
				$templates[] = get_stylesheet_directory() . '/' . $this->template_dir . '/archive.php';
				$templates[] = get_template_directory() . '/' . $this->template_dir . '/archive.php';
				$templates[] = dirname( __DIR__ ) . '/' . $this->template_dir . '/archive.php';

				foreach ( $templates as $template ) {
					if ( file_exists( $template ) ) {
                        echo("test " . $template);
						return $template;
					}
				}
			}
			return $archive_template;
		}

		/**
		 * Template part filter.
		 *
		 * As there is no filter in get_template_part to load templates from plugins,
		 * this method is used to filter a template $slug/$name and return a path from
		 * the plugin if not template can be found elsewhere.
		 *
		 * Usage: load_template( apply_filters( 'tk_profiles_template', 'list', 'item' ), false );
		 *
		 * This will load the following templates in order of preference:
		 *
		 * 1. STYLESHEETPATH . $this->template_dir . '/list-item.php
		 * 2. STYLESHEETPATH . $this->template_dir . '/list.php
		 * 3. TEMPLATEPATH . $this->template_dir . '/list-item.php
		 * 4. TEMPLATEPATH . $this->template_dir . '/list.php
		 * 5. ABSPATH . WPINC . '/theme-compat/' . $this->template_dir . '/list-item.php
		 * 6. ABSPATH . WPINC . '/theme-compat/' . $this->template_dir . '/list.php
		 * 7. dirname( __DIR__ ) . '/' . $this->template_dir . '/list-item.php
		 * 8. dirname( __DIR__ ) . '/' . $this->template_dir . '/list.php
		 *
		 * @param string $slug - template slug.
		 * @param string $name - template name.
		 * @return string $path - full path to template.
		 */
		public function template_part( $slug, $name ) {
			// First get the paths for the template from the $slug and $name.
			$template_paths = array();
			$name           = (string) $name;
			if ( '' !== $name ) {
				$template_paths[] = $this->template_dir . "/{$slug}-{$name}.php";
			}
			$template_paths[] = $this->template_dir . "/{$slug}.php";
			// Use locate_template to see if there is a template in the Theme/Child Theme.
			$template = locate_template( $template_paths, false, false );
			// If no theme template, locate the correct one in the plugin.
			if ( '' === $template ) {
				foreach ( $template_paths as $template_path ) {
					if ( file_exists( dirname( __DIR__ ) . '/' . $template_path ) ) {
						$template = dirname( __DIR__ ) . '/' . $template_path;
						break;
					}
				}
			}
			return $template;
		}
	}
	new TK_Profiles_Templates();
}
