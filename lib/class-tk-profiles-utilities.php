<?php
/**
 * Utilities used in Profiles plugin
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_Utilities' ) ) {
	/**
	 * Utility class - provides filters and actions used in the plugin.
	 */
	class TK_Profiles_Utilities {
		/**
		 * Constructor - registers hooks with the WordPress API.
		 */
		public function __construct() {
			// Adds a filter to get the URL for a profile.
			add_filter( 'tk_profile_url', array( $this, 'url_filter' ), 10, 2 );
			// Adds a filter to get the fields to display in table layouts.
			add_filter( 'tk_profile_table_fields', array( $this, 'table_fields' ) );
			// Adds a filter to turn a string containing comma-separated integers into an array.
			add_filter( 'csv_to_int', array( $this, 'csv_int' ) );
			// Adds a filter to turn a string containing comma-separated strings into an array.
			add_filter( 'csv_to_str', array( $this, 'csv_str' ) );
		}

		/**
		 * Profiles URL filter
		 * Will return the permalink to the profile page, unless the following conditions are met:
		 *  * A valid URL is entered in the external URL field
		 *  * The checkbox on the profile to make it external has been checked.
		 *  * Profiles as authors is disabled - or if enabled, the profile has been tagged to a post and tagged posts are listed on the profiles page
		 *
		 * @param string  $url - value to apply filter to (ignored).
		 * @param integer $profile_id - ID of profile page for user.
		 * @return string $url - URL of profile page.
		 */
		public function url_filter( $url, $profile_id ) {
			$external_link = get_field( 'tk_profiles_external_link', $profile_id );
			$url           = get_permalink( $profile_id );
			if ( get_field( 'tk_profiles_external_link_flag', $profile_id ) && false !== filter_var( $external_link, FILTER_VALIDATE_URL ) ) {
				// valid external link configured - now check to see if any posts have been assigned.
				$use_profiles_as_authors = get_field( 'tk_profiles_as_authors', 'option' );
				if ( ! $use_profiles_as_authors ) {
					// not using profiles as authors.
					$url = $external_link;
				} else {
					if ( ! get_field( 'list_posts_on_profile_page', 'option' ) ) {
						// not showing posts list on author pages.
						$url = $external_link;
					} else {
						if ( ! $this->is_author( $profile_id ) ) {
							// showing posts on author pages, but none assigned to profile yet.
							$url = $external_link;
						}
					}
				}
			}
			return $url;
		}

		/**
		 * Checks to see if a profile has been assigned as an author to any posts
		 *
		 * @param integer $profile_id - post ID for profile page.
		 * @return boolean
		 */
		public function is_author( $profile_id ) {
			$posts_count = get_post_meta( $profile_id, 'profile_numberposts', true );
			return ( ! empty( $posts_count ) ) ? true : false;
		}

		/**
		 * Gets an array of non-zero integers from a comma-delimited string
		 *
		 * @param string $str - comma-delimited list of numbers.
		 * @return array $ret - array of non-zero unique integers.
		 */
		public function csv_int( $str ) {
			/* split, trim, cast and weed */
			$num = array_unique( array_map( 'intVal', array_map( 'trim', explode( ',', $str ) ) ) );
			$ret = array();
			foreach ( $num as $n ) {
				if ( ! empty( $n ) && $n > 0 ) {
					$ret[] = $n;
				}
			}
			return $ret;
		}

		/**
		 * Gets an array of lowercase strings from a comma-delimited string
		 *
		 * @param string $str - comma-delimited list of strings.
		 * @return array $ret - array of non-empty unique lowercase strings.
		 */
		public function csv_str( $str ) {
			/* split, trim, change case and weed */
			$str = array_unique( array_map( 'strtolower', array_map( 'trim', explode( ',', $str ) ) ) );
			$ret = array();
			foreach ( $str as $s ) {
				if ( ! empty( $s ) ) {
					$ret[] = $s;
				}
			}
			return $ret;
		}

		/**
		 * Gets the fields configured for a table view
		 * These are set in ACF options, but can be overridden by the shortcode
		 * using the 'tk_profiles_get_table_fields' filter
		 */
		public function table_fields() {
			// Fields can be listed to include in the table.
			// This maps keywords from the shortcode to field names.
			$all_fields = array(
				'image'     => array(
					'value' => 'featured_image',
					'label' => 'Profile Image',
				),
				'name'      => array(
					'value' => 'post_title',
					'label' => 'Full name',
				),
				'title'     => array(
					'value' => 'tk_profiles_title',
					'label' => 'Title',
				),
				'firstname' => array(
					'value' => 'tk_profiles_first_name',
					'label' => 'First name',
				),
				'surname'   => array(
					'value' => 'tk_profiles_last_name',
					'label' => 'Last name',
				),
				'email'     => array(
					'value' => 'tk_profiles_email',
					'label' => 'Email',
				),
				'tel'       => array(
					'value' => 'tk_profiles_telephone',
					'label' => 'Telephone',
				),
				'faculty'   => array(
					'value' => 'tk_profiles_faculty',
					'label' => 'Faculty',
				),
				'school'    => array(
					'value' => 'tk_profiles_school',
					'label' => 'School',
				),
				'jobtitle'  => array(
					'value' => 'tk_profiles_job_title',
					'label' => 'Job title',
				),
				'location'  => array(
					'value' => 'tk_profiles_location',
					'label' => 'Location',
				),
				'research'  => array(
					'value' => 'tk_profiles_research_area',
					'label' => 'Research Area',
				),
			);

			$table_fields = array();

			// Shortcode hooks into this filter to pass a different set of fields for table views.
			$fields = apply_filters( 'tk_profiles_get_table_fields', array() );

			// get table fields to show.
			if ( ! empty( $fields ) ) {
				// validate.
				$fields = array_intersect( $fields, array_keys( $all_fields ) );
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$table_fields[] = $all_fields[ $field ];
					}
				}
			}
			if ( empty( $table_fields ) ) {
				$table_fields = get_field( 'tk_table_view_fields', 'option' );
				if ( ! $table_fields ) {
					return array(
						array(
							'value' => 'post_title',
							'label' => 'Full name',
						),
						array(
							'value' => 'tk_profiles_email',
							'label' => 'Email',
						),
						array(
							'value' => 'tk_profiles_telephone',
							'label' => 'Telephone',
						),
						array(
							'value' => 'tk_profiles_job_title',
							'label' => 'Job title',
						),
					);
				}
			}
			return $table_fields;
		}
	}
	new TK_Profiles_Utilities();
}
