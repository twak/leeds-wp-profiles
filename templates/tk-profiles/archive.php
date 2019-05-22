<?php
/**
 * Archive template for the Toolkit Profiles plugin
 *
 * @package TK_Profiles
 */

/* ACF Profile page settings */

// Custom title.
$profiles_page_title = ( get_field( 'tk_profiles_page_settings_title', 'option' ) ? : 'Profiles' );

// Intro (Lead text).
$intro = get_field( 'tk_profiles_page_settings_introduction', 'option' );

// Content (after list).
$outro = get_field( 'tk_profiles_page_settings_content', 'option' );

// Display logic.
$display = ( get_field( 'tk_profile_display', 'option' ) ? : 'all' );


?>

<?php get_header();

if ( get_field( 'tk_profiles_page_settings_show_breadcrumb', 'option' ) ) {
	?>
	<div class="tk-profiles-breadcrumb-wrapper">
		<ul class="tk-profiles-breadcrumb">
			<li><a href="<?php echo esc_url( site_url() ); ?>">Home</a></li>
			<li><?php echo esc_html( $profiles_page_title ); ?></li>
		</ul>
	</div>
	<?php
}
?>

<div class="tk-profiles-list-wrapper">
	<h1>foo</h1><h2 class="tk-profiles-heading">test<?php echo esc_html( $profiles_page_title ); ?></h2>
<?php
if ( $intro ) {
	?>
	<div class="tk-profiles-text-wrapper">
		<?php echo $intro; ?>
	</div>
	<?php
}

if ( 'by_cat' === $display ) {
	load_template( apply_filters( 'tk_profiles_template', 'loop', 'by-cat' ), false );
} else {
	load_template( apply_filters( 'tk_profiles_template', 'loop', 'all' ), false );
}

if ( $outro ) {
	?>
	<div class="tk-profiles-text-wrapper">
		<?php echo $outro; ?>
	</div>
	<?php
}
?>
</div>

<?php get_footer(); ?>
