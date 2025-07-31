<?php
/**
 * Setting screen.
 */

/**
 * Register setting fields.
 */
add_action( 'admin_init', function () {
	// If doing AJAX, do nothing.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	// Register fields.
	add_settings_section( 'ts-cptf-section', __( 'Front Page for Custom Post Type', 'tscptf' ), function () {}, 'reading' );
	add_settings_field( 'ts-cptf-post-types', __( 'Post Types', 'tscptf' ), function () {
		$post_types = get_post_types( array(
			'public'      => true,
			'has_archive' => false,
			'_builtin'    => false,
		), OBJECT );
		if ( empty( $post_types ) ) {
			printf( '<p class="description">%s</p>', esc_html__( 'No custom post types available.', 'tscptf' ) );
			printf( '<p class="description">%s</p>', esc_html__( 'Note that registered custom post type\'s public parameter must be true, and has_archive parameter must be false, to be able to set this option.', 'tscptf' ) );
		} else {
			foreach ( $post_types as $post_type ) {
				printf(
					'<label style="display: inline-block; margin: 0 1em 1em 0;"><input type="checkbox" name="ts-cptf-post-types[]" value="%s"%s /> %s</label>',
					esc_attr( $post_type->name ),
					checked( ts_cptf_available( $post_type->name ), true, false ),
					esc_html( $post_type->label )
				);
			}
		}
	}, 'reading', 'ts-cptf-section' );
	register_setting( 'reading', 'ts-cptf-post-types' );
} );

/**
 * Flush permalinks on Save Changes.
 */
add_action('update_option_ts-cptf-post-types', function () {
	add_action('shutdown', function () {
		flush_rewrite_rules();
	});
});
