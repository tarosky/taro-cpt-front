<?php
/**
 * Rewrite rules related.
 *
 * @package tscptf
 */


/**
 * Filter permalink.
 */
add_filter( 'post_type_link', function ( $link, $post ) {
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
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'root_of';
	return $vars;
} );

/**
 * Add rewrite rules.
 *
 * @param string[] $rules Rewrite rules.
 * @return string[]
 */
add_filter( 'rewrite_rules_array', function ( $rules ) {
	$new_rules = array();
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
add_action( 'pre_get_posts', function ( $wp_query ) {
	$is_front = $wp_query->get( 'root_of' );
	if ( ! $is_front ) {
		return;
	}
	// Search post type root.
	$sub_query = new WP_Query( [
		'post_type'      => $is_front,
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'ignore_sticky'  => true,
		'meta_query'     => [
			[
				'key'   => '_tscptf_is_front',
				'value' => '1',
			],
		],
	] );
	// Not found.
	if ( ! $sub_query->have_posts() ) {
		$wp_query->is_404 = true;
		return;
	}
	// Rebuild query vars for single page.
	$front_page = $sub_query->posts[0];
	$wp_query->set( 'root_of', '' );
	$wp_query->set( 'p', $front_page->ID );
	$wp_query->set( 'post_type', $is_front );
	// Set post type object.
	$wp_query->queried_object    = $front_page;
	$wp_query->queried_object_id = $front_page->ID;
	// Set flags.
	$wp_query->is_singular = true;
	$wp_query->is_single   = true;
	$wp_query->is_home     = false;
} );
