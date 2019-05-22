<?php
/**
 * Outputs a table row with profile information in it
 * must be used within a loop with the $post variable set up
 *
 * @package TK_Profiles
 */

// Link to profile page.
$profile_link = apply_filters( 'tk_profile_url', '', get_the_id() );

// Get table fields to show.
$table_fields = apply_filters( 'tk_profile_table_fields', '' );

if ( is_array( $table_fields ) && count( $table_fields ) ) {
	print( '<tr>' );
	foreach ( $table_fields as $field ) {
		switch ( $field['value'] ) {
			// Image column has no text in header row.
			case 'featured_image':
				print( '<td class="profile_image">' );
				if ( has_post_thumbnail() ) {
					?>
			<div class="rs-img img-avatar img-avatar-sm" style="background-image: url('<?php the_post_thumbnail_url( 'small' ); ?>')">
				<a href="<?php echo esc_url( $profile_link ); ?>"><img src="<?php the_post_thumbnail_url( 'small' ); ?>" alt="<?php the_title_attribute(); ?>"></a>
			</div>
					<?php
				} else {
					?>
			<div class="rs-img img-avatar img-avatar-sm" style="background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAIAAAD/gAIDAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA95JREFUeNrsnE9r2mAcx9PVdjKhuoJuUm0tG3qYCPXQMpg9DNqbtw122EvYO9h1ewd7CYXtsJvHQg8VNuyhA7GHSUfrZimz0FohoFsP+y5hmdroXDR5nuj3RymJMTF8+P35Pr88eaY+vH2l0AazG0RAWIRFWIRFWIRFBIRFWITVZb7bd/AnJyyPRLcy640kM+H4KrZPy3vVUv7qR5OwTGw+Eo+tbN70+fVdIAsup74UcufVMmH9NQCKpTfnF+LddzbjTTx62qhVDgu5lnpJWEo4sYrQA5deX5gLLaWzLxCSCEzhUSkMFrL4vbWsLzBQLgdQRCVcDI42cbBi6Q09kf9XtD54/Pz8pIxEJsrFBMBC1UPatpy5BQajAFhzoUVkbsun7+feiMr3AkSpL3B3uOoZmCAFb4gpy5WBsBw63WWwbg3nGgOqjXGAhVLYR4IyZ3W61dB+ITAMnZYOLbWOsYviTnMIFgYr3tGFTzS5rm801frZUXHcYIWWUxgSj/yyGCo6CYttZTd0HQyDa9SOipCasZUN7B7sbOE/xszGIXglolgGWOI9CwM9FDhDPaEC+P8ELA5hW2D5k86z2jOaBi4QSWaMQ+3b9KxuYSFzzhIPay60aIhybITa0hMOMcF3wVpqV+fBDlhL9Cy3GmERlktzlp53pme9dlwcl9Wv78wjMtth6VrcJoOU1a//8d3rcauGGL4cFnIjuZSFJ48ugwVZMKqxixBV4bTOkk06sRqOkYJH5dL7MIY9fPZS0Zoz7UUtmlzHKLrXl+lZ9Kzeyd60K2/019tH0aNt4bsPVq+uvGkDy6YWPsOQsAiLsAiLRliERViERVjWTZ7H9C6AJcPoj2FIWIQluU3Zt8pRcDnlWGulUavUjop2T5m00bOQsB1rQuGHHKgPDEPCsscEtJVb6uUwyWWET2pdAav+rbRrbQ7E8adtbabgxMAyUrKFs6ZnvOMZhpe1ilIy+bw53CxbSIRG7av5z7kRFrzG7/iTK7/2o0Bm31wtj0333Wf++pAv3PxD6JYUl8GyXCVPy3vqxXdFW5oGVc8jNEPJC0t/TSeazOhv8MA7Puffx9IbAl/ylVSUwqdAKpF5YsQXNrB7vL8tyZI9EsGC7AonVruCDrv4EIcIq8MuTsrXl4T6nbkW4jhEWB3WZ3kZqRZnkwIWMpRpbrr62ZRqDqoUsCCdTJXXmfYaK2F1NxKuv0ePXWiuoEywZNFZ99ey1VL+YGdLX5enpdYRgPiQotTcMEKKKBk9ecn5wNUj2w3J/FyabWXCIizCIizCohEWYREWYREWYdEIi7AIi7DcZL8EGAAgHTQ4YUnwNQAAAABJRU5ErkJggg==)">
				<a href="<?php echo esc_url( $profile_link ); ?>"></a>
			</div>
					<?php
				}
				print( '</td>' );
				break;
			case 'post_title':
				$icon = ( get_field( 'tk_profiles_external_link_flag' ) ) ? '<span class="tk-icon-external" aria-hidden="true"></span>' : '';
				printf( '<td class="full_name"><a href="%s">%s%s</a></td>', esc_url( $profile_link ), esc_attr( get_the_title() ), $icon );
				break;
			case 'tk_profiles_email':
				printf( '<td class="profile_email"><a href="mailto:%1$s">%1$s</a></td>', get_field( 'tk_profiles_email' ) );
				break;
			default:
				printf( '<td class="%s">%s</td>', esc_attr( $field['value'] ), get_field( $field['value'] ) );
				break;
		}
	}
	print( '</tr>' );
}
