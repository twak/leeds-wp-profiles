<?php
/**
 * Archive template for the Toolkit Profiles plugin
 * To handle layout for all profiles display
 *
 * @package TK_Profiles
 */

// Get layout.
$template = get_field( 'tk_profiles_page_settings_template', 'option' );

// Collect query args.
$args = array(
	'post_type'      => 'tk_profiles',
	'posts_per_page' => -1,
	'order'          => 'ASC',
);

// Profiles order.
$profiles_order = get_field( 'tk_profiles_page_settings_profiles_order', 'option' );
if ( 'alphabetical' === $profiles_order || 'alphabetical_category' === $profiles_order ) {
	// Order profiles by last name (alphabetical).
	$args['meta_key'] = 'tk_profiles_last_name';
	$args['orderby']  = 'meta_value';
} else {
	// Order profiles by profile order.
	$args['orderby'] = 'menu_order';
}
// Check for taxonomy archive(?).
if ( is_tax( 'tk_profile_category' ) ) {
	$term              = get_queried_object();
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'tk_profile_category',
			'field'    => 'slug',
			'terms'    => $term->slug,
		),
	);
}

// New query.
$loop = new WP_Query( $args );

if ( $loop->have_posts() ) {

	// Cards or tables?
	$template_name = ( 'card_layout' === $template ) ? 'cards' : 'table';

	if ( 'menu_order_category' === $profiles_order || 'alphabetical_category' === $profiles_order ) {

		// Split the page up by category.
		$profile_categories = get_terms( array(
			'taxonomy'   => 'tk_profile_category',
			'hide_empty' => true,
		) );
		if ( count( $profile_categories ) > 1 ) {
			foreach ( $profile_categories as $profile_category ) {
				?>
<div class="tk-profiles-text-wrapper">
	<h4 class="tk-profiles-heading"><?php echo esc_html( $profile_category->name ); ?></h4>
</div>
				<?php
				load_template( apply_filters( 'tk_profiles_template', $template_name, 'header' ), false );
				while ( $loop->have_posts() ) {
					$loop->the_post();
					if ( has_term( $profile_category->term_id, 'tk_profile_category' ) ) {
						load_template( apply_filters( 'tk_profiles_template', $template_name, 'row' ), false );
					}
				}
				$loop->rewind_posts();
				load_template( apply_filters( 'tk_profiles_template', $template_name, 'footer' ), false );
			}
			// End looping through categories.
		} else {
			// One or less categories found.
			load_template( apply_filters( 'tk_profiles_template', $template_name, 'header' ), false );
			while ( $loop->have_posts() ) {
				$loop->the_post();
				load_template( apply_filters( 'tk_profiles_template', $template_name, 'row' ), false );
			}
			load_template( apply_filters( 'tk_profiles_template', $template_name, 'footer' ), false );
		}
	} else {
		// Page is not split by category.
		load_template( apply_filters( 'tk_profiles_template', $template_name, 'header' ), false );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			load_template( apply_filters( 'tk_profiles_template', $template_name, 'row' ), false );
		}
		load_template( apply_filters( 'tk_profiles_template', $template_name, 'footer' ), false );
	}
}
