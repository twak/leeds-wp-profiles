<?php
/**
 * Category archive template for the Toolkit Profiles plugin
 *
 * @package TK_Profiles
 */

// Term display settings.
$term = get_queried_object();

// Main profiles archive page URL.
$profiles_url = get_post_type_archive_link( 'tk_profiles' );

// ACF Profile page setting - Custom title.
$profiles_title = ( get_field( 'tk_profiles_page_settings_title', 'option' ) ? : 'Profiles' );

// ACF Profile page setting - Custom title prefix.
if ( get_field( 'tk_profiles_page_settings_category_title', 'option' ) ) {
	$pagetitle = $term->name;
	if ( get_field( 'tk_profiles_page_settings_title_prefix', 'option' ) ) {
		$pagetitle = $profiles_title . ': ' . $term->name;
	}
} else {
	$pagetitle = $profiles_title;
}

// ACF global description.
$global_intro = get_field( 'tk_profiles_page_settings_introduction', 'option' );
// Intro is category description or global intro from settings.
$intro = ( '' !== $term->description ) ? apply_filters( 'the_content', $term->description ) : $global_intro;
// Archive page content - first see if this is to be shown.
$outro = get_field( 'tk_profiles_page_settings_category_content', 'option' );
if ( $outro ) {
	$outro = get_field( 'tk_profiles_page_settings_content', 'option' );
}

get_header();

if ( get_field( 'tk_profiles_page_settings_show_breadcrumb', 'option' ) ) {
	?>
	<div class="wrapper-pd-xs">
		<ul class="breadcrumb">
			<li><a href="<?php echo esc_url( site_url() ); ?>">Home</a></li>
			<li><a href="<?php echo esc_url( $profiles_url ); ?>"><?php echo esc_html( $profiles_title ); ?></a></li>
	<?php
	// Get breadcrumbs for nested categories.
	if ( 0 !== $term->parent ) {
		$ancestors = array_reverse( get_ancestors( $term->term_id, 'tk_profile_category', 'taxonomy' ) );
		foreach ( $ancestors as $ancestor_id ) {
			$a       = get_term( $ancestor_id, 'tk_profile_category' );
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $ancestor_id ) ), esc_html( $a->name ) );
		}
	}
	?>
			<li><?php echo esc_html( $term->name ); ?></li>
		</ul>
	</div>
	<?php
}
?>

<div class="wrapper-md wrapper-pd">
	<h1 class="heading-underline"><?php echo esc_html( $pagetitle ); ?></h1>
<?php
if ( $intro ) {
	?>
	<div class="wrapper-md">
		<?php echo $intro; ?>
	</div>
	<?php
}
if ( $term ) {
	$args          = array(
		'post_type'      => 'tk_profiles',
		'posts_per_page' => -1,
		'order'          => 'ASC',
		'tax_query'      => array(
			array(
				'taxonomy' => 'tk_profile_category',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
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
				$category_tabs[] = sprintf( '<li%s><a href="%s">%s</a></li>', $tabclass, esc_url( get_term_link( $category ) ), esc_html( $category->name ) );
			}
		}
	}

	// If there are no settings for categories, set a default.
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

if ( $outro ) {
	?>
	<div class="wrapper-md">
		<?php echo $outro; ?>
	</div>
	<?php
}
?>
</div>
<?php
get_footer();
