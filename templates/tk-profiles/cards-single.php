<?php
/**
 * Outputs a single card in a flat panel
 *
 * @package TK_Profiles
 */

// Get profile link.
$profile_link = apply_filters( 'tk_profile_url', '', $post->ID );

// Get name from title.
$name = get_the_title();
?>
	<div class="tk-profiles-flat-card-wrapper">
		<div class="tk-profiles-flat-card">
			<div class="tk-profiles-card-img">
<?php
if ( has_post_thumbnail() ) {
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
				<h3 class="tk-profiles-card-heading"><a href="<?php echo esc_url( $profile_link ); ?>" title="Profile of <?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></a></h3>
				<div class="tk-profiles-card-text"><?php echo wp_trim_words( get_the_content(), 30, '...' ); ?></div>
				<a class="tk-profiles-card-more" href="<?php echo esc_url( $profile_link ); ?>">View profile</a>
			</div>
		</div>
	</div>
