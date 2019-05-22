<?php
/**
 * Profiles shortcodes
 *
 * @package TK_Profiles
 */

if ( ! class_exists( 'TK_Profiles_Shortcodes' ) ) {
	/**
	 * Class to provide shortcodes for use with the plugin
	 */
	class TK_Profiles_Shortcodes {
		/**
		 * Constructor - register all hooks with WordPress API
		 */
		public function __construct() {
			/**
			 * Shortcode for single profile
			 */
			add_shortcode( 'tk_profile', array( $this, 'single_profile' ) );

			/**
			 * Shortcode for multiple profiles
			 */
			add_shortcode( 'tk_profiles', array( $this, 'multiple_profiles' ) );
		}

		/**
		 * Returns content for a single profile (in a card)
		 *
		 * @param array  $atts - attribues passed to shortcode.
		 * @param string $content - content between shortcode tags.
		 */
		public function single_profile( $atts, $content ) {
			$options = shortcode_atts( array(
				'id' => '',
			), $atts );
			$output  = '';
			if ( ! empty( $options['id'] ) ) {
				// Use these in new WP_Query (template expects a loop).
				$profile_query = new WP_Query( array(
					'p'         => $options['id'],
					'post_type' => 'tk_profiles',
				) );
				if ( $profile_query->have_posts() ) {
					while ( $profile_query->have_posts() ) {
						$profile_query->the_post();
						ob_start();
						load_template( apply_filters( 'tk_profiles_template', 'cards', 'single' ), false );
						$output .= ob_get_clean();
					}
				} else {
					$output .= '<p>No Profile found with ID: ' . $options['id'] . '</p>';
				}
				wp_reset_postdata();
			}
			return $output;
		}

		/**
		 * Returns content for multiple profiles (in stacked cards)
		 *
		 * @param array  $atts - attribues passed to shortcode.
		 * @param string $content - content between shortcode tags.
		 */
		public function multiple_profiles( $atts, $content ) {
			$options       = shortcode_atts( array(
				'ids'       => '',
				'category'  => '',
				'format'    => 'cards',
				'fields'    => '',
				'orderby'   => '',
				'separator' => ', ',
				'wrap'      => 'p',
			), $atts );
			$output        = '';
			$profile_query = false;
			$query_args    = array();
			if ( ! empty( $options['ids'] ) ) {
				$idarray = apply_filters( 'csv_to_int', $options['ids'] );
				if ( count( $idarray ) ) {
					$query_args = array(
						'post_type' => 'tk_profiles',
						'post__in'  => $idarray,
						'nopaging'  => true,
					);
				}
			} elseif ( ! empty( $options['category'] ) ) {
				$cat = apply_filters( 'csv_to_str', $options['category'] );
				if ( count( $cat ) ) {
					$query_args = array(
						'post_type' => 'tk_profiles',
						'tax_query' => array(
							array(
								'taxonomy' => 'tk_profile_category',
								'field'    => 'slug',
								'terms'    => $cat,
							),
						),
						'nopaging'  => true,
					);
				}
			}
			if ( count( $query_args ) ) {
				$query_args['orderby'] = 'menu_order';
				$query_args['order']   = 'ASC';
				if ( ! empty( $options['orderby'] ) && 'surname' === $options['orderby'] ) {
					$query_args['meta_key'] = 'tk_profiles_last_name';
					$query_args['orderby']  = 'meta_value';
				}
				$profile_query = new WP_Query( $query_args );
			}
			// If table fields are specified, validate and add a filter.
			if ( ! empty( $options['fields'] ) ) {
				$fields = apply_filters( 'csv_to_str', $options['fields'] );
				if ( count( $fields ) ) {
					add_filter( 'tk_profiles_get_table_fields', function( $f ) use ( $fields ) {
						return $fields;
					} );
				}
			}
			if ( $profile_query && $profile_query->have_posts() ) {
				$numberposts = $profile_query->found_posts;
				/**
				 * Collect HTML for individual profiles in an array
				 * this is so we can output a plain separated list easily
				 */
				$profile_html = array();
				while ( $profile_query->have_posts() ) {
					$profile_query->the_post();
					// Collect output in buffer.
					ob_start();
					switch ( $options['format'] ) {
						case 'table':
							load_template( apply_filters( 'tk_profiles_template', 'table', 'row' ), false );
							break;
						case 'list':
							printf( '<a href="%s">%s</a>', apply_filters( 'tk_profle_url', '', get_the_id() ), esc_html( get_the_title() ) );
							break;
						default:

							if ( $numberposts > 1 ) {
								load_template( apply_filters( 'tk_profiles_template', 'cards', 'row' ), false );
							} else {
								load_template( apply_filters( 'tk_profiles_template', 'cards', 'single' ), false );
							}
							break;
					}
					$profile_html[] = ob_get_clean();
				}
				if ( 'list' === $options['format'] ) {
					if ( ! empty( $options['wrap'] ) ) {
						$output .= sprintf( '<%1$s>%2$s</%1$s>', $options['wrap'], implode( $options['separator'], $profile_html ) );
					} else {
						$output .= implode( $options['separator'], $profile_html );
					}
				} elseif ( 'table' === $options['format'] ) {
					ob_start();
					load_template( apply_filters( 'tk_profiles_template', 'table', 'header' ), false );
					print( implode( '', $profile_html ) );
					load_template( apply_filters( 'tk_profiles_template', 'table', 'footer' ), false );
					$output .= ob_get_clean();
				} else {
					ob_start();
					load_template( apply_filters( 'tk_profiles_template', 'cards', 'header' ), false );
					print( implode( '', $profile_html ) );
					load_template( apply_filters( 'tk_profiles_template', 'cards', 'footer' ), false );
					$output .= ob_get_clean();
				}
				wp_reset_postdata();
			}
			return $output;
		}
	}
	new TK_Profiles_Shortcodes();
}
