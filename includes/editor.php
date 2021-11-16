<?php
/**
 * Editor related functions.
 *
 * @packge tscptf
 */

/**
 * Display post status.
 *
 * @param string[] $states States.
 * @param WP_Post  $post   Post object.
 */
add_filter( 'display_post_states', function( $states, $post ) {
	if ( ! ts_cptf_is_front( $post ) ) {
		return $states;
	}
	$states['is-front'] = __( 'Front Page', 'tscptf' );
	return $states;
}, 10, 2 );

/**
 * Save pst dat.
 */
add_action( 'save_post', function( $post_id, $post ) {
	if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_tscptfnonce' ), 'tscptf_update' ) ) {
		return;
	}
	if ( '1' === filter_input( INPUT_POST, 'tscptf-is-front' ) ) {
		update_post_meta( $post_id, '_tscptf_is_front', 1 );
	} else {
		delete_post_meta( $post_id, '_tscptf_is_front' );
	}

}, 10, 2 );

/**
 * Register meta box.
 *
 * @param string $post_type Post type.
 */
add_action( 'add_meta_boxes', function( $post_type ) {
	if ( ! ts_cptf_available( $post_type ) ) {
		return;
	}
	add_meta_box( 'tscpf-is-front', __( 'Front Page Setting', 'tscptf' ), function( WP_Post $post ) {
		$front = ts_cptf_get_front_page( $post->post_type );
		if ( $front && $front->ID !== $post->ID ) {
			// Other page is front.
			printf(
				'<p class="description">%s: <a href="%s" target="_blank" rel="noopener noreferrer">%s</a><br />%s</p>',
				esc_html__( 'Current Front Page', 'tscptf' ),
				get_edit_post_link( $front->ID ),
				esc_html( get_the_title( $front ) ),
				esc_html__( 'To change front page, deprive of current front page first.', 'tscptf' )
			);
		} else {
			wp_nonce_field( 'tscptf_update', '_tscptfnonce', false );
			$is_front = $front ? 1 : 0;
			foreach ( [ __( 'Not a Front Page', 'tscptf' ), __( 'Set as a Front Page', 'tscptf' ) ] as $index => $label ) {
				printf(
					'<p><label><input type="radio" value="%s" name="tscptf-is-front" %s /> %s</label></p>',
					esc_attr( $index ),
					checked( $index, $is_front, false ),
					esc_html( $label )
				);
			}
		}
	}, $post_type, 'side' );
} );
