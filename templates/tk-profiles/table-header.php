<?php
/**
 * Output the table header
 *
 * @package TK_Profiles
 */

// Get table fields.
$table_fields = apply_filters( 'tk_profile_table_fields', '' );

if ( is_array( $table_fields ) && count( $table_fields ) ) {
	printf( '<table class="table table-profiles table-stripe table-bordered table-hover tablesaw tablesaw-stack columns-%d" data-tablesaw-mode="stack" data-tablesaw-sortable><thead><tr>', count( $table_fields ) );
	foreach ( $table_fields as $field ) {
		switch ( $field['value'] ) {
			// Image column has no text in header row.
			case 'featured_image':
				print( '<th scope="col" class="profile-image"></th>' );
				break;
			// Shorten Full name to Name.
			case 'post_title':
				print( '<th scope="col">Name</th>' );
				break;
			// Make first name and last name columns sortable.
			case 'tk_profiles_first_name':
			case 'tk_profiles_last_name':
				printf( '<th scope="col" class="name-field" data-tablesaw-sortable-col>%s</th>', esc_html( $field['label'] ) );
				break;
			case 'tk_profiles_research_area':
				printf( '<th scope="col" class="research-area">%s</th>', esc_html( $field['label'] ) );
				break;
			default:
				printf( '<th scope="col">%s</th>', esc_html( $field['label'] ) );
				break;
		}
	}
	print( '</tr></thead><tbody>' );
}
