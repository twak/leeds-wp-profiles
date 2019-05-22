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
	<div class="wrapper-xs wrapper-pd">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="jadu-cms">
				<?php the_content(); ?>
				<?php do_action( 'tk_profiles_after_content', $profile_id, get_the_title() ); ?>
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

    <div class="row card-flat  skin-bd-b " style="min-height:4em;"></div>

    <?php

    // Page is not split by category.
    while ($loop->have_posts()) {

        $loop->the_post();
        $profile_link = apply_filters('tk_profile_url', '', get_the_id());
        $name = get_the_title();

        ?>

        <!--        <div class="card-flat card-stacked-xs skin-bd-b skin-box-module">-->

        <div class="row card-flat  skin-bd-b " style="min-height:11em;">
            <div>
                <div class="col-sm-2">
                    <a href="<?php echo esc_url($profile_link); ?>">
                        <?php
                        if (has_post_thumbnail()) {
                            // Check if Thumbnail exists.
                            ?>
                            <div>
                                <img width="140em" src='<?php the_post_thumbnail_url('sq512'); ?>'
                                     alt='"<?php echo esc_attr($name); ?>"'/>
                            </div>
                            <?php
                        } else {
                            ?>

                            <div style="max-height:10em">
                                <div class="rs-img"></div>
                            </div>


                            <?php
                        }
                        ?>
                    </a>
                </div>
                <div class="col-sm-8">
                    <a href="<?php echo esc_url($profile_link); ?>">
                        <h2 style="font-family: freight-display-pro; margin-top:0.5em"><?php the_title(); ?></h2>

                        <?php
                        $value = get_field('all_authors');
                        if ($value)
                            echo('<h3 style="text-align: left">' . $value . '</h3>');
                        ?>
                    </a>
                </div>
                </a>
            </div>
        </div>

        <?php
    }
}

get_footer();
