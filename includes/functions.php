<?php
/**
 * Functions
 *
 * @package tscptf
 */


/**
 *
 *
 * @param string $post_type Post type.
 * @return bool
 */
function ts_cptf_available( $post_type ) {
	return in_array( $post_type, get_option( 'ts-cptf-post-types', [] ), true );
}
