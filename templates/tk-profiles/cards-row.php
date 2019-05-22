<?php
/**
 * Outputs a single card in a stacked set
 *
 * @package TK_Profiles
 */

// Get profile link.
$profile_link = apply_filters( 'tk_profile_url', '', get_the_id() );

// Get name from title.
$name = get_the_title();
?>
	<div class="tk-profiles-stacked-card-wrapper">
		<div class="tk-profiles-stacked-card">
			<div class="tk-profiles-card-img">
<?php
if ( has_post_thumbnail() ) {
	// Check if Thumbnail exists.
	?>
				<div class="tk-profiles-card-img-bg" style="background-image: url('<?php the_post_thumbnail_url( 'medium' ); ?>')">
					<a href="<?php echo esc_url( $profile_link ); ?>"><img src="<?php the_post_thumbnail_url( 'medium' ); ?>" alt="<?php echo esc_attr( $name ); ?>"></a>
				</div>
	<?php
} else {
	?>
				<div class="tk-profiles-card-img-bg">
					<a href="<?php echo esc_url( $profile_link ); ?>"></a>
				</div>
	<?php
}
?>
			</div>
			<div class="tk-profiles-card-content">
				<h3 class="tk-profiles-card-heading">
					<a href="<?php echo esc_url( $profile_link ); ?>">
<?php
echo esc_html( $name );
if ( get_field( 'tk_profiles_external_link_flag' ) ) {
	?>
						<span class="tk-profiles-icon-external" aria-hidden="true"></span>
	<?php
}
?>
					</a>
				</h3>
				<h4 class="tk-profiles-card-subheading">
<?php
if ( get_field( 'tk_profiles_job_title' ) ) {
	the_field( 'tk_profiles_job_title' );
}
?>
				</h4>
			</div>
		</div>
	</div>
