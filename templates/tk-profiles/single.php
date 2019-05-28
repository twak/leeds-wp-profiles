<?php
/**
 * Single Profile template for the Toolkit Profiles plugin
 *
 * @package TK_Profiles
 */

/* redirect this page if set to use an external profile */
$profile_id = get_queried_object_id();

// Redirection.
$external_url = apply_filters( 'tk_profile_url', '', $profile_id );
$permalink = get_permalink( $profile_id );
if ( $external_url !== $permalink ) {
	wp_safe_redirect( $external_url );
	exit;
}

get_header();

if ( get_field( 'tk_profiles_page_settings_show_breadcrumb', 'option' ) ) {
	// Custom title.
	$profiles_page_title = ( get_field( 'tk_profiles_page_settings_title', 'option' ) ? : 'Profiles' );
	$profiles_page_url   = get_post_type_archive_link( 'tk_profiles' );
	?>
	<div class="tk-profiles-breadcrumb-wrapper">
		<ul class="tk-profiles-breadcrumb">
			<li><a href="<?php echo esc_url( site_url() ); ?>">Home</a></li>
			<li><a href="<?php echo esc_url( $profiles_page_url ); ?>"><?php echo esc_html( $profiles_page_title ); ?></a></li>
			<li><?php the_title(); ?></li>
		</ul>
	</div>
	<?php
}
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
	    <div class="main wrapper-lg" style="margin-top:1em">
        <div class="wrapper-xs-pd" style="margin-right: 1em; margin-left:1em">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="jadu-cms">
				<?php the_content(); ?>
				<?php do_action( 'tk_profiles_after_content', $profile_id, get_the_title() ); ?>
			</div>
		</div>
	</div>
        </div>
		<?php
	}
}

?>

<?php
$args = array(
    'post_type' => 'projects',
    'meta_query' => array(
        array(
            'key' => 'authors', // name of custom field
            'value' => '"' . get_the_ID() . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
            'compare' => 'LIKE'
        )
    )
);


$loop = new WP_Query($args);

if ($loop->have_posts()) {

    ?>

    <div class="main wrapper-lg">
    <div class="wrapper-xs-pd" style="margin-right: 1em; margin-left:1em">

    <h2>Projects:</h2>

    <?php

    // Page is not split by category.
    while ($loop->have_posts()) {
        $loop->the_post();
        twak_inner_project();
    }?>
    </div> </div> <?php
}

get_footer();
