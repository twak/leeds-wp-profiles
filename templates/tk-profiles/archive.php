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
	<div class="wrapper-pd-xs">
		<ul class="breadcrumb">
			<li><a href="<?php echo esc_url( site_url() ); ?>">Home</a></li>
			<li><?php echo esc_html( $profiles_page_title ); ?></li>
		</ul>
	</div>
	
	<?php
}
?>

<div class="wrapper-md wrapper-pd">
	<h1 class="heading-underline"><?php echo esc_html( $profiles_page_title ); ?></h1>
<?php
if ( $intro ) {
	?>
	<div class="wrapper-md">
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
	<div class="wrapper-md">
		<?php echo $outro; ?>
	</div>
	<?php
}
?>

</div>

<?php get_footer(); ?>
