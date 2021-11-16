<?php
/**
 * Rewrite rules related.
 *
 * @package tscptf
 */


/**
 * Filter permalink.
 */
add_filter( 'post_type_link', function( $link, $post ) {
	if ( ts_cptf_is_front( $post ) && get_option( 'rewrite_rules' ) ) {
		// This is front page.
		// Generate original pemalink.
		$struct = ts_cptf_post_type_struct( $post->post_type );
		$link   = apply_filters( 'tscptf_root_url', home_url( $struct ), $post );
	}
	return $link;
}, 10, 2 );

/**
 * Add query vars.
 *
 * @aram string[] $vars Query vars.
 */
add_filter( 'query_vars', function( $vars ) {
	$vars[] = 'root_of';
	return $vars;
} );

/**
 * Add rewrite rules.
 *
 * @param string[] $rules Rewrite rules.
 * @return string[]
 */
add_filter( 'rewrite_rules_array', function( $rules ) {
	$new_rules = [];
	foreach ( ts_cptf_post_types() as $post_type ) {
		$struct = ts_cptf_post_type_struct( $post_type );
		if ( empty( $struct ) ) {
			continue;
		}
		$new_rules[ '^' . $struct . '/page/(\d+)/?$' ] = sprintf( 'index.php?root_of=%s&page=$matches[1]', $post_type );
		$new_rules[ '^' . $struct . '/?$' ]            = 'index.php?root_of=' . $post_type;
	}
	if ( ! empty( $new_rules ) ) {
		$rules = array_merge( $new_rules, $rules );
	}
	return $rules;
}, 9999 );

/**
 * Hook for rewrite rules.
 *
 * @param WP_Query $wp_query Query object.
 */
add_action( 'pre_get_posts', function( $wp_query ) {
	$is_front = $wp_query->get( 'root_of' );
	if ( ! $is_front ) {
		return;
	}
	$wp_query->set( 'post_type', $is_front );
	$wp_query->set( 'posts_per_page', 1 );
	$wp_query->set( 'no_found_rows', true );
	$wp_query->set( 'ignore_sticky', true );
	$wp_query->set( 'meta_query', [
		[
			'key'   => '_tscptf_is_front',
			'value' => '1',
		],
	] );
	$wp_query->singular = true;
} );
