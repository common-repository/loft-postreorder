( function( $ ) {
	const $list = $( '.loft-post-reorder-list' );
	const $wrapper = $( '.loft-post-reorder-list-wrapper' );
	if ( $list.length ) {
		$list.sortable().disableSelection();

		$( 'select[name="loft_post_reorder_type"]' ).on( 'change', function() {
			const val = $( this ).val();
			if ( 'manually' == val ) {
				$wrapper.removeClass( 'hide' );
			} else {
				$wrapper.addClass( 'hide' );
			}
		} );

		$( '.loft-post-reset-custom-order' ).on( 'click', function( e ) {
			e.preventDefault();
			$( this ).after( 
				$( '<input>', { 'type': 'hidden', 'name': 'loft-post-reset-custom-orders', 'value': 'reset' } ) 
			);
			$( '.edit-tag-actions [type="submit"]' ).trigger( 'click' );
		} );
	}

} ) ( jQuery );