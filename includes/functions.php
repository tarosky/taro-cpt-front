<?php
/**
 * Functions
 *
 * @package tscptf
 */


/**
 * Detect if post type is ready for front page.
 *
 * @param string $post_type Post type.
 * @return bool
 */
function ts_cptf_available( $post_type ) {
	return in_array( $post_type, ts_cptf_post_types(), true );
}

/**
 * Return post types.
 *
 * @return string[]
 */
function ts_cptf_post_types() {
	return (array) get_option( 'ts-cptf-post-types', [] );
}

/**
 * Detect if post is front page.
 *
 * @param int|null|WP_Post $post Post object.
 * @return bool
 */
function ts_cptf_is_front( $post = null ) {
	$post = get_post( $post );
	if ( ! ts_cptf_available( $post->post_type ) ) {
		return false;
	}
	return '1' === get_post_meta( $post->ID, '_tscptf_is_front', true );
}

/**
 * Get front page.
 *
 * @param string $post_type Post type.
 * @return WP_Post|null
 */
function ts_cptf_get_front_page( $post_type ) {
	$query = new WP_Query( [
		'post_type'      => $post_type,
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'no_fround_rows' => true,
		'ignore_sticky'  => true,
		'meta_query'     => [
			[
				'key'   => '_tscptf_is_front',
				'value' => '1',
			],
		],
	] );
	foreach ( $query->posts as $post ) {
		return $post;
	}
	return null;
}

/**
 * Get post type struct.
 *
 * @param string $post_type Post type.
 * @return string
 */
function ts_cptf_post_type_struct( $post_type ) {
	global $wp_rewrite;
	$post_type_obj = get_post_type_object( $post_type );
	if ( ! $post_type_obj ) {
		return [];
	}
	$struct = [ trim( $post_type_obj->rewrite['slug'], '/' ) ];
	if ( $post_type_obj->rewrite['with_front'] && ( '/' !== $wp_rewrite->front ) ) {
		array_unshift( $struct, trim( $wp_rewrite->front, '/' ) );
	}
	return implode( '/', $struct );
}
