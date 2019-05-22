<?php
/**
 * Profiles plugin help system
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_Help' ) ) {
	/**
	 * Class to provide help text for the WordPress help system
	 */
	class TK_Profiles_Help {
		/**
		 * Constructor - adds action based on current screen.
		 */
		public function __construct() {
			add_action( 'current_screen', array( $this, 'add_help_tabs' ) );
		}

		/**
		 * Adds tabs to the help dropdown.
		 */
		public function add_help_tabs() {
			$current_screen = get_current_screen();
			// Pages to display Help tab on.
			$pages = array(
				'edit-tk_profiles',
				'tk_profiles',
				'edit-tk_profile_category',
				'profiles_page_tk-profiles-settings',
			);

			if ( in_array( $current_screen->id, $pages, true ) ) {
				$admin_url            = get_admin_url();
				$profiles_archive_url = get_post_type_archive_link( 'tk_profiles' );

				$help_text_overview  = '<p>This screen provides access to all of your profiles. You can use Profiles to display staff or membership information on this website.</p>';
				$help_text_overview .= sprintf( '<p>A page displaying all the profiles will be will be displayed at <a target="_blank" href="%1$s">%1$s</a></p>', esc_url( $profiles_archive_url ) );

				$current_screen->add_help_tab(
					array(
						'id'      => 'profiles_help_overview',
						'title'   => 'Overview',
						'content' => $help_text_overview,
					)
				);

				$help_text_bulk_actions  = '<p>You can also edit or move multiple profiles to the bin at once. Select the profiles you want to act on using the checkboxes, then select the action you want to take from the Bulk Actions menu and click Apply.</p>';
				$help_text_bulk_actions .= '<p>When using Bulk Edit, you can change the Profile Categories, Status, etc for all selected profiles at once. To remove a profile from the grouping, just click the x next to its name in the Bulk Edit area that appears.</p>';

				$current_screen->add_help_tab(
					array(
						'id'      => 'profiles_help_bulk_actions',
						'title'   => 'Bulk Actions',
						'content' => $help_text_bulk_actions,
					)
				);

				$help_text_display  = '<h2>Displaying profiles</h2>';
				$help_text_display .= '<p>You can display selected profiles within standard pages, news items, etc, using "shortcodes".</p>';
				$help_text_display .= '<h3>Using Profile shortcodes</h3>';
				$help_text_display .= '<p>Shortcodes can be inserted into the main content area of any page or other post type using square brackets. For example:</p>';
				$help_text_display .= '<p>Typing <pre>[tk_profile id="34"]</pre> would display the profile with the ID of 34.</p>';
				$help_text_display .= '<p>To find the ID of a profile gor to the "Edit" screen for that profile and check the url</p>';
				$help_text_display .= '<p>Example: <em>ID highlighted in red</em></p><pre>{dashboard url}post.php?post=<span style="color: red;">123</span>&amp;action=edit</pre>';
				$help_text_display .= '<h3>Displaying multiple profiles</h3>';
				$help_text_display .= '<p>You can also display multiple by using the <code>[tk_profiles]</code> shortcode and specifying either multiple IDs or a Profile Category</p>';
				$help_text_display .= '<p>Examples:</p><pre>[tk_profiles ids="23,45,62"]</pre><pre>[tk_profiles category="External affiliates"]</pre>';
				$help_text_display .= '<h4>Formatting</h4>';
				$help_text_display .= '<p>When using the shortcode to display multiple categories, you have a number of options as to how it should display. For example:</p>';
				$help_text_display .= '<p><code>[tk_profiles category="External affiliates" format="cards"]</code> would display the the selected profiles in card format (default).</p>';
				$help_text_display .= '<p><code>[tk_profiles category="External affiliates" format="list"]</code> would display the the selected profiles in a list format.</p>';
				$help_text_display .= '<p><code>[tk_profiles category="External affiliates" format="table"]</code> would display the the selected profiles in a table format.</p>';
				$help_text_display .= '<p>If you choose the "table" format, you can specify which profile fields display in the table:</p>';
				$help_text_display .= '<p><code>[tk_profiles category="Staff" format="table" fields="name,email,image"]</code> would display just the name, email and picture.</p>';

				$current_screen->add_help_tab(
					array(
						'id'      => 'profiles_help_display',
						'title'   => 'Displaying Profiles',
						'content' => $help_text_display,
					)
				);
			}
		}
	}
	new TK_Profiles_Help();
}
