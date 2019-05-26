<?php
/**
 * Toolkit Profiles Plugin admin area modifications
 * adds filters / columns / sorting on Profiles admin listing page
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_Admin' ) ) {
	/**
	 * Class to provide services in the admin area for the plugin.
	 */
	class TK_Profiles_Admin {
		/**
		 * Constructor - registers hooks with WordPress API.
		 */
		public function __construct() {
			/**
			 * Adds custom columns to profiles table in admin
			 */
			add_action( 'manage_edit-tk_profiles_columns', array( $this, 'add_profiles_columns' ) );
			add_action( 'manage_tk_profiles_posts_custom_column', array( $this, 'show_profiles_columns' ), 10, 2 );
			add_filter( 'manage_edit-tk_profiles_sortable_columns', array( $this, 'name_columns_register_sortable' ) );
			add_filter( 'pre_get_posts', array( $this, 'name_columns_orderby' ) );

			/**
			 * Adds filter to profiles table in admin for tk_profile_category taxonomy
			 */
			add_action( 'restrict_manage_posts', array( $this, 'restrict_profiles_by_category' ) );

			/**
			 * Creates custom slug settings
			 */
			add_action( 'admin_init', array( $this, 'maybe_add_permalink_section' ) );
		}

		/**
		 * Adds columns to the profiles listing table
		 * hooks into 'manage_edit-tk_profiles_columns'
		 *
		 * @param array $posts_columns - columns for admin table.
		 * @return array new $posts_columns.
		 */
		public function add_profiles_columns( $posts_columns ) {
            unset( $posts_columns['date'] );
//            unset( $posts_columns['tk_profiles_last_name'] );
			$posts_columns['title']                  = 'Full Name';
//			$posts_columns['tk_profiles_first_name'] = 'First Name';
//			$posts_columns['tk_profiles_last_name']  = 'Surname';
			$posts_columns['external']               = 'External';
//			$posts_columns['tk_profile_category']    = 'Categories';
			return $posts_columns;
		}

		/**
		 * Shows the taxonomy column of the manage profiles table
		 * hooks into 'manage_tk_profiles_posts_custom_column'
		 *
		 * @param string  $column_id - ID of column in table.
		 * @param integer $post_id - ID of post in row.
		 */
		public function show_profiles_columns( $column_id, $post_id ) {
			global $post;
			switch ( $column_id ) {
				case 'tk_profiles_last_name':
				case 'tk_profiles_first_name':
					echo esc_html( get_field( $column_id, $post_id ) );
					break;
				case 'external':
					$external_link = get_field( 'tk_profiles_external_link', $post_id );
					if ( get_field( 'tk_profiles_external_link_flag', $post_id ) && false !== filter_var( $external_link, FILTER_VALIDATE_URL ) ) {
						echo '<span class="dashicons dashicons-yes" style="color:green"></span>';
					} else {
						echo '<span class="dashicons dashicons-no" style="color:red"></span>';
					}
					break;
			}
		}

		/**
		 * Registers the first name and surname columns as sortable.
		 *
		 * @param array $columns - array of sortable column IDs.
		 * @return array $columns - array of sortable column IDs with name columns added.
		 */
		public function name_columns_register_sortable( $columns ) {
			$columns['tk_profiles_first_name'] = 'tk_profiles_first_name';
			$columns['tk_profiles_last_name']  = 'tk_profiles_last_name';
			return $columns;
		}

		/**
		 * Orders by first name or surname.
		 *
		 * @param WP_Query $query - passed by reference.
		 */
		public function name_columns_orderby( $query ) {
			if ( ! is_admin() ) {
				return;
			}
			$orderby = $query->get( 'orderby' );
			if ( 'tk_profiles_first_name' === $orderby || 'tk_profiles_last_name' === $orderby ) {
				$query->set( 'meta_key', $orderby );
				$query->set( 'orderby', 'meta_value' );
			}
		}

		/**
		 * Resticts listed profiles by category if a filter has been applied.
		 */
		public function restrict_profiles_by_category() {
			global $typenow;
			global $wp_query;
			if ( 'tk_profiles' === $typenow ) {
				$selected = isset( $wp_query->query['tk_profile_category'] ) ? $wp_query->query['tk_profile_category'] : false;
				wp_dropdown_categories( array(
					'show_option_all' => 'Show All Profile categories',
					'taxonomy'        => 'tk_profile_category',
					'name'            => 'tk_profile_category',
					'value_field'     => 'slug',
					'selected'        => $selected,
					'show_count'      => true,
				) );
			}
		}

		/**
		 * Adds a section to Settings->Permalinks to enable users to
		 * change the profiles and profile category slugs (base)
		 * uses Settings API
		 * First checks if the ACF plugin is active.
		 */
		public function maybe_add_permalink_section() {
			if ( class_exists( 'ACF' ) ) {
				/* Save settings */
				if ( isset( $_POST['tk_profiles_base'] ) ) {
					update_option(
						'tk_profiles_base',
						sanitize_title_with_dashes( $_POST['tk_profiles_base'] )
					);
					update_option(
						'tk_profile_categories_base',
						sanitize_title_with_dashes( $_POST['tk_profile_categories_base'] )
					);
				}

				/* Add section for profiles */
				add_settings_section(
					'tk_profiles_permalinks',
					'Profile URLs',
					'__return_empty_string',
					'permalink'
				);

				/* Add settings fields to the permalink page */
				add_settings_field(
					'tk_profiles_base',
					'Profiles URL Base',
					array( $this, 'add_slug_settings_callback' ),
					'permalink',
					'tk_profiles_permalinks',
					array(
						'field_name'  => 'tk_profiles_base',
						'placeholder' => 'profiles',
					)
				);
				add_settings_field(
					'tk_profile_categories_base',
					'Profile Categories Base',
					array( $this, 'add_slug_settings_callback' ),
					'permalink',
					'tk_profiles_permalinks',
					array(
						'field_name'  => 'tk_profile_categories_base',
						'placeholder' => 'profile_category',
					)
				);
			}
		}

		/**
		 * Generic callback for slug settings input field
		 *
		 * @param array $options - passed from add_settings_field callback.
		 */
		public function add_slug_settings_callback( $options ) {
			$field       = $options['field_name'];
			$placeholder = $options['placeholder'];
			$value       = get_option( $field );
			printf( '<input type="text" value="%s" name="%s" id="%s" class="regular-text" placeholder="%s" />', esc_attr( $value ), esc_attr( $field ), esc_attr( $field ), esc_attr( $placeholder ) );
		}
	}
	new TK_Profiles_Admin();
}
