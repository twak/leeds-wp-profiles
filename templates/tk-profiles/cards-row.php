<?php
/**
 * Outputs a single card in a stacked set
 *
 * @package TK_Profiles
 */

// Get profile link.
$profile_link = apply_filters( 'tk_profile_url', '', $post->ID );

// Get name from title.
$name = get_the_title();
?>
	<div class="col-xs-12 col-ms-6 col-sm-4 col-md-3">
		<div class="card-flat card-stacked-xs skin-bd-b skin-box-module">
			<div class="card-img">
<?php
if ( has_post_thumbnail() ) {
	// Check if Thumbnail exists.
	?>
				<div class="rs-img" style="background-image: url('<?php the_post_thumbnail_url( 'sq512' ); ?>')">
					<a href="<?php echo esc_url( $profile_link ); ?>"><img src="<?php the_post_thumbnail_url( 'medium' ); ?>" alt="<?php echo esc_attr( $name ); ?>"></a>
				</div>
	<?php
} else {
	?>
				<div class="rs-img">
					<a href="<?php echo esc_url( $profile_link ); ?>"></a>
				</div>
	<?php
}
?>
			</div>
			<div class="card-content equalize-inner">
				<h3 class="heading-link text-center">
					<a href="<?php echo esc_url( $profile_link ); ?>">
<?php
echo esc_html( $name );
if ( get_field( 'tk_profiles_external_link_flag' ) ) {
	?>
						<span class="tk-icon-external" aria-hidden="true"></span>
	<?php
}
?>
					</a>
				</h3>
				<h4 class="heading-related text-center">
<?php
if ( get_field( 'tk_profiles_job_title' ) ) {
	the_field( 'tk_profiles_job_title' );
}
?>
				</h4>
			</div>
		</div>
	</div>
