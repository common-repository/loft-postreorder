<?php
/**
* Get taxonomy types supported
*/
function loft_post_reorder_taxonomy_supported() {
	return apply_filters( 
		'loftocean/category_reorder/supported_taxonomy', 
		array( 'category' ) 
	);
}
/**
* Update post order value
* @param id post id
* @param meta prefix
*/
function loft_post_reorder_update_post_order( $pid, $prefix ) {
	$cats = wp_get_post_categories( $pid, array(
		'fields' => 'ids',
		'hide_empty' => true
	) );
	if ( ! is_wp_error( $cats ) ) {
		foreach ( $cats as $cid ) {
			$ids = get_ancestors( $cid, 'category' );
			if ( empty( $ids ) ) {
				$ids = array( $cid );
			} else {
				array_push( $ids, $cid );
			}
			foreach ( $ids as $id ) {
				$meta = get_post_meta( $pid, $prefix . $id, true );
				if ( empty( $meta ) ) {
					update_post_meta( $pid, $prefix . $id, 1 );
				}
			}
		}
	}
}