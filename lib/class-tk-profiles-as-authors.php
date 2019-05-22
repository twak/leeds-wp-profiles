<?php
/**
 * Using profiles as authors - added as an optional extra in plugin settings.
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_As_Authors' ) ) {
	/**
	 * Provides additional feature whereby you canuse profiles as the authors of blog
	 * posts. Allows for multiple authors, and embeds a short bio, picture and link on
	 * the post.
	 */
	class TK_Profiles_As_Authors {
		/**
		 * Register all hooks with WordPress API
		 */
		public function __construct() {
			/**
			 * Register post author custom fields
			 * as one of these fields switches the functionality of this add-on on and off
			 * these are added whether this feature is enabled or not
			 */
			add_action( 'acf/init', array( $this, 'register_post_authors' ), 11 );

			/**
			 * Add all the hooks for this plugin feature
			 */
			add_action( 'acf/init', array( $this, 'register_post_authors_hooks' ), 12 );
		}

		/**
		 * Set up ACF fields for extension which allows
		 * specification of profiles as authors of posts
		 */
		public function register_post_authors() {
			/**
			 * ACF Fields for Profile settings page
			 */
			acf_add_local_field_group( array(
				'key'        => 'group_tk_profiles_as_authors_settings',
				'title'      => 'Author Integration',
				'fields'     => array(
					array(
						'key'          => 'field_tk_profiles_as_authors',
						'label'        => 'Use profiles as Authors',
						'name'         => 'tk_profiles_as_authors',
						'type'         => 'true_false',
						'instructions' => 'Enabling this option will allow you to select one or more profiles as authors on blog posts. It will also display the profile "cards" for the authors at the foot of each post, and include a list of author posts within the profile page',
						'ui'           => true,
					),
					array(
						'key'               => 'field_tk_profiles_as_authors_top_credit',
						'label'             => 'Credit authors at top of posts?',
						'name'              => 'top_credit',
						'type'              => 'true_false',
						'ui'                => true,
						'default_value'     => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profiles_as_authors',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
					),
					array(
						'key'               => 'field_tk_profiles_as_authors_top_prefix',
						'label'             => 'Prefix for top credit on posts',
						'name'              => 'top_prefix',
						'type'              => 'text',
						'placeholder'       => 'Written by ',
						'default_value'     => 'Written by ',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profiles_as_authors',
									'operator' => '==',
									'value'    => '1',
								),
								array(
									'field'    => 'field_tk_profiles_as_authors_top_credit',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
					),
					array(
						'key'               => 'field_tk_profiles_as_authors_bottom_credit',
						'label'             => 'Credit authors at bottom of posts?',
						'name'              => 'bottom_credit',
						'type'              => 'true_false',
						'ui'                => true,
						'default_value'     => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profiles_as_authors',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
					),
					array(
						'key'               => 'field_tk_profiles_as_authors_profile_posts',
						'label'             => 'Add list of posts to profile pages?',
						'name'              => 'list_posts_on_profile_page',
						'type'              => 'true_false',
						'instructions'      => 'Please note that enabling this option will result in profiles which are External not redirecting to the External URL if the profile has been assigned to any posts',
						'ui'                => true,
						'default_value'     => 1,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profiles_as_authors',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
					),
				),
				'location'   => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'tk-profiles-settings',
						),
					),
				),
				'menu_order' => 3,
			) );

			/**
			 * Add ACF field to posts to allow choice of profiles for author(s)
			 */
			if ( get_field( 'tk_profiles_as_authors', 'option' ) ) {
				acf_add_local_field_group(array(
					'key'                   => 'group_5805dae7babfd',
					'title'                 => 'Post Author',
					'fields'                => array(
						array(
							'key'           => 'field_5805daf2d9cda',
							'label'         => 'Select one or more authors',
							'name'          => 'post_authors',
							'type'          => 'post_object',
							'instructions'  => '',
							'post_type'     => array(
								0 => 'tk_profiles',
							),
							'taxonomy'      => array(),
							'allow_null'    => 0,
							'multiple'      => 1,
							'return_format' => 'object',
							'ui'            => 1,
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'post',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'side',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => array(
						0 => 'author',
					),
					'active'                => 1,
					'description'           => '',
				) );
			}
		}

		/**
		 * Registers hooks for plugin extension
		 */
		public function register_post_authors_hooks() {
			/**
			 * Only run these hooks if the feature has been added in the plugin settings
			 */
			if ( get_field( 'tk_profiles_as_authors', 'option' ) ) {
				/* add author data to posts */
				add_action( 'tk_content_before', array( $this, 'add_author_credit' ) );
				add_action( 'tk_content_after', array( $this, 'add_author_bio' ) );

				/* change author column for posts */
				add_action( 'manage_edit-post_columns', array( $this, 'add_authors_column' ) );
				add_action( 'manage_post_posts_custom_column', array( $this, 'show_authors_column' ), 10, 2 );

				/**
				 * Adds custom column to profiles table in admin
				 */
				add_action( 'manage_edit-tk-profiles_columns', array( $this, 'add_profiles_columns' ) );
				add_action( 'manage_profiles_posts_custom_column', array( $this, 'show_profiles_columns' ), 11, 2 );

				/* adds a list of author posts on profile page */
				add_action( 'tk_profiles_after_content', array( $this, 'add_author_posts' ), 10, 2 );

				/* updates the metadata for the number of posts by an author when a post is saved */
				add_action( 'save_post', array( $this, 'update_profile_numberposts_on_save' ) );
			}
		}

		/**
		 * Adds author credits above the content
		 */
		public function add_author_credit() {
			if ( 'post' === get_post_type( get_the_ID() ) && get_field( 'top_credit', 'option' ) ) {
				$out     = '';
				$authors = get_field( 'post_authors' );
				if ( is_array( $authors ) && count( $authors ) ) {
					$prefix = get_field( 'top_prefix', 'option' );
					if ( ! $prefix ) {
						$prefix = 'Written by';
					}
					$prefix = trim( $prefix );
					$out   .= '<div class="author-credit-above">';
					if ( count( $authors ) === 1 ) {
						// External/Internal link flag.
						$url  = ( get_field( 'tk_profiles_external_link_flag', $authors[0]->ID ) ) ? get_field( 'tk_profiles_external_link', $authors[0]->ID ) : get_permalink( $authors[0]->ID );
						$out .= sprintf( '<p>%s <a href="%s" title="Profile of %s">%s</a></p>', esc_html( $prefix ), esc_url( $url ), esc_attr( $authors[0]->post_title ), esc_html( $authors[0]->post_title ) );
					} else {
						$author_html = array();
						foreach ( $authors as $author ) {
							$url           = ( get_field( 'tk_profiles_external_link_flag', $author->ID ) ) ? get_field( 'tk_profiles_external_link', $author->ID ) : get_permalink( $author->ID );
							$author_html[] = sprintf( '<a href="%s" title="Profile of %s">%s</a>', esc_url( $url ), esc_attr( $author->post_title ), esc_html( $author->post_title ) );
						}
						$last = array_pop( $author_html );
						$out .= sprintf( '<p>%s %s and %s</p>', esc_html( $prefix ), implode( ', ', $author_html ), $last );
					}
					$out .= '</div>';
				}
				print( $out );
			}
		}

		/**
		 * Adds author bios after the content
		 */
		public function add_author_bio() {
			if ( 'post' === get_post_type( get_the_ID() ) && get_field( 'bottom_credit', 'option' ) ) {
				$out     = '';
				$authors = get_field( 'post_authors' );
				if ( is_array( $authors ) && count( $authors ) ) {
					$out .= '<div class="container-row ">';
					if ( count( $authors ) === 1 ) {
						$out .= '<h3 class="h2-lg heading-underline">Author</h3><div class="clearfix">';
					} else {
						$out .= '<h3 class="h2-lg heading-underline">Authors</h3><div class="clearfix">';
					}
					// Get author IDs.
					$author_ids = array();
					foreach ( $authors as $author ) {
						$author_ids[] = $author->ID;
					}
					// Use these in new WP_Query (template expects a loop).
					$author_query = new WP_Query( array(
						'post_type' => 'tk_profiles',
						'post__in'  => $author_ids,
						'orderby'   => 'post__in',
					) );
					if ( $author_query->have_posts() ) {
						while ( $author_query->have_posts() ) {
							$author_query->the_post();
							ob_start();
							load_template( apply_filters( 'tk_profiles_template', 'cards', 'single' ), false );
							$out .= ob_get_clean();
						}
					}
					wp_reset_postdata();
					$out .= '</div></div>';
				}
				print( $out );
			}
		}

		/**
		 * Adds a list of posts by the author to the profile page
		 *
		 * @param integer $profile_id - ID of post for profile.
		 * @param string  $profile_title - author name.
		 */
		public function add_author_posts( $profile_id, $profile_title ) {
			if ( get_field( 'list_posts_on_profile_page', 'option' ) ) {
				$author_posts_query = $this->get_posts_for_profile( $profile_id );
				if ( $author_posts_query->have_posts() ) {
					printf( '<h3>Posts by %s</h3><ul>', $profile_title );
					while ( $author_posts_query->have_posts() ) {
						$author_posts_query->the_post();
						$authors = get_field( 'post_authors' );
						if ( count( $authors ) === 1 ) {
							printf( '<li><a href="%s">%s</a></li>', get_permalink(), get_the_title() );
						} else {
							$author_html = array();
							foreach ( $authors as $author ) {
								if ( $author->ID !== $profile_id ) {
									$url           = apply_filters( 'tk_profile_url', '', $author->ID );
									$author_html[] = sprintf( '<a href="%s" title="Profile of %s">%s</a>', $url, esc_attr( $author->post_title ), $author->post_title );
								}
							}
							if ( count( $author_html ) === 1 ) {
								$additional_authors = sprintf( ' (with %s)', $author_html[0] );
							} else {
								$last               = array_pop( $author_html );
								$additional_authors = sprintf( '(with %s and %s)', implode( ', ', $author_html ), $last );
							}
							printf( '<li><a href="%s">%s</a>%s</li>', get_permalink(), get_the_title(), $additional_authors );
						}
					}
				}
				wp_reset_postdata();
			}
		}

		/**
		 * Gets the posts assigned to a profile using a meta query
		 * the post_authors acf field saves profile IDs in a serializeed array, so
		 * we need to query using the profile ID in double quotes and a LIKE comparison
		 *
		 * @param integer $profile_id - ID of profile.
		 * @param integer $limit - to limit the number of authors this can return.
		 */
		public function get_posts_for_profile( $profile_id, $limit = false ) {
			$numberposts = intVal( $limit );
			if ( ! $numberposts ) {
				$numberposts = -1;
			}
			$args = array(
				'numberposts' => $numberposts,
				'post_type'   => 'post',
				'nopaging'    => true,
				'meta_query'  => array(
					array(
						'key'     => 'post_authors',
						'value'   => '"' . $profile_id . '"',
						'compare' => 'LIKE',
					),
				),
			);
			return new WP_Query( $args );
		}

		/**
		 * This is hooked to save_post to update the number of posts by profiles
		 *
		 * @param integer $post_id - ID of post.
		 */
		public function update_profile_numberposts_on_save( $post_id ) {
			// Make sure this is not a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}
			// make sure this is a post.
			$post_type = get_post_type( $post_id );
			if ( 'post' !== $post_type ) {
				return;
			}

			// Get the profile_authors field.
			$authors = get_field( 'post_authors', $post_id );
			if ( ! $authors ) {
				return;
			} else {
				foreach ( $authors as $author ) {
					$profile_posts = $this->get_posts_for_profile( $author->ID, 1 );
					$this->update_profile_numberposts( $author->ID, $profile_posts->found_posts );
				}
			}
		}

		/**
		 * Saves number of posts attributed to profile in postmeta.
		 *
		 * @param integer $profile_id - ID of profile.
		 * @param integer $numberposts - number of posts to update field.
		 */
		public function update_profile_numberposts( $profile_id, $numberposts ) {
			update_post_meta( $profile_id, 'profile_numberposts', $numberposts );
		}

		/**
		 * Gets number of posts attributed to profile
		 *
		 * @param integer $profile_id - ID of post used for profile.
		 * @return integer $num - number of posts authored.
		 */
		public function get_profile_numberposts( $profile_id ) {
			$num = get_post_meta( $profile_id, 'profile_numberposts', true );
			if ( $num ) {
				return intVal( $num );
			}
			return 0;
		}

		/**
		 * Adds a new columns to posts table for authors (set via profiles)
		 *
		 * @param array $posts_columns - columns for posts list table.
		 * @return array $posts_columns - modified columns for posts list table with author added.
		 */
		public function add_authors_column( $posts_columns ) {
			$new_column = array(
				'tkprofiles' => 'Author profile(s)',
			);
			return array_merge( $posts_columns, $new_column );
		}

		/**
		 * Shows the authors assigned to a post from the profiles section
		 * rather than the post_author.
		 *
		 * @param string  $column_id - ID of column being shown.
		 * @param integer $post_id - ID of post in row.
		 */
		public function show_authors_column( $column_id, $post_id ) {
			if ( 'tkprofiles' === $column_id ) {
				$authors = get_field( 'post_authors', $post_id );
				if ( is_array( $authors ) && count( $authors ) ) {
					$author_html = array();
					foreach ( $authors as $author ) {
						$url           = admin_url( 'post.php?action=edit&post=' . $author->ID );
						$author_html[] = sprintf( '<a href="%s" title="Edit profile for %s">%s</a>', esc_url( $url ), esc_attr( $author->post_title ), esc_html( $author->post_title ) );
					}
					echo implode( ', ', $author_html );
				} else {
					echo '-';
				}
			}
		}

		/**
		 * Adds post count column to the profiles listing table
		 * hooks into 'manage_edit-profiles_columns'
		 *
		 * @param array $posts_columns - array of columns for profiles admin table.
		 * @return array $new_posts_columns - array with one column added for the number of posts.
		 */
		public function add_profiles_columns( $posts_columns ) {
			$posts_columns['profile_numberposts'] = 'Posts';
			return $posts_columns;
		}

		/**
		 * Shows the post count in a column of the manage profiles table
		 * hooks into 'manage_profiles_posts_custom_column'
		 *
		 * @param string  $column_id - ID of column in profiles admin table.
		 * @param integer $profile_id - ID of profile being displayed in row.
		 */
		public function show_profiles_columns( $column_id, $profile_id ) {
			global $post;
			switch ( $column_id ) {
				case 'profile_numberposts':
					$post_count = $this->get_profile_numberposts( $profile_id );
					if ( $post_count ) {
						/**
						 * Update post counts for profiles
						 * this corrects post counts for authors when they are removed from a post
						 * as author (the save_post hook cannot be used for this) - need to find
						 * a better way of doing this? - refresh button?
						 */
						$profile_posts = $this->get_posts_for_profile( $profile_id, 1 );
						$this->update_profile_numberposts( $profile_id, $profile_posts->found_posts );
						$post_count = $profile_posts->found_posts;
					}
					echo $post_count;
					break;
			}
		}
	}
	new TK_Profiles_As_Authors();
}
