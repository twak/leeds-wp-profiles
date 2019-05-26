<?php
/**
 * Advanced Custom Fields setup for Toolkit Profiles plugin
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_ACF' ) ) {
	/**
	 * Class to create custom fields for the Profile post type using the
	 * Advanced Custom Fields plugin
	 */
	class TK_Profiles_ACF {
		/**
		 * Constructor - registers all hooks with the WordPress API.
		 */
		public function __construct() {
			/**
			 * Sets up custom fields in ACF
			 */
			add_action( 'acf/init', array( $this, 'setup_acf' ), 10 );
		}

		/**
		 * Adds ACF custom fields and options page
		 */
		public static function setup_acf() {
			/**
			 * Create Profiles settings page
			 */
			acf_add_options_page( array(
				'page_title'  => 'Profiles Settings',
				'menu_title'  => 'Profiles Settings',
				'menu_slug'   => 'tk-profiles-settings',
				'capability'  => 'edit_posts',
				'redirect'    => false,
				'parent_slug' => 'edit.php?post_type=tk_profiles',
			) );

			/* Profile page settings (title and intro) */
			acf_add_local_field_group( array(
				'key'        => 'group_tk_profiles_page_settings',
				'title'      => 'Profile Page Settings',
				'fields'     => array(
					array(
						'key'          => 'field_tk_profiles_page_settings_title',
						'label'        => 'Page Title',
						'name'         => 'tk_profiles_page_settings_title',
						'type'         => 'text',
						'instructions' => 'Add a custom title to the profiles list page. If left blank the title of the page will be "Profiles".',
					),
					array(
						'key'   => 'field_tk_profiles_page_settings_category_title',
						'label' => 'Use the category name on category archives?',
						'name'  => 'tk_profiles_page_settings_category_title',
						'type'  => 'true_false',
						'ui'    => true,
					),
					array(
						'key'               => 'field_tk_profiles_page_settings_title_prefix',
						'label'             => 'Use the page title as a prefix on category archives?',
						'name'              => 'tk_profiles_page_settings_title_prefix',
						'type'              => 'true_false',
						'ui'                => true,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profiles_page_settings_category_title',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
					),
					array(
						'key'   => 'field_tk_profiles_page_settings_show_breadcrumb',
						'label' => 'Show Breadcrumb at top of page?',
						'name'  => 'tk_profiles_page_settings_show_breadcrumb',
						'type'  => 'true_false',
						'ui'    => true,
					),
					array(
						'key'          => 'field_tk_profiles_page_settings_introduction',
						'label'        => 'Archive Page Introduction',
						'name'         => 'tk_profiles_page_settings_introduction',
						'type'         => 'wysiwyg',
						'instructions' => 'Add an introduction at the top of the profiles list page. Category archives use the category description here if present.',
						'tabs'         => 'all',
						'toolbar'      => 'basic',
						'media_upload' => 0,
					),
					array(
						'key'          => 'field_tk_profiles_page_settings_content',
						'label'        => 'Archive Page Content (under profiles list)',
						'name'         => 'tk_profiles_page_settings_content',
						'type'         => 'wysiwyg',
						'instructions' => 'Add content to be displayed under the profiles on the profiles list page.',
						'tabs'         => 'all',
						'toolbar'      => 'basic',
						'media_upload' => 0,
					),
					array(
						'key'   => 'field_tk_profiles_page_settings_category_content',
						'label' => 'Display Archive Page Content on category archives?',
						'name'  => 'tk_profiles_page_settings_category_content',
						'type'  => 'true_false',
						'ui'    => true,
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
				'menu_order' => 1,
			) );

			/**
			 * Archive Profile page display settings
			 */
			acf_add_local_field_group(array(
				'key'        => 'tk_group_profiles_display',
				'title'      => 'Profiles Display',
				'fields'     => array(
					array(
						'key'           => 'field_tk_profile_display',
						'label'         => 'Profile Display',
						'name'          => 'tk_profile_display',
						'type'          => 'select',
						'required'      => 1,
						'choices'       => array(
							'all'    => 'Display all profiles in a single page',
							'by_cat' => 'Display profiles by category',
						),
						'allow_null'    => 1,
						'return_format' => 'value',
					),
					array(
						'key'               => 'field_tk_profile_by_category_rules',
						'label'             => 'Profile Display (by category) rules',
						'name'              => 'tk_profile_display_by_category',
						'type'              => 'repeater',
						'instructions'      => 'Select the desired layout options for each profile category',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profile_display',
									'operator' => '==',
									'value'    => 'by_cat',
								),
							),
						),
						'layout'            => 'table',
						'button_label'      => 'Add Rule',
						'sub_fields'        => array(
							array(
								'key'           => 'field_tk_profile_category',
								'label'         => 'Category',
								'name'          => 'profile_category',
								'type'          => 'taxonomy',
								'taxonomy'      => 'tk_profile_category',
								'field_type'    => 'select',
								'return_format' => 'id',
								'multiple'      => 0,
							),
							array(
								'key'           => 'field_tk_category_layout',
								'label'         => 'Layout',
								'name'          => 'category_layout',
								'type'          => 'radio',
								'choices'       => array(
									'table_layout' => 'Table Layout',
									'card_layout'  => 'Card Layout',
								),
								'default_value' => 'table_layout',
								'layout'        => 'vertical',
								'return_format' => 'value',
							),
							array(
								'key'           => 'field_tk_category_order',
								'label'         => 'Order',
								'name'          => 'category_order',
								'type'          => 'radio',
								'choices'       => array(
									'alphabetical' => 'Alphabetical by surname',
									'menu_order'   => 'Profile order',
								),
								'default_value' => 'alphabetical',
								'layout'        => 'vertical',
								'return_format' => 'value',
							),
						),
					),
					array(
						'key'               => 'field_tk_profiles_page_settings_template',
						'label'             => 'Layout',
						'name'              => 'tk_profiles_page_settings_template',
						'type'              => 'select',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profile_display',
									'operator' => '==',
									'value'    => 'all',
								),
							),
						),
						'choices'           => array(
							'table_layout' => 'Table Layout',
							'card_layout'  => 'Card Layout',
						),
						'default_value'     => 'table_layout',
						'return_format'     => 'value',
					),
					array(
						'key'               => 'field_tk_profiles_page_settings_profiles_order',
						'label'             => 'Profiles order',
						'name'              => 'tk_profiles_page_settings_profiles_order',
						'type'              => 'select',
						'instructions'      => 'Select to order profiles by alphabetically or category.',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profile_display',
									'operator' => '==',
									'value'    => 'all',
								),
							),
						),
						'choices'           => array(
							'alphabetical'          => 'Alphabetical by surname',
							'menu_order'            => 'Profile order',
							'alphabetical_category' => 'Alphabetical by surname (grouped by category)',
							'menu_order_category'   => 'Profile order (grouped by category)',
						),
						'default_value'     => 'alphabetical',
						'return_format'     => 'value',
					),
					array(
						'key'               => 'field_tk_table_view_fields',
						'label'             => 'Fields to include in table view',
						'name'              => 'tk_table_view_fields',
						'type'              => 'checkbox',
						'instructions'      => 'Select the fields you want to show as columns in table views',
						'choices'           => array(
							'featured_image'            => 'Profile Image',
							'post_title'                => 'Full name',
							'tk_profiles_email'         => 'Email',
							'tk_profiles_telephone'     => 'Telephone',
							'tk_profiles_faculty'       => 'Faculty',
							'tk_profiles_school'        => 'School',
							'tk_profiles_job_title'     => 'Job title',
							'tk_profiles_location'      => 'Location',
							'tk_profiles_research_area' => 'Research Area',
						),
						'default_value'     => array(
							'post_title'            => 'Full name',
							'tk_profiles_email'     => 'Email',
							'tk_profiles_telephone' => 'Telephone',
							'tk_profiles_job_title' => 'Job title',
						),
						'layout'            => 'vertical',
						'toggle'            => 0,
						'return_format'     => 'array',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_tk_profile_display',
									'operator' => '==',
									'value'    => 'all',
								),
								array(
									'field'    => 'field_tk_profiles_page_settings_template',
									'operator' => '==',
									'value'    => 'table_layout',
								),
							),
							array(
								array(
									'field'    => 'field_tk_profile_display',
									'operator' => '==',
									'value'    => 'by_cat',
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
				'menu_order' => 2,
			));

			/**
			 * Single profile page display settings
			 */
			acf_add_local_field_group( array(
				'key'        => 'group_tk_profiles_single_settings',
				'title'      => 'Single profile page settings',
				'fields'     => array(
					array(
						'key'          => 'field_tk_profiles_single_settings_related',
						'label'        => 'Related profiles',
						'name'         => 'tk_profiles_single_settings_related',
						'type'         => 'checkbox',
						'instructions' => 'Ticking this box will make profiles related by category appear at the bottom of every profile page.',
						'choices'      => array(
							'show_related' => 'Show related profiles on the profile page',
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
				'menu_order' => 4,
			) );

			/**
			 * Single Profile Post Fields
			 */
			acf_add_local_field_group( array(
				'key'                   => 'group_tk_profiles_single_fields',
				'title'                 => 'Profile Facts',
				'fields'                => array(

					array(
						'key'   => 'field_tk_profiles_job_title',
						'label' => 'Job title',
						'name'  => 'tk_profiles_job_title',
						'type'  => 'text',
					),
                    array(
                        'key'   => 'field_tk_profiles_bibtex_name',
                        'label' => 'Bibtex name',
                        'name'  => 'tk_profiles_bibtex_name',
                        'type'  => 'text',
                    ),
					array(
						'key'   => 'field_tk_profiles_external_link',
						'label' => 'External Profile Link',
						'name'  => 'tk_profiles_external_link',
						'type'  => 'url',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'tk_profiles',
						),
					),
				),
				'label_placement'       => 'left',
				'instruction_placement' => 'field',
				'position'              => 'acf_after_title',
			) );

			/**
			 * External link profile
			 */
			acf_add_local_field_group( array(
				'key'      => 'group_tk_profiles_external_link_flag',
				'title'    => 'External profile',
				'fields'   => array(
					array(
						'key'          => 'field_tk_profiles_external_link_flag',
						'name'         => 'tk_profiles_external_link_flag',
						'type'         => 'checkbox',
						'instructions' => 'Ticking this box will make this profile link to the external profile.',
						'choices'      => array(
							'external_link' => 'Make this profile external',
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'tk_profiles',
						),
					),
				),
				'position' => 'side',
			));
		}
	}
	new TK_Profiles_ACF();
}
