<?php
/**
 * Archive template for the Toolkit Profiles plugin
 * To handle layout for category archive
 *
 * @package TK_Profiles
 */

// Table profile layout.
$args = array(
	'post_type'      => 'tk_profiles',
	'posts_per_page' => -1,
	'order'          => 'ASC',
);
// Get categories.
$profile_categories = get_terms( array(
	'taxonomy'   => 'tk_profile_category',
	'hide_empty' => true,
) );

// Set the requested (or default) term.
$term = false;
if ( is_tax( 'tk_profile_category' ) ) {
	// Term is specified.
	$term = get_queried_object();
} else {
	if ( have_rows( 'tk_profile_display_by_category', 'option' ) ) {
		// Get all rows from the repeater.
		$cats        = get_field( 'tk_profile_display_by_category', 'option' );
		$category_id = $cats[0]['profile_category'];
		$term        = get_term( $category_id );
	}
}

if ( $term ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'tk_profile_category',
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		),
	);

	$category_tabs = array();

	// Set profiles order and layout per category, and build tab navigation.
	if ( have_rows( 'tk_profile_display_by_category', 'option' ) ) {
		while ( have_rows( 'tk_profile_display_by_category', 'option' ) ) {
			the_row();
			// See if this rule applies to the selected category.
			$category_id = get_sub_field( 'profile_category' );

			if ( $category_id === $term->term_id ) {

				// Set order for this category.
				$profiles_order = get_sub_field( 'category_order' );
				if ( 'alphabetical' === $profiles_order ) {
					// Order profiles by last name (alphabetical).
					$args['meta_key'] = 'tk_profiles_last_name';
					$args['orderby']  = 'meta_value';
				} else {
					// Order profiles by profile order.
					$args['orderby'] = 'menu_order';
				}
				// Set layout for this category.
				$template      = get_sub_field( 'category_layout' );
				$template_name = ( 'card_layout' === $template ) ? 'cards' : 'table';
			}

			// Make a tab.
			$tabclass = ( $term->term_id === $category_id ) ? ' class="active"' : '';
			$category = get_term( $category_id );
			if ( $category->name ) {
				$category_tabs[] = sprintf( '<li%s><a href="%s">%s</a></li>', $tabclass, get_term_link( $category ), $category->name );
			}
		}
	}

	// if there are no settings for categories, set a default.
	if ( ! isset( $args['orderby'] ) ) {
		$args['meta_key'] = 'tk_profiles_last_name';
		$args['orderby']  = 'meta_value';
		$template_name    = 'table';
		$flag_show_images = false;
	}

	// New query.
	$loop = new WP_Query( $args );

	if ( $loop->have_posts() ) {

		// Only output headings if there is more than one category.
		if ( count( $category_tabs ) > 1 ) {
			?>
				<div class="tk-tabs-header" style="margin-bottom:1em;">
					<ul class="nav nav-tabs tk-nav-tabs pull-left">
			<?php
			foreach ( $category_tabs as $tab ) {
				print( $tab );
			}
			?>
					</ul>
				</div>
			<?php
		}
		load_template( apply_filters( 'tk_profiles_template', $template_name, 'header' ), false );

		while ( $loop->have_posts() ) {
			$loop->the_post();
			load_template( apply_filters( 'tk_profiles_template', $template_name, 'row' ), false );
		}
		load_template( apply_filters( 'tk_profiles_template', $template_name, 'footer' ), false );
	}
}
