jQuery( function ( $ ) {
	'use strict';

	var $selection = $( '#ham-selection' );

	// Make table scroll with fixed header
	$selection.tableScroll( {
		header_class: 'widefat',
		footer_class: 'widefat',
		height: 400
	} );

	$selection.on( 'change', 'tbody tr input', function () {
		var $this = $( this ),
			$td = $this.parent(),
			$tr = $td.parent(),
			id = $tr.data( 'id' ),
			$sub = $tr.siblings( '[data-parent="' + id + '"]' ),
			index = $tr.find( 'td' ).index( $td ),
			$cb;

		if ( ! $sub.length ) {
			return;
		}

		$cb = $sub.find( 'td:eq(' + index + ') input' );
		$this.is( ':checked' ) ? $cb.attr( 'checked', 'checked' ) : $cb.removeAttr( 'checked' );
	} );
} );