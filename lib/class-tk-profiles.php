<?php
/**
 * Toolkit Profiles Plugin
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles' ) ) {

	class TK_Profiles {
		/**
		 * Plugin version
		 *
		 * @var string $version - version number in SemVer format.
		 */
		public static $version = '1.1.6';

		/**
		 * Constructor - registers all hooks with the WordPress API
		 */
		public function __construct() {
			/**
			 * upgrade from previous version
			 */
			add_action( 'init', array( $this, 'upgrade' ), 11 );

			/**
			 * Enqueues the Profiles CSS and Javascript
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );

			/**
			 * plugin activation/deactivation
			 */
			register_activation_hook( __FILE__, array( __CLASS__, 'plugin_activation' ) );
		}

		/**
		 * Upgrades the plugin from a previous version
		 */
		public function upgrade() {
			$current_version = get_option( 'tk_profiles_plugin_version' );
			if ( $current_version !== self::$version ) {
				switch ( $current_version ) {
					case '1.1.4':
					case '1.1.5':
						update_field( 'tk_profiles_page_settings_show_breadcrumb', true, 'option' );
						global $wpdb;
						$wpdb->update(
							$wpdb->posts,
							array(
								'post_type' => 'tk_profiles',
							),
							array(
								'post_type' => 'profiles',
							),
							array( '%s' ),
							array( '%s' )
						);
						$wpdb->update(
							$wpdb->term_taxonomy,
							array(
								'taxonomy' => 'tk_profile_category',
							),
							array(
								'taxonomy' => 'profile_category',
							),
							array( '%s' ),
							array( '%s' )
						);
						wp_cache_flush();
						flush_rewrite_rules();
						// Intentional fall-through - each upgrade step should be applied.
				}
				/* update the version option */
				update_option( 'tk_profiles_plugin_version', self::$version );
			}
		}

		/**
		 * Gets the path to the plugin directory (with trailing slash).
		 */
		public static function plugin_path() {
			return plugin_dir_path( __DIR__ );
		}

		/**
		 * Enqueues the Profiles CSS
		 */
		public static function enqueue_styles() {
			wp_enqueue_style(
				'toolkit-profiles-css',
				plugins_url( 'css/toolkit-profiles.css', __DIR__ ),
				'',
				self::$version
			);
		}

		/**
		 * Enqueues scripts for admin area
		 */
		public static function enqueue_admin_script(){
			wp_enqueue_script(
				'toolkit-profiles-js',
				plugins_url( 'js/toolkit-profiles.js', __DIR__ ),
				array( 'jquery' ),
				self::$version,
				true
			);
		}

		/**
		 * Flush rewrite rules when creating new post type
		 *
		 * @see https://paulund.co.uk/flush-permalinks-custom-post-type
		 */
		public static function plugin_activation() {
			include_once dirname( __FILE__ ) . '/class-tk-profiles-post-type.php';
			TK_Profiles_Post_Type::create_taxonomy();
			TK_Profiles_Post_Type::create_post_type();
			flush_rewrite_rules();
		}
	}
	new TK_Profiles();
}
