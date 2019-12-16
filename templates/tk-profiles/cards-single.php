<?php
/**
 * Outputs a single card in a flat panel
 *
 * @package TK_Profiles
 */

// Get profile link.
$profile_link = apply_filters( 'tk_profile_url', '', get_the_id() );

// Get name from title.
$name = get_the_title();
?>
	<div class="container-row ">
		<div class="card-flat skin-box-module">
			<div class="card-img" style="max-width:200px;">
<?php
if ( has_post_thumbnail() ) {
	?>
				<div class="rs-img" style="background-image: url('<?php the_post_thumbnail_url( 'medium' ); ?>')">
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
				<span class="equalizer-inner" style="display:block;">
					<h3 class="heading-link-alt"><a href="<?php echo esc_url( $profile_link ); ?>" title="Profile of <?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></a></h3>
					<div class="note"><?php echo wp_trim_words( get_the_content(), 30, '...' ); ?></div>
					<a class="more" href="<?php echo esc_url( $profile_link ); ?>">View profile</a>
				</span>
			</div>
		</div>
	</div>
