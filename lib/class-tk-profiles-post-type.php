<?php
/**
 * Custom post type and taxonomy for Toolkit Profiles plugin
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_Post_Type' ) ) {
	/**
	 * Class to register custom post type and taxonomy for Profiles
	 */
	class TK_Profiles_Post_Type {
		/**
		 * Constructor - registers hooks with WordPress API
		 */
		public function __construct() {
			/**
			 * Add profiles Post Type taxonomy
			 * added with priority LESS THAN post type registration
			 * to ensure the rewrite slug is not overwritten
			 */
			add_action( 'acf/init', array( $this, 'create_taxonomy' ), 8 );

			/**
			 * Add profiles Custom Post Type
			 * added with priority GREATER THAN taxonomy registration
			 * to ensure the rewrite slug is not overwritten
			 */
			add_action( 'acf/init', array( $this, 'create_post_type' ), 9 );

			/* Add post type to body class */
			add_filter( 'body_class', array( $this, 'add_post_type_to_body_class' ) );

			/* updated messages for admin screens */
			add_filter( 'post_updated_messages', array( $this, 'codex_profiles_updated_messages' ) );
		}

		/**
		 * Creates a profiles taxonomy
		 */
		public function create_taxonomy() {
			$profile_cat_slug_setting = get_option( 'tk_profile_categories_base' );
			$profile_cat_slug         = $profile_cat_slug_setting ? $profile_cat_slug_setting : 'profile_type';

			register_taxonomy( 'tk_profile_category', array( 'tk_profiles' ), array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => 'Profile Categories',
					'singular_name'     => 'Profile Category',
					'search_items'      => 'Search Profile Categories',
					'all_items'         => 'All Profile Categories',
					'parent_item'       => 'Parent Profile Category',
					'parent_item_colon' => 'Parent Profile Category:',
					'edit_item'         => 'Edit Profile Category',
					'update_item'       => 'Update Profile Category',
					'add_new_item'      => 'Add New Profile Category',
					'new_item_name'     => 'New Profile Category',
					'menu_name'         => 'Profile Categories',
				),
				'show_ui'           => true,
				'query_var'         => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => $profile_cat_slug,
					'with_front' => false,
				),
			) );
		}

		/**
		 * Creates the profiles post type
		 */
		public function create_post_type() {
			$profile_slug_setting = get_option( 'tk_profiles_base' );
			$profile_slug         = $profile_slug_setting ? $profile_slug_setting : 'profiles';

			register_post_type( 'tk_profiles', array(
				'labels'       => array(
					'name'                  => 'People',
					'singular_name'         => 'Person',
					'add_new'               => 'Add New',
					'add_new_item'          => 'Add New Person',
					'edit'                  => 'Edit',
					'edit_item'             => 'Edit Person',
					'new_item'              => 'New Person',
					'view'                  => 'View Person',
					'view_item'             => 'View Person',
					'view_items'            => 'View People',
					'search_items'          => 'Search People',
					'not_found'             => 'No Profile found',
					'not_found_in_trash'    => 'No Profile found in Trash',
					'all items'             => 'All Profiles',
					'archives'              => 'Profile archives',
					'insert into item'      => 'Insert into Person',
					'uploaded_to_this_item' => 'Uploaded to this Person',
				),
				'public'       => true,
				'hierarchical' => true,
				'has_archive'  => true,
				'supports'     => array(
					'title',
					'editor',
					'thumbnail',
				),
				'rewrite'      => array(
					'slug'       => $profile_slug,
					'with_front' => false,
				),
				'menu_icon'    => 'dashicons-admin-users',
				'can_export'   => true,
			) );
		}

		/**
		 * Adds the post type to the body class with tk- prefix
		 *
		 * @param array $classes to add to body.
		 * @return array $classes modified with post typ added.
		 */
		public function add_post_type_to_body_class( $classes ) {
			if ( 'tk_profiles' === get_post_type() ) {
				$classes[] = sanitize_html_class( 'tk-profiles' );
			}
			return $classes;
		}

		/**
		 * Profile update messages.
		 * See /wp-admin/edit-form-advanced.php
		 *
		 * @param array $messages Existing post update messages.
		 * @return array Amended post update messages with new update messages.
		 */
		public function codex_profiles_updated_messages( $messages ) {
			$post      = get_post();
			$post_type = get_post_type( $post );
			if ( 'tk_profiles' === $post_type ) {
				$permalink         = get_permalink( $post->ID );
				$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
				$preview_link      = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), 'Preview profile' );
				$view_link         = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), 'View profile' );

				$messages['tk_profiles'] = array(
					0  => '', // Unused. Messages start at index 1.
					1  => 'Profile updated.' . $view_link,
					2  => 'Custom profile field updated.',
					3  => 'Custom profile field deleted.',
					4  => 'Profile updated.',
					5  => 'Profile restored to revision.',
					6  => 'Profile published.' . $view_link,
					7  => 'Profile saved.',
					8  => 'Profile submitted.' . $preview_link,
					9  => sprintf( 'Profile scheduled for: <strong>%1$s</strong>.%s', date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ), $view_link ) ),
					10 => 'Profile draft updated.' . $preview_link,
				);
			}
			return $messages;
		}
	}
	new TK_Profiles_Post_Type();
}
